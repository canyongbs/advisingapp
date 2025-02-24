<?php

namespace AdvisingApp\Engagement\Jobs;

use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Exceptions\SesS3InboundSpamOrVirusDetected;
use AdvisingApp\Engagement\Exceptions\UnableToDetectAnyMatchingEducatablesFromSesS3EmailPayload;
use AdvisingApp\Engagement\Exceptions\UnableToDetectTenantFromSesS3EmailPayload;
use AdvisingApp\Engagement\Exceptions\UnableToRetrieveContentFromSesS3EmailPayload;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\InboundEmailsUpdates;
use App\Models\Tenant;
use Aws\Crypto\KmsMaterialsProviderV2;
use Aws\Kms\KmsClient;
use Aws\S3\Crypto\S3EncryptionClientV2;
use Aws\S3\S3Client;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpMimeMailParser\Attachment;
use PhpMimeMailParser\Parser;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Throwable;

class ProcessSesS3InboundEmail implements ShouldQueue, ShouldBeUnique, NotTenantAware
{
    use Queueable;

    // Unique for 30 minutes
    public $uniqueFor = 1800;

    public function __construct(
        protected string $emailFilePath
    ) {}

    public function uniqueId(): string
    {
        return $this->emailFilePath;
    }

    public function handle(): void
    {
        if (! InboundEmailsUpdates::active()) {
            return;
        }

        DB::beginTransaction();

        try {
            $encryptionClient = new S3EncryptionClientV2(
                new S3Client([
                    'credentials' => [
                        'key' => config('filesystems.disks.s3.key'),
                        'secret' => config('filesystems.disks.s3.secret'),
                    ],
                    'region' => config('filesystems.disks.s3.region'),
                ])
            );

            // Needed to suppress warnings from the SDK. SES encrypts using V1 so we need @SecurityProfile to be V2_AND_LEGACY
            // But the SDK throws a warning when using V2_AND_LEGACY
            $errorReportingLevel = error_reporting();
            error_reporting(E_ERROR & ~E_WARNING);

            $result = $encryptionClient->getObject([
                '@KmsAllowDecryptWithAnyCmk' => false,
                '@SecurityProfile' => 'V2_AND_LEGACY',
                '@MaterialsProvider' => new KmsMaterialsProviderV2(
                    new KmsClient([
                        'credentials' => [
                            'key' => config('filesystems.disks.s3.key'),
                            'secret' => config('filesystems.disks.s3.secret'),
                        ],
                        'region' => 'us-west-2',
                    ]),
                    config('services.kms.ses_s3_key_id')
                ),
                '@CipherOptions' => [
                    'Cipher' => 'gcm',
                    'KeySize' => 256,
                ],
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key' => config('filesystems.disks.s3-inbound-email.root') . '/' . $this->emailFilePath,
            ]);

            // Reset the error reporting level
            error_reporting($errorReportingLevel);

            try {
                $content = $result['Body']->getContents();
            } catch (Throwable $e) {
                throw new UnableToRetrieveContentFromSesS3EmailPayload($this->emailFilePath, $e);
            }

            throw_if(empty($content), new UnableToRetrieveContentFromSesS3EmailPayload($this->emailFilePath));

            $parser = (new Parser())
                ->setText($content);

            // In the future, this probably shouldn't result in a job failure. We should just log it and move on.
            throw_if(
                $parser->getHeader('X-SES-Spam-Verdict') !== 'PASS' || $parser->getHeader('X-SES-Virus-Verdict') !== 'PASS',
                new SesS3InboundSpamOrVirusDetected($this->emailFilePath, $parser->getHeader('X-SES-Spam-Verdict'), $parser->getHeader('X-SES-Virus-Verdict')),
            );

            $matchedTenants = collect($parser->getAddresses('to'))
                ->pluck('address')
                ->map(function (string $address) {
                    $localPart = filter_var($address, FILTER_VALIDATE_EMAIL) ? explode('@', $address)[0] : null;

                    if ($localPart === null) {
                        return null;
                    }

                    return Tenant::query()
                        ->where(
                            DB::raw('LOWER(domain)'),
                            'like',
                            strtolower("{$localPart}.%")
                        )
                        ->first();
                })
                ->filter();

            throw_if(
                $matchedTenants->isEmpty(),
                new UnableToDetectTenantFromSesS3EmailPayload($this->emailFilePath),
            );

            $sender = $parser->getAddresses('from')[0]['address'];

            $matchedTenants->each(function (Tenant $tenant) use ($parser, $content, $sender) {
                $tenant->execute(function () use ($parser, $content, $sender) {
                    $students = Student::query()
                        // This will need to be changed when we refactor email into a different table
                        ->where('email', $sender)
                        ->get();

                    if ($students->isNotEmpty()) {
                        $students->each(function (Student $student) use ($parser, $content) {
                            /** @var EngagementResponse $engagementResponse */
                            $engagementResponse = $student->engagementResponses()
                                ->create([
                                    'subject' => $parser->getHeader('subject'),
                                    'content' => $parser->getMessageBody('htmlEmbedded'),
                                    'sent_at' => $parser->getHeader('date'),
                                    'type' => EngagementResponseType::Email,
                                    'raw' => $content,
                                ]);

                            collect($parser->getAttachments())->each(function (Attachment $attachment) use ($engagementResponse) {
                                $engagementResponse->addMediaFromStream($attachment->getStream())
                                    ->toMediaCollection('attachments');
                            });
                        });

                        Storage::disk('s3-inbound-email')->delete($this->emailFilePath);

                        // If we found students and added records, we can stop here as Student records take final precedence over Prospect records.
                        return;
                    }

                    $prospects = Prospect::query()
                        // This will need to be changed when we refactor email into a different table
                        ->where('email', $sender)
                        ->get();

                    throw_if(
                        $prospects->isEmpty(),
                        new UnableToDetectAnyMatchingEducatablesFromSesS3EmailPayload($this->emailFilePath),
                    );

                    $prospects->each(function (Prospect $prospect) use ($parser, $content) {
                        /** @var EngagementResponse $engagementResponse */
                        $engagementResponse = $prospect->engagementResponses()
                            ->create([
                                'subject' => $parser->getHeader('subject'),
                                'content' => $parser->getMessageBody('htmlEmbedded'),
                                'sent_at' => $parser->getHeader('date'),
                                'type' => EngagementResponseType::Email,
                                'raw' => $content,
                            ]);

                        collect($parser->getAttachments())->each(function (Attachment $attachment) use ($engagementResponse) {
                            $engagementResponse->addMediaFromStream($attachment->getStream())
                                ->setName($attachment->getFilename())
                                ->setFileName($attachment->getFilename())
                                ->toMediaCollection('attachments');
                        });
                    });

                    Storage::disk('s3-inbound-email')->delete($this->emailFilePath);
                });
            });
        } catch (
            UnableToRetrieveContentFromSesS3EmailPayload | SesS3InboundSpamOrVirusDetected | UnableToDetectTenantFromSesS3EmailPayload | UnableToDetectAnyMatchingEducatablesFromSesS3EmailPayload $e) {
                DB::rollBack();

                // Instantly fail for this exception
                $this->fail($e);
            } catch (Throwable $e) {
                DB::rollBack();

                throw $e;
            }
    }

    public function failed(?Throwable $exception): void
    {
        if ($exception === null) {
            $this->moveFile('/failed');

            return;
        }

        match ($exception::class) {
            SesS3InboundSpamOrVirusDetected::class => $this->moveFile('/spam-or-virus-detected'),
            default => $this->moveFile('/failed'),
        };
    }

    protected function moveFile(string $destination): void
    {
        Storage::disk('s3-inbound-email')->move($this->emailFilePath, $destination . '/' . $this->emailFilePath);
    }
}
