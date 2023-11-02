<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Assist\Form\Models\Form;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Pages\EditRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\Form\Actions\GenerateFormEmbedCode;
use Assist\Form\Filament\Resources\FormResource;
use Assist\Form\Filament\Resources\FormResource\Pages\Concerns\HasSharedFormConfiguration;

class EditForm extends EditRecord
{
    use HasSharedFormConfiguration;

    protected static string $resource = FormResource::class;

    protected static ?string $navigationLabel = 'Edit';

    public function form(FilamentForm $form): FilamentForm
    {
        return $form
            ->schema($this->fields());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->url(fn (Form $form) => route('forms.show', ['form' => $form]))
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            Action::make('embed_snippet')
                ->label('Embed Snippet')
                ->infolist(
                    [
                        TextEntry::make('snippet')
                            ->label('Click to Copy')
                            ->state(function (Form $form) {
                                $code = resolve(GenerateFormEmbedCode::class)->handle($form);

                                return <<<EOD
                                ```
                                {$code}
                                ```
                                EOD;
                            })
                            ->markdown()
                            ->copyable()
                            ->copyableState(fn (Form $form) => resolve(GenerateFormEmbedCode::class)->handle($form))
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500),
                    ]
                )
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->hidden(fn (Form $form) => ! $form->embed_enabled),
            DeleteAction::make(),
        ];
    }
}
