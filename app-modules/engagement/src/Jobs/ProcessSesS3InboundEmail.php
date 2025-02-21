<?php

namespace AdvisingApp\Engagement\Jobs;

use App\Features\InboundEmailsUpdates;
use Aws\Crypto\KmsMaterialsProviderV2;
use Aws\Kms\KmsClient;
use Aws\S3\Crypto\S3EncryptionClientV2;
use Aws\S3\S3Client;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
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

        $arrayHeaderTo = $parser->getAddresses('to');

        // $text = $parser->getMessageBody('htmlEmbedded');

        var_dump($parser->getHeaders());
    }
}
