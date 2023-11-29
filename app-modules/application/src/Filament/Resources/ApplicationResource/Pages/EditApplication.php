<?php

namespace Assist\Application\Filament\Resources\ApplicationResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Pages\EditRecord;
use Assist\Application\Models\Application;
use Filament\Infolists\Components\TextEntry;
use Assist\Form\Actions\GenerateSubmissibleEmbedCode;
use Assist\Application\Filament\Resources\ApplicationResource;
use Assist\Application\Filament\Resources\ApplicationResource\Pages\Concerns\HasSharedFormConfiguration;

class EditApplication extends EditRecord
{
    use HasSharedFormConfiguration;

    protected static string $resource = ApplicationResource::class;

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
                ->url(fn (Application $application) => route('applications.show', ['application' => $application]))
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            Action::make('embed_snippet')
                ->label('Embed Snippet')
                ->infolist(
                    [
                        TextEntry::make('snippet')
                            ->label('Click to Copy')
                            ->state(function (Application $application) {
                                $code = resolve(GenerateSubmissibleEmbedCode::class)->handle($application);

                                return <<<EOD
                                ```
                                {$code}
                                ```
                                EOD;
                            })
                            ->markdown()
                            ->copyable()
                            ->copyableState(fn (Application $application) => resolve(GenerateSubmissibleEmbedCode::class)->handle($application))
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500),
                    ]
                )
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->hidden(fn (Application $application) => ! $application->embed_enabled),
            DeleteAction::make(),
        ];
    }
}
