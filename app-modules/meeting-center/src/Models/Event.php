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

namespace AdvisingApp\MeetingCenter\Models;

use Exception;
use App\Models\BaseModel;
use Laravel\Pennant\Feature;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use AdvisingApp\Campaign\Models\CampaignAction;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\MeetingCenter\Jobs\CreateEventAttendees;

/**
 * @mixin IdeHelperEvent
 */
class Event extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'location',
        'capacity',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function eventRegistrationForm(): HasOne
    {
        return $this->hasOne(EventRegistrationForm::class, 'event_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(EventAttendee::class, 'event_id');
    }

    public static function executeFromCampaignAction(CampaignAction $action): bool|string
    {
        if (app(LicenseSettings::class)->data->addons->eventManagement) {
            try {
                DB::beginTransaction();

                $event = Event::find($action->data['event']);

                $user = $action->campaign->user;

                $campaignRelation = Feature::active('enable-segments')
                    ? 'segment'
                    : 'caseload';

                $emails = $action
                    ->campaign
                    ->{$campaignRelation}
                    ->retrieveRecords()
                    ->whereNotNull('email')
                    ->whereNotIn('email', $event->attendees()->pluck('email')->toArray())
                    ->pluck('email')
                    ->toArray();

                dispatch(new CreateEventAttendees($event, $emails, $user));

                DB::commit();

                return true;
            } catch (Exception $e) {
                DB::rollBack();

                return $e->getMessage();
            }
        }

        return false;
        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }
}
