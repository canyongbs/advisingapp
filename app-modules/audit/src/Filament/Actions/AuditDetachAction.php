<?php

namespace Assist\Audit\Filament\Actions;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Filament\Tables\Actions\DetachAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AuditDetachAction extends DetachAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->using(function (Model $record, Table $table) {
            /** @var BelongsToMany $relationship */
            $relationship = $table->getRelationship();

            /** @var Auditable $parent */
            $parent = $relationship->getParent();

            $parent->auditDetach($relationship->getRelationName(), $record);
        });
    }
}
