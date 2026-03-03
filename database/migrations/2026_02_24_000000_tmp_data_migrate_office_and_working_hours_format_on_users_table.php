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

use App\Features\PersonalBookingAvailabilityFeature;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    private const ORDERED_DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    public function up(): void
    {
        DB::transaction(function () {
            DB::table('users')
                ->where(function (Builder $query) {
                    $query->whereNotNull('office_hours')
                        ->orWhereNotNull('working_hours');
                })
                ->chunkById(100, function (Collection $users) {
                    foreach ($users as $user) {
                        $updates = [];

                        if (! is_null($user->office_hours)) {
                            $officeHours = json_decode($user->office_hours, true);

                            if (is_array($officeHours)) {
                                $updates['office_hours'] = json_encode($this->convertOldToNewFormat($officeHours));
                            }
                        }

                        if (! is_null($user->working_hours)) {
                            $workingHours = json_decode($user->working_hours, true);

                            if (is_array($workingHours)) {
                                $updates['working_hours'] = json_encode($this->convertOldToNewFormat($workingHours));
                            }
                        }

                        if (! empty($updates)) {
                            DB::table('users')
                                ->where('id', $user->id)
                                ->update($updates);
                        }
                    }
                });
            PersonalBookingAvailabilityFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            PersonalBookingAvailabilityFeature::deactivate();
            DB::table('users')
                ->where(function (Builder $query) {
                    $query->whereNotNull('office_hours')
                        ->orWhereNotNull('working_hours');
                })
                ->chunkById(100, function (Collection $users) {
                    foreach ($users as $user) {
                        $updates = [];

                        if (! is_null($user->office_hours)) {
                            $officeHours = json_decode($user->office_hours, true);

                            if (is_array($officeHours)) {
                                $updates['office_hours'] = json_encode($this->convertNewToOldFormat($officeHours));
                            }
                        }

                        if (! is_null($user->working_hours)) {
                            $workingHours = json_decode($user->working_hours, true);

                            if (is_array($workingHours)) {
                                $updates['working_hours'] = json_encode($this->convertNewToOldFormat($workingHours));
                            }
                        }

                        if (! empty($updates)) {
                            DB::table('users')
                                ->where('id', $user->id)
                                ->update($updates);
                        }
                    }
                });
        });
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function convertOldToNewFormat(array $data): array
    {
        $result = [];

        foreach (self::ORDERED_DAYS as $day) {
            $dayData = $data[$day] ?? [];

            $isEnabled = (bool) ($dayData['enabled'] ?? false);

            $result[$day] = [
                'is_enabled' => $isEnabled,
                'starts_at' => $this->normalizeTime($dayData['starts_at'] ?? null, 'new'),
                'ends_at' => $this->normalizeTime($dayData['ends_at'] ?? null, 'new'),
            ];
        }

        return $result;
    }

    private function normalizeTime(?string $time, string $format = 'new'): ?string
    {
        if (! filled($time)) {
            return null;
        }

        try {
            if ($format === 'new') {
                $carbon = Carbon::createFromFormat('H:i:s', $time);

                return $carbon->format('H:i');
            }
            $carbon = Carbon::createFromFormat('H:i', $time);

            return $carbon->format('H:i:s');
        } catch (Throwable) {
            return $time;
        }
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function convertNewToOldFormat(array $data): array
    {
        $result = [];

        foreach (self::ORDERED_DAYS as $day) {
            $dayData = $data[$day] ?? [];

            $isEnabled = (bool) ($dayData['is_enabled'] ?? false);

            $result[$day] = [
                'enabled' => $isEnabled,
                'starts_at' => $this->normalizeTime($dayData['starts_at'] ?? null, 'old'),
                'ends_at' => $this->normalizeTime($dayData['ends_at'] ?? null, 'old'),
            ];
        }

        return $result;
    }
};
