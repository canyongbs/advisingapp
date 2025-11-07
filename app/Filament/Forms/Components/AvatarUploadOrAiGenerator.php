<?php

namespace App\Filament\Forms\Components;

use AdvisingApp\Ai\Jobs\GenerateAvatar;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;

class AvatarUploadOrAiGenerator
{
    public static function make(): Fieldset
    {
        return Fieldset::make('Avatar')
            ->schema([
                SpatieMediaLibraryFileUpload::make('avatar')
                    ->disk('s3')
                    ->collection('avatar')
                    ->visibility('private')
                    ->avatar()
                    ->columnSpanFull()
                    ->acceptedFileTypes([
                        'image/png',
                        'image/jpeg',
                        'image/gif',
                    ])
                    ->hiddenJs(<<<'JS'
                        $get('is_generating_avatar')
                        JS)
                    ->hiddenLabel(),
                Textarea::make('avatar_generation_instructions')
                    ->label('Please describe the Avatar you would like us to create using generative AI:')
                    ->validationAttribute('avatar generation instructions')
                    ->maxLength(1000)
                    ->visibleJs(<<<'JS'
                        $get('is_generating_avatar')
                        JS)
                    ->dehydrated(false)
                    ->saveRelationshipsUsing(function (Textarea $component, Get $get, Set $set, Model $record, ?string $state) {
                        if (! $get('is_generating_avatar')) {
                            return;
                        }

                        if (blank($state)) {
                            return;
                        }

                        dispatch(new GenerateAvatar($record, $state));

                        Notification::make()
                            ->title('Avatar generation started')
                            ->body('We are generating your avatar; it should be ready in 1-5 minutes.')
                            ->success()
                            ->send();

                        $component->state(null);
                        $set('is_generating_avatar', false);
                    }),
                Toggle::make('is_generating_avatar')
                    ->label('Generate avatar with AI')
                    ->dehydrated(false)
                    ->visible(fn (): bool => app(AiIntegratedAssistantSettings::class)->getDefaultModel()->getService()->hasImageGeneration()),
            ])
            ->columns(1)
            ->columnSpan(1);
    }
}
