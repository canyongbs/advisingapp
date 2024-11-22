<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Prospect\Filament\Pages;

use Cknow\Money\Money;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use App\Filament\Forms\Components\Heading;
use App\Filament\Forms\Components\Paragraph;
use App\Settings\ProspectConversionSettings;
use App\Filament\Clusters\ConstituentManagement;

class ManageProspectConversionSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $settings = ProspectConversionSettings::class;

    public string $currency = 'USD';

    protected static ?string $title = 'Conversion';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Prospects';

    protected static ?string $cluster = ConstituentManagement::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return parent::canAccess() && $user->can('prospect_conversion.manage');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        Heading::make()
                            ->content('Conversion Value'),
                        Paragraph::make()
                            ->content('Setting a conversion value will enable tracking of potential institutional revenue driven by engagement efforts including individual, bulk, and campaign outreach.'),
                        Paragraph::make()
                            ->content('Please enter the estimated average revenue generated by a student over their lifetime at your institution:'),
                        TextInput::make('estimated_average_revenue')
                            ->label('Estimated Average Revenue')
                            ->prefix('$')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->rule('decimal:0,2')
                            ->required(),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['estimated_average_revenue'] = Money::parseByDecimal($data['estimated_average_revenue'], $this->currency);

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $settings = app(static::getSettings());

        $data['estimated_average_revenue'] = $settings->estimated_average_revenue?->formatByDecimal();

        return $data;
    }
}
