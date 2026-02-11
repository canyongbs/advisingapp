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

namespace AdvisingApp\Task\Histories;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AdvisingApp\Timeline\Models\History;
use AdvisingApp\Timeline\Timelines\TaskHistoryTimeline;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperTaskHistory
 */
class TaskHistory extends History implements ProvidesATimeline
{
    public function timeline(): TaskHistoryTimeline
    {
        return new TaskHistoryTimeline($this);
    }

    /**
     * @return Collection<int, Model>
     */
    public static function getTimelineData(Model $forModel): Collection
    {
        assert($forModel instanceof Student || $forModel instanceof Prospect);

        return $forModel->taskHistories()->get();
    }

    /**
     * @return array<string, string>
     */
    public function getFormattedValueForKey(string $key): array
    {
        return match ($key) {
            'status' => [
                'key' => 'Status',
                'old' => array_key_exists($key, $this->old)
                    ? TaskStatus::tryFrom($this->old[$key])?->getLabel()
                    : null,
                'new' => TaskStatus::tryFrom($this->new[$key])?->getLabel(),
            ],
            'due' => [
                'key' => 'Due',
                'old' => data_get($this->old, $key)
                    ? Carbon::parse($this->old[$key])->format('m-d-Y')
                    : '(Not set)',
                'new' => $this->new[$key]
                    ? Carbon::parse($this->new[$key])->format('m-d-Y')
                    : '(Not set)',
            ],
            'assigned_to' => [
                'key' => 'Assigned to',
                'old' => array_key_exists($key, $this->old)
                    ? User::find($this->old[$key])?->name
                    : null,
                'new' => User::find($this->new[$key])?->name,
                'extra' => [
                    'old' => [
                        'link' => array_key_exists($key, $this->old)
                            ? UserResource::getUrl('view', ['record' => $this->old[$key]])
                            : null,
                    ],
                    'new' => [
                        'link' => UserResource::getUrl('view', ['record' => $this->new[$key]]),
                    ],
                ],
            ],
            'created_by' => [
                'key' => 'Created by',
                'old' => array_key_exists($key, $this->old)
                    ? User::find($this->old[$key])?->name
                    : null,
                'new' => User::find($this->new[$key])?->name,
                'extra' => [
                    'old' => [
                        'link' => array_key_exists($key, $this->old)
                            ? UserResource::getUrl('view', ['record' => $this->old[$key]])
                            : null,
                    ],
                    'new' => [
                        'link' => UserResource::getUrl('view', ['record' => $this->new[$key]]),
                    ],
                ],
            ],
            default => parent::getFormattedValueForKey($key),
        };
    }

    /**
     * @return Collection<string, string>
     */
    public function getFormattedValues(): Collection
    {
        $values = parent::getFormattedValues()
            ->merge([
                'concern' => [
                    'key' => 'Concern',
                    'old' => data_get($this->old, 'concern_id')
                        ? sprintf(
                            '%s: %s',
                            str($this->old['concern_type'])->ucfirst(),
                            Relation::getMorphedModel($this->old['concern_type'])::find($this->old['concern_id'])?->display_name /** @phpstan-ignore property.notFound */
                        ) : '(Not set)',
                    'new' => data_get($this->new, 'concern_id')
                        ? sprintf(
                            '%s: %s',
                            str($this->new['concern_type'])->ucfirst(),
                            Relation::getMorphedModel($this->new['concern_type'])::find($this->new['concern_id'])?->display_name /** @phpstan-ignore property.notFound */
                        ) : '(Not set)',
                ],
            ])
            ->forget(['concern_type', 'concern_id']);

        if ($values['concern']['old'] === $values['concern']['new']) {
            $values->forget('concern');
        }

        return $values;
    }
}
