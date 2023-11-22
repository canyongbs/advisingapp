<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Actions\Finders;

use ReflectionClass;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

class ApplicationModels
{
    public function all(): Collection
    {
        return collect(File::allFiles(app_path()))
            ->map(function ($item) {
                $path = $item->getRelativePathName();
                $class = sprintf(
                    '\%s%s',
                    Container::getInstance()->getNamespace(),
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\')
                );

                return $class;
            })
            ->filter(function ($class) {
                $isModel = false;

                if (class_exists($class)) {
                    $reflection = new ReflectionClass($class);
                    $isModel = ($reflection->isSubclassOf(Model::class) || $reflection->isSubclassOf(BaseModel::class)) &&
                    ! $reflection->isAbstract();
                }

                return $isModel;
            });
    }

    public function implementingPermissions(): Collection
    {
        return $this->all()->filter(function ($class) {
            $implementsPermissions = false;

            $reflection = new ReflectionClass($class);
            $parentClass = $reflection->getParentClass();

            if (in_array(DefinesPermissions::class, $reflection->getTraitNames()) || in_array(DefinesPermissions::class, $parentClass->getTraitNames())) {
                $implementsPermissions = true;
            }

            return $implementsPermissions;
        });
    }
}
