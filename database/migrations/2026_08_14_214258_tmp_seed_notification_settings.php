<?php

use App\Features\NotificationSettingsFeature;
use App\Models\NotificationSetting;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Enums\Color;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $notificationSetting = NotificationSetting::query()->oldest()->first();

            if (! $notificationSetting) {
                return;
            }

            $settings = app(NotificationSettings::class);

            $settings->name = $notificationSetting->name;
            $settings->from_name = $notificationSetting->from_name;
            $settings->description = $notificationSetting->description;
            $settings->primary_color = Color::tryFrom((string) $notificationSetting->primary_color);

            $settings->save();

            $logo = $notificationSetting->getFirstMedia('logo');

            $logo?->copy(NotificationSettings::getSettingsPropertyModel('notifications.logo'), 'logo');

            NotificationSettingsFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            NotificationSettingsFeature::deactivate();

            $settings = app(NotificationSettings::class);

            $settings->name = null;
            $settings->from_name = null;
            $settings->description = null;
            $settings->primary_color = null;

            $settings->save();

            NotificationSettings::getSettingsPropertyModel('notifications.logo')->clearMediaCollection('logo');
        });
    }
};
