<?php

namespace AdvisingApp\Engagement\Jobs;

use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\InboundEmailsUpdates;
use App\Models\Tenant;
use Aws\Crypto\KmsMaterialsProviderV2;
use Aws\Kms\KmsClient;
use Aws\S3\Crypto\S3EncryptionClientV2;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpMimeMailParser\Attachment;
use PhpMimeMailParser\Parser;
use Spatie\Multitenancy\Jobs\NotTenantAware;

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

        // TODO: Probably wrap this whole thing in a try/catch to report any errors

        $content = $result['Body']->getContents();

        $parser = (new Parser())
            ->setText($content);

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

        // TODO: Move the email to a failed folder and throw a custom exceptions saying that a Tenant match could not be found.
        throw_if($matchedTenants->isEmpty(), new Exception('No matching tenants found'));

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

                if ($prospects->isEmpty()) {
                    // TODO: Move the email to a failed folder and throw a custom exceptions saying that no matching Educatable record could be found.
                }

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

                // Throw an error if file failed to delete?
                Storage::disk('s3-inbound-email')->delete($this->emailFilePath);
            });
        });
    }
}
