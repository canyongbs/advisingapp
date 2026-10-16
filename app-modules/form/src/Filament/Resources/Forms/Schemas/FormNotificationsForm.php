<?php

namespace AdvisingApp\Form\Filament\Resources\Forms\Schemas;

use AdvisingApp\Form\Models\Form;
use App\Filament\Forms\Components\UserSelect;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormNotificationsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
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
                            ->label('Notify to Followers')
                            ->visible(fn (Form $form) => $form->is_authenticated),
                        Toggle::make('notify_via_app')
                            ->label('Notify via App'),
                        Toggle::make('notify_via_email')
                            ->label('Notify via Email'),
                    ]),
            ]);
    }
}
