<?php

namespace AdvisingApp\Authorization\Actions;

use AdvisingApp\Authorization\Models\OtpLoginCode;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class GenerateOtpLoginCode
{
    public function __invoke(User $user, CarbonInterface $expiresAt): string
    {
        // Invalidate any outstanding codes for this user (single active code).
        OtpLoginCode::query()
            ->whereBelongsTo($user)
            ->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $otpLoginCode = new OtpLoginCode();
        $otpLoginCode->code = $code; // hashed via the model cast
        $otpLoginCode->expires_at = Carbon::instance($expiresAt);
        $otpLoginCode->user()->associate($user);
        $otpLoginCode->save();

        return $code;
    }
}
