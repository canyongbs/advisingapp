<?php

use InterNACHI\Modular\Support\ModuleConfig;
use InterNACHI\Modular\Support\ModuleRegistry;

arch('All Core Settings classes should have defaults for all properties')
    ->expect('App\Settings')
    ->toHaveDefaultsForAllProperties();

app(ModuleRegistry::class, [
    'modules_path' => 'app-modules',
    'cache_path' => 'cache/modules.php',
])->modules()->each(function (ModuleConfig $module) {
    arch("All {$module->name} Settings classes should have defaults for all properties")
        ->expect($module->namespace() . 'Settings')
        ->toHaveDefaultsForAllProperties();
});
