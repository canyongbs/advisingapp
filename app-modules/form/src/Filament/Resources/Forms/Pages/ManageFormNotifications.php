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

namespace AdvisingApp\Form\Filament\Resources\Forms\Pages;

use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Models\Form;
use App\Features\FormsNotificationFeature;
use App\Filament\Forms\Components\UserSelect;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection as ConcernsEditPageRedirection;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Override;

class ManageFormNotifications extends EditRecord
{
    use ConcernsEditPageRedirection;

    protected static string $resource = FormResource::class;

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?string $breadcrumb = 'Notifications';

    #[Override]
    public static function canAccess(array $parameters = []): bool
    {
        return FormsNotificationFeature::active() && parent::canAccess($parameters);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->schema([
                        UserSelect::make('notification_users')
                            ->label('Users to Notify')
                            ->relationship('notificationUsers', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                        Toggle::make('notify_to_care_team')
                            ->label('Notify to Care Team')
                            ->visible(fn (Form $form) => $form->is_authenticated),
                        Toggle::make('notify_to_subscribers')
                            ->label('Notify to Subscribers')
                            ->visible(fn (Form $form) => $form->is_authenticated),
                        Toggle::make('notify_via_app')
                            ->label('Notify via App'),
                        Toggle::make('notify_via_email')
                            ->label('Notify via Email'),
                    ]),
            ]);
    }
}
