<?php

namespace AdvisingApp\Authorization\Filament\Pages\Auth;

use AdvisingApp\Authorization\Actions\ClaimOtpLoginCode;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasRoutes;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Panel;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Route;

/**
 * @property-read Schema $form
 */
class ConfirmOneTimeLoginCode extends SimplePage
{
    use HasRoutes;
    use InteractsWithFormActions;
    use WithRateLimiting;

    public const SESSION_KEY = 'one-time-login';

    protected string $view = 'authorization::confirm-one-time-login-code';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        if (! $this->getIntendedUser() instanceof User) {
            redirect()->route('filament.admin.auth.login');

            return;
        }

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                OneTimeCodeInput::make('code')
                    ->label('Verification code')
                    ->autofocus()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function authenticate(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title('Too many attempts')
                ->body("Please try again in {$exception->secondsUntilAvailable} seconds.")
                ->danger()
                ->send();

            return;
        }

        $user = $this->getIntendedUser();

        if (! $user instanceof User) {
            redirect()->route('filament.admin.auth.login');

            return;
        }

        $data = $this->form->getState();

        if (! app(ClaimOtpLoginCode::class)($user, $data['code'])) {
            Notification::make()
                ->title('Invalid code')
                ->body('The code you entered is invalid or has expired.')
                ->danger()
                ->send();

            return;
        }

        $panel = Filament::getPanel('admin');

        $panel->auth()->login($user);

        session()->regenerate();

        session()->forget(static::SESSION_KEY);

        redirect()->to($this->getRedirectUrl($user, $panel));
    }

    public function getSaveFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Continue')
            ->submit('authenticate');
    }

    public static function routes(Panel $panel): void
    {
        $slug = static::getSlug();

        Route::get("/{$slug}", static::class)
            ->name('auth.one-time-login');
    }

    protected function getIntendedUser(): ?User
    {
        $id = session(static::SESSION_KEY . '.user');

        return is_string($id) ? User::query()->find($id) : null;
    }

    protected function getRedirectUrl(User $user, Panel $panel): string
    {
        if (! $user->is_external && blank($user->password)) {
            return route('filament.admin.auth.set-password');
        }

        return $panel->getHomeUrl() ?? url('/');
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [$this->getSaveFormAction()];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
