<?php

namespace App\Console\Commands;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\Tenant;
use Aws\Crypto\KmsMaterialsProviderV2;
use Aws\Kms\KmsClient;
use Aws\S3\Crypto\S3EncryptionClientV2;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpMimeMailParser\Parser;

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
        $s3 = Storage::disk('s3-inbound-email');
        $files = collect($s3->files())
            ->filter(fn (string $file) => $file !== 'AMAZON_SES_SETUP_NOTIFICATION');

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
            $key = config('filesystems.disks.s3-inbound-email.root') . '/' . $file;
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

            $parser = new Parser();

            $parser->setText($content);

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

            $matchedTenants->each(function (Tenant $tenant) use ($parser, $sender) {
                $tenant->execute(function () use ($parser, $sender) {
                    $students = Student::query()
                        // This will need to be changed when we refactor email into a different table
                        ->where('email', $sender)
                        ->get();

                    if ($students->isNotEmpty()) {
                        $students->each(function (Student $student) use ($parser) {
                            // Add a record
                        });

                        // TODO: Delete the email

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

                    $prospects->each(function (Prospect $prospect) use ($parser) {
                        // Add a record
                    });
                });
            });
        });
    }
}
