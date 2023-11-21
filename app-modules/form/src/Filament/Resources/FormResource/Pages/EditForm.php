<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
