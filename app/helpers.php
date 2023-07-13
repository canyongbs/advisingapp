<?php

use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;

if (! function_exists('get_application_models')) {
    function get_application_models(): Collection
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
                    $reflection = new \ReflectionClass($class);
                    $isModel = $reflection->isSubclassOf(Model::class) &&
                    ! $reflection->isAbstract();
                }

                return $isModel;
            });
    }
}
