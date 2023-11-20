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

namespace Assist\Form\Filament\Resources\FormResource\RelationManagers;

use Excel;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\Action;
use Assist\Form\Models\FormSubmission;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Infolists\Components\Section;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Assist\Form\Exports\FormSubmissionExport;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Form\Filament\Blocks\FormFieldBlockRegistry;
use App\Filament\Resources\RelationManagers\RelationManager;

class FormSubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    public function infolist(Infolist $infolist): Infolist
    {
        /** @var FormSubmission $submission */
        $submission = $infolist->getRecord();

        $schema = [];

        $blocks = FormFieldBlockRegistry::keyByType();

        $form = $submission->form;

        if ($form->is_wizard) {
            foreach ($form->steps as $step) {
                $schema[] = Section::make($step->label)
                    ->schema($this->getFieldsInfolistComponents(
                        $step->fields,
                        $blocks,
                        data: $submission->content[$step->label] ?? [],
                    ))
                    ->statePath($step->label);
            }
        } else {
            $schema = $this->getFieldsInfolistComponents(
                $form->fields,
                $blocks,
                data: $submission->content,
            );
        }

        return $infolist
            ->schema($schema)
            ->columns(1)
            ->statePath('content');
    }

    public function getFieldsInfolistComponents(Collection $fields, array $blocks, array $data): array
    {
        $schema = [];

        foreach ($fields as $field) {
            if (! array_key_exists($field->key, $data)) {
                continue;
            }

            $block = $blocks[$field->type];

            if (! $block) {
                continue;
            }

            $schema[] = $block::getInfolistEntry($field);
        }

        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->headerActions([
                Action::make('export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        $filename = str("form-submissions-{$this->getOwnerRecord()->name}-")
                            ->append(now()->format('Y-m-d-Hisv'))
                            ->slug()
                            ->append('.csv');

                        return Excel::download(new FormSubmissionExport($this->getOwnerRecord()->submissions), $filename);
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (FormSubmission $record) => "Submission Details: {$record->created_at}"),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('Export')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $filename = str("selected-form-submissions-{$this->getOwnerRecord()->name}-")
                                ->append(now()->format('Y-m-d-Hisv'))
                                ->slug()
                                ->append('.csv');

                            return Excel::download(new FormSubmissionExport($records), $filename);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
