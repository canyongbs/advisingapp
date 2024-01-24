<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\Form\Actions\GenerateSubmissibleEmbedCode;
use AdvisingApp\ServiceManagement\Models\ServiceRequestForm;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource\Pages\Concerns\HasSharedFormConfiguration;

class EditServiceRequestForm extends EditRecord
{
    use HasSharedFormConfiguration;

    protected static string $resource = ServiceRequestFormResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->fields());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->url(fn (ServiceRequestForm $serviceRequestForm) => route('service-request-forms.show', ['serviceRequestForm' => $serviceRequestForm]))
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            Action::make('embed_snippet')
                ->label('Embed Snippet')
                ->infolist(
                    [
                        TextEntry::make('snippet')
                            ->label('Click to Copy')
                            ->state(function (ServiceRequestForm $serviceRequestForm) {
                                $code = resolve(GenerateSubmissibleEmbedCode::class)->handle($serviceRequestForm);

                                return <<<EOD
                                ```
                                {$code}
                                ```
                                EOD;
                            })
                            ->markdown()
                            ->copyable()
                            ->copyableState(fn (ServiceRequestForm $serviceRequestForm) => resolve(GenerateSubmissibleEmbedCode::class)->handle($serviceRequestForm))
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500),
                    ]
                )
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->hidden(fn (ServiceRequestForm $serviceRequestForm) => ! $serviceRequestForm->embed_enabled),
            DeleteAction::make(),
        ];
    }
}
