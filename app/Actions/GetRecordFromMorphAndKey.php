<?php

namespace App\Actions;

use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;

class GetRecordFromMorphAndKey
{
    public function via(string $morphReference, string $key)
    {
        $className = Relation::getMorphedModel($morphReference);

        if (is_null($className)) {
            throw new Exception("Model not found for reference: {$morphReference}");
        }

        return $className::whereKey($key)->firstOrFail();
    }
}
