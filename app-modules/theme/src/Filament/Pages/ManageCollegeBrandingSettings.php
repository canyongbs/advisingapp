<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Theme\Filament\Pages;

use App\Features\SettingsPermissions;
use App\Filament\Clusters\DisplaySettings;
use App\Filament\Forms\Components\ColorSelect;
use App\Models\User;
use App\Settings\CollegeBrandingSettings;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SettingsPage;

class ManageCollegeBrandingSettings extends SettingsPage
{
    protected static ?string $navigationLabel = 'Branding';

    protected static ?int $navigationSort = 20;

    protected static string $settings = CollegeBrandingSettings::class;

    protected static ?string $title = 'Branding';

    protected static ?string $cluster = DisplaySettings::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return SettingsPermissions::active() ? $user->can(['settings.view-any']) : $user->can(['product_admin.view-any']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('is_enabled')
                    ->inline(false)
                    ->label('Enable Branding Bar')
                    ->required()
                    ->live()
                    ->columnSpanFull(),
                Toggle::make('dismissible')
                    ->inline(false)
                    ->label('Dismissible')
                    ->visible(fn (Get $get) => $get('is_enabled'))
                    ->columnSpanFull(),
                TextInput::make('college_text')
                    ->label('College Text')
                    ->placeholder('@ Canyon Community College - Go Lions!')
                    ->required()
                    ->string()
                    ->visible(fn (Get $get) => $get('is_enabled'))
                    ->autocomplete(false),
                ColorSelect::make('color')
                    ->label('Branding Bar Color')
                    ->visible(fn (Get $get) => $get('is_enabled'))
                    ->required(),
            ])
            ->disabled(! auth()->user()->can('product_admin.*.update'));
    }

    public function save(): void
    {
        if (! auth()->user()->can('product_admin.*.update')) {
            return;
        }

        // Save the settings first
        parent::save();

        // Retrieve the updated settings
        $settings = app(CollegeBrandingSettings::class);

        // Check the specific field
        if (! $settings->dismissible) {
            User::query()->update(['is_branding_bar_dismissed' => false]);
        }
    }

    public function getRedirectUrl(): ?string
    {
        return ManageCollegeBrandingSettings::getUrl();
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        if (! auth()->user()->can('product_admin.*.update')) {
            return [];
        }

        return parent::getFormActions();
    }
}
