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

namespace AdvisingApp\Form\Http\Controllers;

use AdvisingApp\Form\Actions\StoreSecret;
use AdvisingApp\Form\Http\Requests\StoreSecretRequest;
use App\Features\PasswordFormFieldFeature;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use SensitiveParameter;
use Symfony\Component\HttpFoundation\Response;

class StoreSecretController extends Controller
{
    public function __invoke(#[SensitiveParameter] StoreSecretRequest $request, StoreSecret $storeSecret): JsonResponse
    {
        abort_unless(PasswordFormFieldFeature::active(), Response::HTTP_NOT_FOUND);

        $value = $request->validated('value');
        $previousSecretId = $request->validated('previous_secret_id');

        assert(is_string($value) || is_null($value));
        assert(is_string($previousSecretId) || is_null($previousSecretId));

        $secret = $storeSecret($value, $previousSecretId);

        return response()->json([
            'id' => $secret?->getKey(),
        ]);
    }
}
