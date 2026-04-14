<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Authorization\Http\Controllers;

use AdvisingApp\Authorization\Models\OtpLoginCode;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class VerifyOtpLoginCodeController
{
    /**
     * @throws Throwable
     */
    public function __invoke(Request $request, OtpLoginCode $otpCode): RedirectResponse|Response
    {
        if ($request->getMethod() === 'HEAD') {
            // Protection against link scanning bots, like Microsoft Outlook.
            return response()->noContent();
        }

        abort_if(
            boolean: now()->greaterThanOrEqualTo($otpCode->created_at->addMinutes(20))
                || $otpCode->used_at !== null,
            code: 403,
            message: 'This OTP code has already been used or has expired. Please request a new one.'
        );

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        if (! Hash::check($request->input('code'), $otpCode->code)) {
            return back()->withErrors([
                'code' => 'The OTP code you entered is incorrect. Please try again.',
            ]);
        }

        $otpCode->used_at = now();
        $otpCode->saveOrFail();

        $user = $otpCode->user;

        $panel = Filament::getPanel('admin');

        Auth::guard($panel->getAuthGuard())->login($user);

        return redirect()->to($panel->getHomeUrl());
    }
}
