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

namespace AdvisingApp\Ai\Filament\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use AdvisingApp\Ai\Enums\AiModel;
use Filament\Forms\Components\Select;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Filament\Clusters\ArtificialIntelligence;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;

/**
 * @property-read ?AiAssistant $defaultAssistant

 */
class ManageAiIntegratedAssistantSettings extends SettingsPage
{
    protected static string $settings = AiIntegratedAssistantSettings::class;

    protected static ?string $title = 'Integrated Assistant';

    protected static ?string $cluster = ArtificialIntelligence::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?int $navigationSort = 40;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->hasLicense(LicenseType::ConversationalAi)) {
            return false;
        }

        return $user->can(['product_admin.view-any', 'product_admin.*.view']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('default_model')
                    ->options(collect(AiModel::getDefaultModels())
                        ->mapWithKeys(fn (AiModel $model): array => [$model->value => $model->getLabel()])
                        ->all())
                    ->searchable()
                    ->helperText('Used for general purposes like generating content when an assistant is not being used.')
                    ->required(),
            ])
            ->disabled(! auth()->user()->can('product_admin.*.update'));
    }

    public function save(): void
    {
        if (! auth()->user()->can('product_admin.*.update')) {
            return;
        }

        parent::save();
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
