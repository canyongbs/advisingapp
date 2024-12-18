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

namespace App\Filament\Resources\UserResource\Pages;

use AdvisingApp\Authorization\Models\License;
use App\Filament\Forms\Components\Licenses;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use STS\FilamentImpersonate\Pages\Actions\Impersonate;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->disabled(false)
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('job_title')
                            ->string()
                            ->maxLength(255),
                        Toggle::make('is_external')
                            ->label('User can only login via Single Sign-On (SSO)'),
                        TextInput::make('created_at')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format(config('project.datetime_format') ?? 'Y-m-d H:i:s'))
                            ->disabled(),
                        TextInput::make('updated_at')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format(config('project.datetime_format') ?? 'Y-m-d H:i:s'))
                            ->disabled(),
                    ])
                    ->disabled(),
                Licenses::make()
                    ->hidden(fn (?User $record) => is_null($record))
                    ->disabled(function () {
                        /** @var User $user */
                        $user = auth()->user();

                        return $user->cannot('create', License::class);
                    }),
            ]);
    }

    public function boot()
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER,
            fn (): View => view('filament.resources.user-resource.mfa-badge'),
            scopes: ViewUser::class,
        );
    }

    protected function getHeaderActions(): array
    {
        /** @var User $user */
        $user = $this->getRecord();

        return [
            Action::make('mfa_reset')
                ->visible(
                    fn (User $record) => ! $record->is_external
                    && $record->hasConfirmedMultifactor() || $record->hasEnabledMultifactor()
                    && auth()->user()->can('resetMultifactorAuthentication', $record),
                )
                ->label('Reset MFA')
                ->icon('heroicon-s-lock-open')
                ->modalDescription('Are you sure you want to reset the multifactor authentication for this user?')
                ->action(fn (User $record) => $record->disableMultifactorAuthentication()),
            Impersonate::make()
                ->record($user),
            EditAction::make(),
        ];
    }
}
