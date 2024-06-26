<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\BasicNeeds\Models\BasicNeedsCategory;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource;
use App\Exceptions\SoftDeleteContraintViolationException;

class EditBasicNeedsCategory extends EditRecord
{
    protected static string $resource = BasicNeedsCategoryResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(255)
                    ->string(),
                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535)
                    ->string(),
            ])->columns(1);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->using(function (BasicNeedsCategory $basicNeedsCategory) {
                    try {
                        $basicNeedsCategory->delete();

                        return $basicNeedsCategory;
                    } catch (SoftDeleteContraintViolationException $e) {
                        Notification::make()
                            ->title($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
