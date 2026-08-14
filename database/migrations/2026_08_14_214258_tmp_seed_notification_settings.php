<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Features\NotificationSettingsFeature;
use App\Models\NotificationSetting;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Enums\Color;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
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
