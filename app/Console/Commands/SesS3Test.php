<?php

namespace App\Console\Commands;

use Aws\Crypto\KmsMaterialsProviderV2;
use Aws\Kms\KmsClient;
use Aws\S3\Crypto\S3EncryptionClientV2;
use Aws\S3\S3Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SesS3Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ses-s3-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $s3 = Storage::disk('s3');
        $files = collect($s3->files('inbound-email'))
            ->filter(fn (string $file) => $file !== 'inbound-email/AMAZON_SES_SETUP_NOTIFICATION');

        // This is where we would dispatch a Unique job per file to process the email then delete it

        $files->each(function (string $file) {
            $encryptionClient = new S3EncryptionClientV2(
                new S3Client([
                    'credentials' => [
                        'key' => config('filesystems.disks.s3.key'),
                        'secret' => config('filesystems.disks.s3.secret'),
                    ],
                    'region' => 'us-west-2',
                ])
            );

            // TODO: Set the KMS key ID, this is best practice and we will need to store it as an ENV variable
            $kmsKeyId = config('services.kms.ses_s3_key_id');
            $materialsProvider = new KmsMaterialsProviderV2(
                new KmsClient([
                    'credentials' => [
                        'key' => config('filesystems.disks.s3.key'),
                        'secret' => config('filesystems.disks.s3.secret'),
                    ],
                    'region' => 'us-west-2',
                ]),
                $kmsKeyId
            );

            $bucket = config('filesystems.disks.s3.bucket');
            $key = config('filesystems.disks.s3.root') . '/' . $file;
            $cipherOptions = [
                'Cipher' => 'gcm',
                'KeySize' => 256,
            ];

            // Needed to suppress warnings from the SDK. SES encrypts using V1 so we need @SecurityProfile to be V2_AND_LEGACY
            // But the SDK throws a warning when using V2_AND_LEGACY
            $errorReportingLevel = error_reporting();
            error_reporting(E_ERROR & ~E_WARNING);

            $result = $encryptionClient->getObject([
                '@KmsAllowDecryptWithAnyCmk' => false,
                '@SecurityProfile' => 'V2_AND_LEGACY',
                '@MaterialsProvider' => $materialsProvider,
                '@CipherOptions' => $cipherOptions,
                'Bucket' => $bucket,
                'Key' => $key,
            ]);

            error_reporting($errorReportingLevel);

            // Get the content as a string
            $content = $result['Body']->getContents();

            // Now you can work with the actual content
            $this->info($content);
        });
    }
}
