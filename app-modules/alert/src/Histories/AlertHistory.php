<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Alert\Histories;

use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\Alert\Models\AlertStatus;
use AdvisingApp\Alert\Observers\AlertHistoryObserver;
use AdvisingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AdvisingApp\Timeline\Models\History;
use AdvisingApp\Timeline\Timelines\AlertHistoryTimeline;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

#[ObservedBy([AlertHistoryObserver::class])]
class AlertHistory extends History implements ProvidesATimeline
{
    public function timeline(): AlertHistoryTimeline
    {
        return new AlertHistoryTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        /* @var Student|Prospect $forModel */
        return $forModel->alertHistories()->get();
    }

    public function getFormattedValueForKey(string $key): array
    {
        return match ($key) {
            'status' => [
                'key' => 'Status',
                'old' => array_key_exists($key, $this->old) ? SystemAlertStatusClassification::tryFrom($this->old[$key])?->getLabel() : null,
                'new' => SystemAlertStatusClassification::tryFrom($this->new[$key])?->getLabel(),
            ],
            'status_id' => [
                'key' => 'Status',
                'old' => array_key_exists($key, $this->old)
                  ? AlertStatus::find($this->old[$key])?->name
                  : null,
                'new' => AlertStatus::find($this->new[$key])?->name,
            ],
            'severity' => [
                'key' => 'Severity',
                'old' => array_key_exists($key, $this->old) ? AlertSeverity::tryFrom($this->old[$key])?->getLabel() : null,
                'new' => AlertSeverity::tryFrom($this->new[$key])?->getLabel(),
            ],
            default => parent::getFormattedValueForKey($key),
        };
    }
}
