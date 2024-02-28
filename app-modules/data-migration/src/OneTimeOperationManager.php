<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\DataMigration;

use SplFileInfo;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use AdvisingApp\DataMigration\Models\Operation;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

class OneTimeOperationManager
{
    /**
     * @return Collection<OneTimeOperationFile>
     */
    public static function getUnprocessedOperationFiles(): Collection
    {
        $operationFiles = self::getUnprocessedFiles();

        return $operationFiles->map(fn (SplFileInfo $file) => OneTimeOperationFile::make($file));
    }

    /**
     * @return Collection<SplFileInfo>
     */
    public static function getAllOperationFiles(): Collection
    {
        $operationFiles = self::getAllFiles();

        return $operationFiles->map(fn (SplFileInfo $file) => OneTimeOperationFile::make($file));
    }

    /**
     * @return Collection<SplFileInfo>
     */
    public static function getUnprocessedFiles(): Collection
    {
        $allOperationFiles = self::getAllFiles();

        return $allOperationFiles->filter(function (SplFileInfo $operationFilepath) {
            $operation = self::getOperationNameFromFilename($operationFilepath->getBasename());

            return Operation::whereName($operation)->doesntExist();
        });
    }

    /**
     * @return Collection<SplFileInfo>
     */
    public static function getAllFiles(): Collection
    {
        try {
            return collect(File::files(self::getDirectoryPath()));
        } catch (DirectoryNotFoundException $e) {
            return collect();
        }
    }

    public static function getClassObjectByName(string $operationName): OneTimeOperation
    {
        $filepath = self::pathToFileByName($operationName);

        return File::getRequire($filepath);
    }

    public static function getModelByName(string $operationName): ?Operation
    {
        return Operation::whereName($operationName)->first();
    }

    public static function getOperationFileByModel(Operation $operationModel): OneTimeOperationFile
    {
        $filepath = self::pathToFileByName($operationModel->name);

        throw_unless(File::exists($filepath), FileNotFoundException::class);

        return OneTimeOperationFile::make(new SplFileInfo($filepath));
    }

    /**
     * @throws \Throwable
     */
    public static function getOperationFileByName(string $operationName): OneTimeOperationFile
    {
        $filepath = self::pathToFileByName($operationName);

        throw_unless(File::exists($filepath), FileNotFoundException::class, sprintf('File %s does not exist', self::buildFilename($operationName)));

        return OneTimeOperationFile::make(new SplFileInfo($filepath));
    }

    public static function pathToFileByName(string $operationName): string
    {
        return self::getDirectoryPath() . self::buildFilename($operationName);
    }

    public static function fileExistsByName(string $operationName): bool
    {
        return File::exists(self::pathToFileByName($operationName));
    }

    public static function getDirectoryName(): string
    {
        return Config::get('one-time-operations.directory');
    }

    public static function getDirectoryPath(): string
    {
        return App::basePath(Str::of(self::getDirectoryName())->rtrim('/')) . DIRECTORY_SEPARATOR;
    }

    public static function getOperationNameFromFilename(string $filename): string
    {
        return str($filename)->remove('.php');
    }

    public static function getTableName(): string
    {
        return Config::get('one-time-operations.table', 'operations');
    }

    public static function buildFilename($operationName): string
    {
        return $operationName . '.php';
    }
}
