<?php

namespace Assist\Authorization\Filament\Pages\Auth;

use Filament\Panel;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\SimplePage;
use Filament\Actions\ActionGroup;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Filament\Pages\Concerns\HasRoutes;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Password;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Assist\Authorization\Http\Middleware\RedirectIfPasswordNotSet;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

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
