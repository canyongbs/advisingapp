<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\LicenseSettings;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

class ManageLicenseSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Subscription Management';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 10;

    protected static string $settings = LicenseSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('license_key')
                    ->label('License Key')
                    ->required()
                    ->disabled(),
                Section::make('Subscription Information')
                    ->columns()
                    ->schema(
                        [
                            TextInput::make('data.subscription.clientName')
                                ->label('Client Name')
                                ->required(),
                            TextInput::make('data.subscription.partnerName')
                                ->label('Partner Name')
                                ->required(),
                            TextInput::make('data.subscription.clientPo')
                                ->label('Client PO')
                                ->required(),
                            TextInput::make('data.subscription.partnerPo')
                                ->label('Partner PO')
                                ->required(),
                            DatePicker::make('data.subscription.startDate')
                                ->label('Start Date')
                                ->required(),
                            DatePicker::make('data.subscription.endDate')
                                ->label('End Date')
                                ->required(),
                        ]
                    ),
                Section::make('Limits Configuration')
                    ->columns()
                    ->schema(
                        [
                            TextInput::make('data.limits.crmSeats')
                                ->label('CRM Seats')
                                ->numeric()
                                ->required(),
                            TextInput::make('data.limits.analyticsSeats')
                                ->label('Analytics Seats')
                                ->numeric()
                                ->required(),
                            TextInput::make('data.limits.emails')
                                ->label('Emails')
                                ->numeric()
                                ->required(),
                            TextInput::make('data.limits.sms')
                                ->label('SMS')
                                ->numeric()
                                ->required(),
                            DatePicker::make('data.limits.resetDate')
                                ->label('Reset Date')
                                ->native(false)
                                ->format('m-d')
                                ->displayFormat('m-d')
                                ->required(),
                        ]
                    ),
                Section::make('Addons')
                    ->columns()
                    ->schema(
                        [
                            Toggle::make('data.addons.onlineAdmissions')
                                ->label('Online Admissions'),
                            Toggle::make('data.addons.realtimeChat')
                                ->label('Realtime Chat'),
                            Toggle::make('data.addons.dynamicForms')
                                ->label('Dynamic Forms'),
                            Toggle::make('data.addons.conductSurveys')
                                ->label('Conduct Surveys'),
                            Toggle::make('data.addons.personalAssistant')
                                ->label('Personal Assistant'),
                            Toggle::make('data.addons.serviceManagement')
                                ->label('Service Management'),
                            Toggle::make('data.addons.knowledgeManagement')
                                ->label('Knowledge Management'),
                            Toggle::make('data.addons.studentAndProspectPortal')
                                ->label('Student and Prospect Portal'),
                        ]
                    ),
            ]);
    }
}
