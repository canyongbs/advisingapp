<?php

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
