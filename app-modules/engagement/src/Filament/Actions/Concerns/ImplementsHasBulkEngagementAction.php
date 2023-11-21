<?php

namespace Assist\Engagement\Filament\Actions\Concerns;

use Filament\Actions\Action;
use Filament\Actions\StaticAction;

trait ImplementsHasBulkEngagementAction
{
    public array $engageActionData = [];

    public array $engageActionRecords = [];

    public function cancelBulkEngagementAction(): Action
    {
        return Action::make('cancelBulkEngagementAction')
            ->label('Cancel')
            ->mountUsing(function () {
                $this->engageActionData = $this->mountedTableBulkActionData;
                $this->engageActionRecords = $this->selectedTableRecords;

                $this->unmountTableBulkAction();
            })
            ->requiresConfirmation()
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('danger'))
            ->action(function () {
                $this->engageActionData = [];
                $this->engageActionRecords = [];
            })
            ->modalDescription(fn () => 'The message has not been sent, are you sure you wish to return to the list view?')
            ->closeModalByClickingAway(false)
            ->modalCloseButton(false)
            ->modalCancelAction(false)
            ->extraModalFooterActions([
                Action::make('restoreBulkEngagementAction')
                    ->label('Cancel')
                    ->action(function () {
                        $this->mountTableBulkAction('engage');

                        $this->mountedTableBulkActionData = $this->engageActionData;
                        $this->selectedTableRecords = $this->engageActionRecords;
                    })
                    ->cancelParentActions(),
            ]);
    }
}
