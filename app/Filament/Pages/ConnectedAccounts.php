<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

/**
 * @property Schema $form
 */
class ConnectedAccounts extends ProfilePage
{
    protected static ?string $slug = 'connected-accounts';

    protected static ?string $title = 'Connected Accounts';

    protected static ?int $navigationSort = 70;

    public static function canAccess(): bool
    {
        return self::getConnectedAccounts()->count() > 0 && parent::canAccess();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    TextEntry::make('calendar')
                        ->label(function (): string {
                            /** @var User $user */
                            $user = auth()->user();

                            return "{$user->calendar->provider_type->getLabel()} Calendar";
                        })
                        ->state(function (): ?string {
                            /** @var User $user */
                            $user = auth()->user();

                            return $user->calendar?->name;
                        }),
                    Actions::make([
                        Action::make('Disconnect')
                            ->icon('heroicon-m-trash')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(function () {
                                /** @var User $user */
                                $user = auth()->user();

                                $calendar = $user->calendar;
                                $providerLabel = $calendar->provider_type->getLabel();

                                $revoked = resolve(CalendarManager::class)
                                    ->driver($calendar->provider_type->value)
                                    ->revokeToken($calendar);

                                if ($revoked) {
                                    $calendar->delete();

                                    Notification::make()
                                        ->title("Disconnected {$providerLabel} Calendar")
                                        ->success()
                                        ->send();

                                    $user->refresh();

                                    if (! $user->calendar?->oauth_token) {
                                        return redirect(ProfileInformation::getUrl());
                                    }

                                    return redirect(request()->url());
                                }
                                Notification::make()
                                    ->title("Failed to disconnect {$providerLabel} Calendar")
                                    ->body('Please try again or contact support if the problem persists.')
                                    ->danger()
                                    ->send();
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
