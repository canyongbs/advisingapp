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

namespace AdvisingApp\Authorization\Filament\Pages\Auth;

use AdvisingApp\Authorization\Http\Middleware\RedirectIfPasswordNotSet;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasRoutes;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Panel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;

/**
 * @property-read Form $form
 */
class SetPassword extends SimplePage
{
    use HasRoutes;
    use InteractsWithFormActions;
    use WithRateLimiting;

    protected static string $view = 'authorization::set-password';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        if (filled($user->password) || $user->is_external) {
            redirect(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title('Too many attempts')
                ->body("Please try again in {$exception->secondsUntilAvailable} seconds.")
                ->danger()
                ->send();

            return;
        }

        /** @var User $user */
        $user = auth()->user();

        $data = $this->form->getState();

        $user->password = $data['password'];
        $user->save();

        auth()->logout();

        Notification::make()
            ->title('Password set')
            ->body('Your password has been set.')
            ->success()
            ->send();

        redirect(Filament::getUrl());
    }

    public function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->submit('save');
    }

    public static function routes(Panel $panel): void
    {
        $slug = static::getSlug();

        Route::get("/{$slug}", static::class)
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->name('auth.set-password');
    }

    /**
     * @return string | array<string>
     */
    public static function getRouteMiddleware(Panel $panel): string | array
    {
        return [
            ...(static::isEmailVerificationRequired($panel) ? [static::getEmailVerifiedMiddleware($panel)] : []),
            ...static::$routeMiddleware,
        ];
    }

    /**
     * @return string | array<string>
     */
    public static function getWithoutRouteMiddleware(Panel $panel): string | array
    {
        return [
            RedirectIfPasswordNotSet::class,
        ];
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->password()
            ->required()
            ->rule(Password::default())
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute('password');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Confirm password')
            ->password()
            ->required()
            ->dehydrated(false);
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
