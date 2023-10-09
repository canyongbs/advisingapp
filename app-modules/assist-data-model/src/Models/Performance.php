<?php

namespace Assist\AssistDataModel\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * @mixin IdeHelperPerformance
 */
class Performance extends Model
{
    use HasFactory;
    use DefinesPermissions;

    // TODO: Need to revisit whether or not this should be the primary key, just using it for now since there is nothing else
    protected $primaryKey = 'sisid';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    public function getWebPermissions(): Collection
    {
        return collect(['view-any', '*.view']);
    }

    public function getApiPermissions(): Collection
    {
        return collect([]);
    }

    public function getTable()
    {
        if ($this->table) {
            return $this->table;
        }

        return config('database.adm_materialized_views_enabled')
            ? 'performance_local'
            : parent::getTable();
    }
}
