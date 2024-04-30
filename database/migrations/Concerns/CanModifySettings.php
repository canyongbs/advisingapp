<?php

namespace Database\Migrations\Concerns;

use Closure;
use Illuminate\Support\Facades\DB;

trait CanModifySettings
{
    public function updateSettings(string $group, string $name, Closure $modifyPayload, bool $isEncrypted = false): void
    {
        $payload = DB::table('settings')
            ->where('group', $group)
            ->where('name', $name)
            ->value('payload');

        $payload = json_decode($payload);

        if ($isEncrypted) {
            $payload = decrypt($payload);
        }

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
}
