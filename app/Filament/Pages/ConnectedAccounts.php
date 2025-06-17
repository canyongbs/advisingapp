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

namespace App\Filament\Pages;

use AdvisingApp\MeetingCenter\Managers\CalendarManager;
use App\Models\User;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

/**
 * @property Form $form
 */
class ConnectedAccounts extends ProfilePage
{
    protected static ?string $slug = 'connected-accounts';

    protected static ?string $title = 'Connected Accounts';

    protected static ?int $navigationSort = 60;

    public static function canAccess(): bool
    {
        return self::getConnectedAccounts()->count() > 0 && parent::canAccess();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Connected Accounts')
                    ->description('Disconnect your external accounts.')
                    ->schema(self::getConnectedAccounts()->toArray())
                    ->visible(fn () => self::getConnectedAccounts()->count() > 0),
            ]);
    }

    /**
     * @return Collection<int, Grid>
     */
    private static function getConnectedAccounts(): Collection
    {
        $connectedAccounts = collect([
            Grid::make()
                ->schema([
                    Placeholder::make('calendar')
                        ->label(function (): string {
                            /** @var User $user */
                            $user = auth()->user();

                            return "{$user->calendar->provider_type->getLabel()} Calendar";
                        })
                        ->content(function (): ?string {
                            /** @var User $user */
                            $user = auth()->user();

                            return $user->calendar?->name;
                        }),
                    Actions::make([
                        FormAction::make('Disconnect')
                            ->icon('heroicon-m-trash')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(function () {
                                /** @var User $user */
                                $user = auth()->user();

                                $calendar = $user->calendar;

                                $revoked = resolve(CalendarManager::class)
                                    ->driver($calendar->provider_type->value)
                                    ->revokeToken($calendar);

                                if ($revoked) {
                                    $calendar->delete();

                                    Notification::make()
                                        ->title("Disconnected {$calendar->provider_type->getLabel()} Calendar")
                                        ->success()
                                        ->send();
                                }
                            }),
                    ])->alignRight()
                        ->verticallyAlignCenter(),
                ])
                ->visible(function (): bool {
                    /** @var User $user */
                    $user = auth()->user();

                    return filled($user->calendar?->oauth_token);
                }),
        ])->filter(fn (Component $component) => $component->isVisible());

        return $connectedAccounts;
    }
}
