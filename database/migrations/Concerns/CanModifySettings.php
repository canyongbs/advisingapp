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

namespace Database\Migrations\Concerns;

use Closure;
use Illuminate\Support\Facades\DB;

trait CanModifySettings
{
    /**
     * @param Closure(mixed): mixed $modifyPayload
     */
    public function updateSettings(string $group, string $name, Closure $modifyPayload, bool $isEncrypted = false): void
    {
        $payload = $this->getSetting($group, $name, $isEncrypted);

        $payload = $modifyPayload($payload);

        if ($isEncrypted) {
            $payload = encrypt($payload);
        }

        $payload = json_encode($payload);

        DB::table('settings')
            ->where('group', $group)
            ->where('name', $name)
            ->update([
                'payload' => $payload,
                'updated_at' => now(),
            ]);
    }

    public function getSetting(string $group, string $name, bool $isEncrypted = false): mixed
    {
        $payload = DB::table('settings')
            ->where('group', $group)
            ->where('name', $name)
            ->value('payload');

        $payload = json_decode($payload);

        if ($isEncrypted) {
            $payload = decrypt($payload);
        }

        return $payload;
    }
}
