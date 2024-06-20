<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedProgramResource\Pages;

use Filament\Forms\Form;
use Laravel\Pennant\Feature;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\BasicNeeds\Models\BasicNeedCategory;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedProgramResource;

class CreateBasicNeedProgram extends CreateRecord
{
    protected static string $resource = BasicNeedProgramResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Program Name')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->unique(),
                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535)
                    ->string(),
                Select::make('basic_need_category_id')
                    ->label('Program Category')
                    ->required()
                    ->relationship('basicNeedCategories', 'name')
                    ->visible(Feature::active('basic-needs-category'))
                    ->native(false)
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => BasicNeedCategory::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                    ->preload(),
                TextInput::make('contact_person')
                    ->label('Contact Person')
                    ->maxLength(255)
                    ->string(),
                TextInput::make('contact_email')
                    ->label('Email Address')
                    ->maxLength(255)
                    ->string()
                    ->email(),
                TextInput::make('contact_phone')
                    ->label('Contact Phone')
                    ->maxLength(255)
                    ->string(),
                TextInput::make('location')
                    ->label('Location')
                    ->maxLength(255)
                    ->string(),
                TextInput::make('availability')
                    ->label('Availability')
                    ->maxLength(255)
                    ->string(),
                TextInput::make('eligibility_criteria')
                    ->label('Eligibility Criteria')
                    ->maxLength(255)
                    ->string(),
                TextInput::make('application_process')
                    ->label('Application Process')
                    ->maxLength(255)
                    ->string(),
            ]);
    }
}
