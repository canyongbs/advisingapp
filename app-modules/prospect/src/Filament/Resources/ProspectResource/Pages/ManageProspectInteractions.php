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

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\MorphToSelect;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\CreateInteraction;
use Assist\Interaction\Filament\Resources\InteractionResource\RelationManagers\HasManyMorphedInteractionsRelationManager;

class ManageProspectInteractions extends ManageRelatedRecords
{
    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'interactions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Interactions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Interactions';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public function form(Form $form): Form
    {
        return (resolve(CreateInteraction::class))->form($form);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return (resolve(HasManyMorphedInteractionsRelationManager::class))->infolist($infolist);
    }

    public function table(Table $table): Table
    {
        return (resolve(HasManyMorphedInteractionsRelationManager::class))->table($table);
    }
}
