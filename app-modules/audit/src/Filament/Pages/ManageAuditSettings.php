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

namespace AdvisingApp\Audit\Filament\Pages;

use AdvisingApp\Audit\Actions\Finders\AuditableModels;
use AdvisingApp\Audit\Settings\AuditSettings;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageAuditSettings extends SettingsPage
{
    protected static ?string $navigationLabel = 'Auditing';

    protected static ?int $navigationSort = 20;

    protected static string $settings = AuditSettings::class;

    protected static ?string $title = 'Auditing';

    protected static ?string $navigationGroup = 'Global Administration';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->isSuperAdmin();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('audited_models_exclude')
                    ->options(AuditableModels::all())
                    ->multiple()
                    ->in(AuditableModels::all()->keys()->toArray())
                    ->rules(
                        [
                            'array',
                        ]
                    )
                    ->hintIcon(
                        icon: 'heroicon-m-question-mark-circle',
                        tooltip: 'Models added here will not be tracked by the audit trail.'
                    )
                    ->columnSpanFull(),
                TextInput::make('retention_duration_in_days')
                    ->label('Retention Duration')
                    ->integer()
                    ->minValue(1)
                    ->step(1)
                    ->suffix('Day/s')
                    ->hintIcon(
                        icon: 'heroicon-m-question-mark-circle',
                        tooltip: 'Audit trail records older than the retention duration will be deleted.'
                    ),
                TextInput::make('assistant_chat_message_logs_retention_duration_in_days')
                    ->label('Assistant retention Duration')
                    ->integer()
                    ->minValue(1)
                    ->step(1)
                    ->suffix('Day/s')
                    ->hintIcon(
                        icon: 'heroicon-m-question-mark-circle',
                        tooltip: 'Assistant chat message logs older than the retention duration will be deleted.'
                    ),
            ]);
    }
}
