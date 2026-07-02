<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Engagement\Jobs;

use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Exceptions\SesS3InboundSpamOrVirusDetected;
use AdvisingApp\Engagement\Exceptions\UnableToDetectTenantFromSesS3EmailPayload;
use AdvisingApp\Engagement\Exceptions\UnableToRetrieveContentFromSesS3EmailPayload;
use AdvisingApp\Engagement\Models\UnmatchedInboundCommunication;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\Tenant;
use Aws\Crypto\KmsMaterialsProviderV3;
use Aws\Kms\KmsClient;
use Aws\S3\Crypto\S3EncryptionClientV3;
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
        DB::beginTransaction();

        try {
            $content = $this->getContent();

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
                            strtolower("{$localPart}.%") // @phpstan-ignore Common.noStrtolower
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
                        ->whereRelation('emailAddresses', 'address', $sender)
                        ->get();

                    if ($students->isNotEmpty()) {
                        $students->each(function (Student $student) use ($parser, $content) {
                            $engagementResponse = $student->engagementResponses()
                                ->create([
                                    'subject' => $parser->getHeader('subject'),
                                    'content' => $parser->getMessageBody('htmlEmbedded'),
                                    'sent_at' => $parser->getHeader('date'),
                                    'type' => EngagementResponseType::Email,
                                    'raw' => $content,
                                    'status' => EngagementResponseStatus::New,
                                ]);

                            collect($parser->getAttachments())->each(function (Attachment $attachment) use ($engagementResponse, $student) {
                                try {
                                    if (
                                        ($attachment->getContentDisposition() === 'inline')
                                        && filled(trim($contentId = $attachment->getContentID(), '<>'))
                                    ) {
                                        $media = $engagementResponse->addMediaFromStream($attachment->getStream())
                                            ->withCustomProperties(['cid' => trim($contentId, '<>')])
                                            ->setName($attachment->getFilename())
                                            ->setFileName($attachment->getFilename())
                                            ->toMediaCollection('inline_attachments');

                                        if (is_null($media->created_by_id)) {
                                            $media->createdBy()->associate($student);
                                            $media->saveQuietly();
                                        }

                                        return;
                                    }

                                    $media = $engagementResponse->addMediaFromStream($attachment->getStream())
                                        ->setName($attachment->getFilename())
                                        ->setFileName($attachment->getFilename())
                                        ->toMediaCollection('attachments');

                                    if (is_null($media->created_by_id)) {
                                        $media->createdBy()->associate($student);
                                        $media->saveQuietly();
                                    }
                                } catch (Throwable $throw) {
                                    report($throw);
                                }
                            });
                        });

                        Storage::disk('s3-inbound-email')->delete($this->emailFilePath);

                        DB::commit();

                        // If we found students and added records, we can stop here as Student records take final precedence over Prospect records.
                        return;
                    }

                    $prospects = Prospect::query()
                        ->whereRelation('emailAddresses', 'address', $sender)
                        ->get();

                    if ($prospects->isEmpty()) {
                        $unmatchedInboundCommunication = UnmatchedInboundCommunication::create([
                            'type' => EngagementResponseType::Email,
                            'subject' => $parser->getHeader('subject'),
                            'body' => $parser->getMessageBody('htmlEmbedded'),
                            'occurred_at' => $parser->getHeader('date'),
                            'sender' => $sender,
                        ]);

                        collect($parser->getAttachments())->each(function (Attachment $attachment) use ($unmatchedInboundCommunication) {
                            try {
                                if (
                                    ($attachment->getContentDisposition() === 'inline')
                                    && filled(trim($contentId = $attachment->getContentID(), '<>'))
                                ) {
                                    $unmatchedInboundCommunication->addMediaFromStream($attachment->getStream())
                                        ->withCustomProperties(['cid' => trim($contentId, '<>')])
                                        ->setName($attachment->getFilename())
                                        ->setFileName($attachment->getFilename())
                                        ->toMediaCollection('inline_attachments');

                                    return;
                                }

                                $unmatchedInboundCommunication->addMediaFromStream($attachment->getStream())
                                    ->setName($attachment->getFilename())
                                    ->setFileName($attachment->getFilename())
                                    ->toMediaCollection('attachments');
                            } catch (Throwable $throw) {
                                report($throw);
                            }
                        });

                        Storage::disk('s3-inbound-email')->delete($this->emailFilePath);

                        DB::commit();

                        return;
                    }

                    $prospects->each(function (Prospect $prospect) use ($parser, $content) {
                        $engagementResponse = $prospect->engagementResponses()
                            ->create([
                                'subject' => $parser->getHeader('subject'),
                                'content' => $parser->getMessageBody('htmlEmbedded'),
                                'sent_at' => $parser->getHeader('date'),
                                'type' => EngagementResponseType::Email,
                                'raw' => $content,
                                'status' => EngagementResponseStatus::New,
                            ]);

                        collect($parser->getAttachments())->each(function (Attachment $attachment) use ($engagementResponse, $prospect) {
                            try {
                                if (
                                    ($attachment->getContentDisposition() === 'inline')
                                    && filled(trim($contentId = $attachment->getContentID(), '<>'))
                                ) {
                                    $media = $engagementResponse->addMediaFromStream($attachment->getStream())
                                        ->withCustomProperties(['cid' => trim($contentId, '<>')])
                                        ->setName($attachment->getFilename())
                                        ->setFileName($attachment->getFilename())
                                        ->toMediaCollection('inline_attachments');

                                    if (is_null($media->created_by_id)) {
                                        $media->createdBy()->associate($prospect);
                                        $media->saveQuietly();
                                    }

                                    return;
                                }

                                $media = $engagementResponse->addMediaFromStream($attachment->getStream())
                                    ->setName($attachment->getFilename())
                                    ->setFileName($attachment->getFilename())
                                    ->toMediaCollection('attachments');

                                if (is_null($media->created_by_id)) {
                                    $media->createdBy()->associate($prospect);
                                    $media->saveQuietly();
                                }
                            } catch (Throwable $throw) {
                                report($throw);
                            }
                        });
                    });

                    Storage::disk('s3-inbound-email')->delete($this->emailFilePath);

                    DB::commit();
                });
            });
        } catch (
            UnableToRetrieveContentFromSesS3EmailPayload | SesS3InboundSpamOrVirusDetected | UnableToDetectTenantFromSesS3EmailPayload $exception
        ) {
            DB::rollBack();

            // Instantly fail for this exception
            $this->fail($exception);
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        if ($exception !== null && app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }

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

    protected function getContent(): string
    {
        $encryptionClient = new S3EncryptionClientV3(
            new S3Client([
                'credentials' => [
                    'key' => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
                'region' => config('filesystems.disks.s3.region'),
            ])
        );

        // Needed to suppress warnings from the SDK. SES encrypts using V1 so we need @SecurityProfile to be V3_AND_LEGACY
        // But the SDK throws a warning when using V3_AND_LEGACY
        $errorReportingLevel = error_reporting();
        error_reporting(E_ERROR & ~E_WARNING);

        try {
            $result = $encryptionClient->getObject([
                '@KmsAllowDecryptWithAnyCmk' => false,
                '@SecurityProfile' => 'V3_AND_LEGACY',
                '@MaterialsProvider' => new KmsMaterialsProviderV3(
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
                '@CommitmentPolicy' => 'FORBID_ENCRYPT_ALLOW_DECRYPT',
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key' => config('filesystems.disks.s3-inbound-email.root') . '/' . $this->emailFilePath,
            ]);
        } finally {
            // Reset the error reporting level
            error_reporting($errorReportingLevel);
        }

        try {
            // @phpstan-ignore method.nonObject
            $content = $result['Body']?->getContents();
        } catch (Throwable $exception) {
            throw new UnableToRetrieveContentFromSesS3EmailPayload($this->emailFilePath, $exception);
        }

        throw_if(empty($content), new UnableToRetrieveContentFromSesS3EmailPayload($this->emailFilePath));

        return (string) $content;
    }
}
