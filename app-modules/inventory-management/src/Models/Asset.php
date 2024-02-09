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

namespace AdvisingApp\InventoryManagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\InventoryManagement\Models\Scopes\ClassifiedAs;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\InventoryManagement\Enums\SystemAssetStatusClassification;

/**
 * @property-read string $purchase_age
 *
 * @mixin IdeHelperAsset
 */
class Asset extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'description',
        'location_id',
        'name',
        'purchase_date',
        'serial_number',
        'status_id',
        'type_id',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(AssetType::class, 'type_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(AssetLocation::class, 'location_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(AssetStatus::class, 'status_id');
    }

    public function maintenanceActivities(): HasMany
    {
        return $this->hasMany(MaintenanceActivity::class, 'asset_id');
    }

    public function checkOuts(): HasMany
    {
        return $this->hasMany(AssetCheckOut::class, 'asset_id');
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(AssetCheckIn::class, 'asset_id');
    }

    public function latestCheckOut(): HasOne
    {
        return $this->hasOne(AssetCheckOut::class, 'asset_id')
            ->latest('checked_out_at');
    }

    public function latestCheckIn(): HasOne
    {
        return $this->hasOne(AssetCheckIn::class, 'asset_id')
            ->latest('checked_in_at');
    }

    public function isAvailable(): bool
    {
        return $this->status->classification === SystemAssetStatusClassification::Available
            && (is_null($this->latestCheckOut) || ! is_null($this->latestCheckOut?->asset_check_in_id));
    }

    public function isNotAvailable(): bool
    {
        return ! $this->isAvailable();
    }

    public function isCheckedOut(): bool
    {
        return $this->status->classification === SystemAssetStatusClassification::CheckedOut
            && is_null($this->latestCheckOut?->asset_check_in_id);
    }

    public function transitionToUnderMaintenance(): void
    {
        $this->status()
            ->associate(AssetStatus::where('name', 'Under Maintenance')->first())
            ->save();
    }

    public function transitionToAvailable(): void
    {
        $this->status()
            ->associate(AssetStatus::tap(new ClassifiedAs(SystemAssetStatusClassification::Available))->first())
            ->save();
    }

    protected function purchaseAge(): Attribute
    {
        return Attribute::get(function () {
            if ($this->purchase_date->isFuture()) {
                return '0 Years 0 Months';
            }

            /** @var ?User $user */
            $user = auth()->user();

            $diff = $this
                ->purchase_date
                ->roundMonth()
                ->setTimezone($user?->timezone)
                ->diff();

            return $diff->y . ' ' . ($diff->y === 1 ? 'Year' : 'Years') . ' ' .
                $diff->m . ' ' . ($diff->m === 1 ? 'Month' : 'Months');
        });
    }
}
