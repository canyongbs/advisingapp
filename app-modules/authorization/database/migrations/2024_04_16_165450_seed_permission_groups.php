<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $groups = DB::table('permission_groups')
            ->pluck('id', 'name')
            ->all();

        DB::table('permissions')
            ->whereNull('group_id')
            ->eachById(function (object $permission) use (&$groups) {
                if (! str($permission->name)->contains('.')) {
                    throw new Exception("Invalid permission name: [{$permission->name}] does not contain a period.");
                }

                $groupName = match ((string) str($permission->name)->before('.')) {
                    'sla' => 'SLA',
                    'sms_template' => 'SMS Template',
                    'in-app-communication' => 'In-App Communication',
                    'integration-aws-ses-event-handling' => 'Integration: AWS SES Event Handling',
                    'integration-google-analytics' => 'Integration: Google Analytics',
                    'integration-google-recaptcha' => 'Integration: Google reCAPTCHA',
                    'integration-microsoft-clarity' => 'Integration: Microsoft Clarity',
                    'integration-twilio' => 'Integration: Twilio',
                    default => (string) str($permission->name)
                        ->before('.')
                        ->headline(),
                };

                $groupId = $groups[$groupName] ?? null;

                if (blank($groupId)) {
                    $groupId = $groups[$groupName] = (string) Str::uuid();

                    DB::table('permission_groups')->insert([
                        'id' => $groupId,
                        'name' => $groupName,
                        'created_at' => now(),
                    ]);
                }

                DB::table('permissions')
                    ->where('id', $permission->id)
                    ->update([
                        'group_id' => $groupId,
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('permissions')
            ->update([
                'group_id' => null,
                'updated_at' => now(),
            ]);
    }
};
