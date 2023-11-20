<?php

namespace Assist\Audit\Filament\Actions;

use OwenIt\Auditing\Contracts\Auditable;
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AuditAttachAction extends AttachAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->using(function ($data, $record, BelongsToMany $relationship) {
            /** @var Auditable $parent */
            $parent = $relationship->getParent();

            $parent->auditAttach($relationship->getRelationName(), $record);
        });
    }
}
