<?php

namespace Assist\Authorization\Actions;

use Illuminate\Support\Facades\File;
use Assist\Authorization\Models\Role;

class CreateRoles
{
    public function handle(): void
    {
        collect(File::directories(config_path('roles')))->each(function ($path, $key) {
            collect(File::files($path))->each(function ($file, $key) use ($path) {
                $guardName = explode('roles' . DIRECTORY_SEPARATOR, $path)[1];
                $role = explode('.' . $file->getExtension(), $file->getFilename())[0];

                Role::firstOrCreate([
                    'name' => $role,
                    'guard_name' => $guardName,
                ]);
            });
        });
    }
}
