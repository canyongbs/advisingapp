<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace FilamentTiptapEditor\Actions;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Actions\Action;

class GridBuilderAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(trans('filament-tiptap-editor::grid-modal.heading'));

        $this->modalWidth('md');

        $this->form([
            Grid::make(2)
                ->schema([
                    ViewField::make('grid_preview')
                        ->view('filament-tiptap-editor::components.grid-modal-preview')
                        ->columnSpanFull(),
                    TextInput::make('columns')
                        ->label(trans('filament-tiptap-editor::grid-modal.labels.columns'))
                        ->required()
                        ->default(2)
                        ->reactive()
                        ->minValue(2)
                        ->maxValue(12)
                        ->numeric()
                        ->step(1),
                    Select::make('stack_at')
                        ->label(trans('filament-tiptap-editor::grid-modal.labels.stack_at'))
                        ->reactive()
                        ->selectablePlaceholder(false)
                        ->options([
                            'none' => trans('filament-tiptap-editor::grid-modal.labels.dont_stack'),
                            'sm' => 'sm',
                            'md' => 'md',
                            'lg' => 'lg',
                        ])
                        ->default('md'),
                    Toggle::make('asymmetric')
                        ->label(trans('filament-tiptap-editor::grid-modal.labels.asymmetric'))
                        ->default(false)
                        ->reactive()
                        ->columnSpanFull(),
                    TextInput::make('asymmetric_left')
                        ->label(trans('filament-tiptap-editor::grid-modal.labels.asymmetric_left'))
                        ->required()
                        ->reactive()
                        ->minValue(1)
                        ->maxValue(12)
                        ->numeric()
                        ->visible(fn (callable $get) => $get('asymmetric')),
                    TextInput::make('asymmetric_right')
                        ->label(trans('filament-tiptap-editor::grid-modal.labels.asymmetric_right'))
                        ->required()
                        ->reactive()
                        ->minValue(1)
                        ->maxValue(12)
                        ->numeric()
                        ->visible(fn (callable $get) => $get('asymmetric')),
                ]),
        ]);

        $this->modalFooterActions(function ($action) {
            return [
                $action->getModalSubmitAction()
                    ->label(trans('filament-tiptap-editor::grid-modal.labels.submit')),
                $action->getModalCancelAction(),
            ];
        });

        $this->action(function (TiptapEditor $component, $data) {
            $component->getLivewire()->dispatch(
                event: 'insertFromAction',
                type: 'grid',
                statePath: $component->getStatePath(),
                data: $data,
            );
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'filament_tiptap_grid';
    }
}
