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
