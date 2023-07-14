<?php

namespace App\Actions\Finders;

use ReflectionClass;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\DefinesPermissions;

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
                    $isModel = $reflection->isSubclassOf(Model::class) &&
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

            if (in_array(DefinesPermissions::class, $reflection->getTraitNames())) {
                $implementsPermissions = true;
            }

            return $implementsPermissions;
        });
    }
}
