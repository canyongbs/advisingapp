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

namespace AdvisingApp\StudentDataModel\Models\Contracts;

use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\Task\Models\Task;
use App\Models\Tag;
use App\Models\Taggable;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @phpstan-require-extends Model
 *
 * @mixin Model
 *
 * @property-read Collection<int, User> $careTeam
 * @property-read ProspectEmailAddress|StudentEmailAddress|null $primaryEmailAddress
 * @property-read Collection<int, Alert> $alerts
 * @property-read Collection<int, Task> $tasks
 * @property-read Collection<int, Tag> $tags
 * @property-read Collection<int, Interaction> $interactions
 * @property-read Collection<int, CaseModel> $cases
 */
interface Educatable extends Identifiable, CanBeNotified
{
    public static function getLabel(): string;

    public static function displayNameKey(): string;

    public static function displayEmailKey(): string;

    public function careTeam(): MorphToMany;

    public static function getLicenseType(): LicenseType;

    /**
     * @return HasManyThrough<EventAttendee, covariant Model, covariant Model>
     */
    public function eventAttendeeRecords(): HasManyThrough;

    public function canReceiveSms(): bool;

    /**
     * @return MorphToMany<Tag, covariant Student|Prospect, covariant Taggable>
     */
    public function tags(): MorphToMany;

    /**
     * @return MorphMany<Alert, covariant Model>
     */
    public function alerts(): MorphMany;

    /**
     * @return MorphMany<Task, covariant Model>
     */
    public function tasks(): MorphMany;

    /**
     * @return BelongsTo<covariant Model, covariant Model>
     */
    public function primaryEmailAddress(): BelongsTo;

    /**
     * @return HasMany<covariant Model, covariant Model>
     */
    public function emailAddresses(): HasMany;

    /**
     * @return BelongsTo<covariant Model, covariant Model>
     */
    public function primaryPhoneNumber(): BelongsTo;

    /**
     * @return HasMany<covariant Model, covariant Model>
     */
    public function phoneNumbers(): HasMany;

    /**
     * @return MorphMany<Interaction, covariant Model>
     */
    public function interactions(): MorphMany;

    /**
     * @return MorphMany<CaseModel, covariant Model>
     */
    public function cases(): MorphMany;
}
