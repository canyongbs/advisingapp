<?php

namespace AdvisingApp\Authorization\Actions;

use AdvisingApp\Authorization\Models\OtpLoginCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClaimOtpLoginCode
{
    public function __invoke(User $user, string $code): bool
    {
        $records = OtpLoginCode::query()
            ->whereBelongsTo($user)
            ->whereNull('used_at')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->where('created_at', '>', now()->subMinutes(20))
                ->orWhere('expires_at', '>', now()))
            ->get();

        foreach ($records as $record) {
            if (Hash::check($code, $record->code)) {
                $record->used_at = now();
                $record->save();

                return true;
            }
        }

        return false;
    }
}
