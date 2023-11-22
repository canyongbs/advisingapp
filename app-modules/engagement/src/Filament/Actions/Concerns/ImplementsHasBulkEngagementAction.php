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
