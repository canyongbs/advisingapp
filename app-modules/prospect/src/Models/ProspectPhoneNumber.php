<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Prospect\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Prospect\Observers\ProspectPhoneNumberObserver;
use AdvisingApp\StudentDataModel\Enums\PhoneHealthStatus;
use AdvisingApp\StudentDataModel\Models\BouncedPhoneNumber;
use AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperProspectPhoneNumber
 */
#[ObservedBy(ProspectPhoneNumberObserver::class)]
class ProspectPhoneNumber extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'prospect_id',
        'number',
        'ext',
        'type',
        'can_receive_sms',
        'order',
    ];

    protected $casts = [
        'can_receive_sms' => 'boolean',
    ];

    /**
     * @return BelongsTo<Prospect, $this>
     */
    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    /**
     * @return HasOne<SmsOptOutPhoneNumber, $this>
     */
    public function smsOptOut(): HasOne
    {
        return $this->hasOne(SmsOptOutPhoneNumber::class, 'number', 'number');
    }

    /**
     * @return HasOne<BouncedPhoneNumber, $this>
     */
    public function bounced(): HasOne
    {
        return $this->hasOne(BouncedPhoneNumber::class, 'number', 'number');
    }

    public function getHealthStatus(): PhoneHealthStatus
    {
        if ($this->bounced()->exists()) {
            return PhoneHealthStatus::Bounced;
        }

        if ($this->smsOptOut()->exists()) {
            return PhoneHealthStatus::OptedOut;
        }

        if (! $this->can_receive_sms) {
            return PhoneHealthStatus::NoSmsCapability;
        }

        return PhoneHealthStatus::Healthy;
    }
}
