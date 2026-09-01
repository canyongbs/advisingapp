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

use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Tests\Fixtures\TestEmailSettingFromNameNotification;
use AdvisingApp\Theme\Settings\ThemeSettings;
use App\Features\ThemeLogoPublicDiskFeature;
use App\Models\User;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Enums\Color;
use Filament\Support\Colors\Color as FilamentColor;
use Illuminate\Support\Facades\Storage;

it('renders the notification mail with the configured `primary_color`', function () {
    $user = User::factory()->create();

    $expected = FilamentColor::convertToRgb(FilamentColor::all()[Color::Gray->value][600]);

    expect((string) (new TestEmailSettingFromNameNotification())->toMail($user)->render())
        ->not->toContain($expected);

    $settings = app(NotificationSettings::class);
    $settings->primary_color = Color::Gray;
    $settings->save();

    expect((string) (new TestEmailSettingFromNameNotification())->toMail($user)->render())
        ->toContain($expected);
});

it('applies notification settings when instantiated directly', function () {
    $settings = app(NotificationSettings::class);
    $settings->from_name = 'Advising Team';
    $settings->save();

    $mailMessage = new MailMessage();

    expect($mailMessage->viewData['settings'])->toBe($settings)
        ->and($mailMessage->from[1])->toBe('Advising Team');
});

it('renders the active theme logo fallback with a public media URL', function () {
    Storage::fake('s3-public');

    $themeSettings = app(ThemeSettings::class);
    $themeSettings->is_logo_active = true;
    $themeSettings->save();

    $themeLogo = ThemeSettings::getSettingsPropertyModel('theme.is_logo_active');
    $themeLogo
        ->addMediaFromString('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"></svg>')
        ->usingFileName('logo.svg')
        ->toMediaCollection('logo', 's3-public');

    $logoUrl = $themeLogo->getFirstMediaUrl('logo');

    expect(ThemeLogoPublicDiskFeature::active())->toBeTrue()
        ->and((string) view('vendor.mail.html.header', ['url' => url('/')])->render())
        ->toContain($logoUrl)
        ->not->toContain('X-Amz-Expires');
});
