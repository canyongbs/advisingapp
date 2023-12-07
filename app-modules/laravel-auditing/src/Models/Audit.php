<?php

namespace Assist\Auditing\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $tags
 * @property string $event
 * @property array $new_values
 * @property array $old_values
 * @property mixed $user
 * @property mixed $auditable.
 *
 * @mixin IdeHelperAudit
 */
class Audit extends Model implements \Assist\Auditing\Contracts\Audit
{
    use \Assist\Auditing\Audit;

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        // Note: Please do not add 'auditable_id' in here, as it will break non-integer PK models
    ];

    public function getSerializedDate($date)
    {
        return $this->serializeDate($date);
    }
}
