<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\StudentDataModel\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\StudentDataModel\Enums\EmailAddressOptInOptOutStatus;
use AdvisingApp\StudentDataModel\Enums\EmailHealthStatus;
use AdvisingApp\StudentDataModel\Observers\StudentEmailAddressObserver;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperStudentEmailAddress
 */
#[ObservedBy([StudentEmailAddressObserver::class])]
class StudentEmailAddress extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'sisid',
        'address',
        'type',
        'order',
    ];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'sisid', 'sisid');
    }

    /**
     * @return HasOne<BouncedEmailAddress, $this>
     */
    public function bounced(): HasOne
    {
        return $this->hasOne(BouncedEmailAddress::class, 'address', 'address');
    }

    /**
     * @return HasOne<EmailAddressOptInOptOut, $this>
     */
    public function optedOut(): HasOne
    {
        return $this->hasOne(EmailAddressOptInOptOut::class, 'address', 'address');
    }

    public function getHealthStatus(): EmailHealthStatus
    {
        // Check in order: Bounced > OptedOut > Healthy
        if ($this->bounced()->exists()) {
            return EmailHealthStatus::Bounced;
        }

        $optOutRecord = $this->optedOut()->first();

        if ($optOutRecord && $optOutRecord->status === EmailAddressOptInOptOutStatus::OptedOut) {
            return EmailHealthStatus::OptedOut;
        }

        return EmailHealthStatus::Healthy;
    }
}
