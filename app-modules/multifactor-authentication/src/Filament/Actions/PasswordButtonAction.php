<?php

namespace AdvisingApp\MultifactorAuthentication\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

class PasswordButtonAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! $this->isPasswordSessionValid()) {
            $this->requiresConfirmation()
                ->modalHeading('Confirm password')
                ->modalDescription('Please confirm your password to complete this action.')
                ->form([
                    TextInput::make('current_password')
                        ->label('Current password')
                        ->required()
                        ->password()
                        ->rule('current_password'),
                ]);
        }
    }

    public function call(array $data = []): mixed
    {
        if (! $this->isPasswordSessionValid()) {
            session(['auth.password_confirmed_at' => time()]);
        }

        return parent::call($data);
    }

    protected function isPasswordSessionValid()
    {
        return session()->has('auth.password_confirmed_at')
            && (time() - session('auth.password_confirmed_at', 0)) < config('auth.password_timeout');
    }
}
