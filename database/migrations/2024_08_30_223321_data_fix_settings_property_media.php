<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $themeLogo = DB::table('settings')->where('group', 'theme')
            ->where('name', 'is_logo_active')
            ->first();

        if ($themeLogo) {
            DB::table('media')
                ->where('model_type', 'settings_property')
                ->where('model_id', $themeLogo->id)
                ->update(['model_type' => 'theme_settings_property']);
        }

        $themeFavicon = DB::table('settings')->where('group', 'theme')
            ->where('name', 'is_favicon_active')
            ->first();

        if ($themeFavicon) {
            DB::table('media')
                ->where('model_type', 'settings_property')
                ->where('model_id', $themeFavicon->id)
                ->update(['model_type' => 'theme_settings_property']);
        }

        $portalLogo = DB::table('settings')->where('group', 'portal')
            ->where('name', 'logo')
            ->first();

        if ($portalLogo) {
            DB::table('media')
                ->where('model_type', 'settings_property')
                ->where('model_id', $portalLogo->id)
                ->update(['model_type' => 'portal_settings_property']);
        }

        $portalFavicon = DB::table('settings')->where('group', 'portal')
            ->where('name', 'favicon')
            ->first();

        if ($portalFavicon) {
            DB::table('media')
                ->where('model_type', 'settings_property')
                ->where('model_id', $portalFavicon->id)
                ->update(['model_type' => 'portal_settings_property']);
        }
    }

    public function down(): void
    {
        DB::table('media')
            ->whereIn('model_type', [
                'theme_settings_property',
                'portal_settings_property',
            ])
            ->update(['model_type' => 'settings_property']);
    }
};
