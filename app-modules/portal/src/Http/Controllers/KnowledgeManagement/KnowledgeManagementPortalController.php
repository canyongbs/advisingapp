<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Portal\Http\Controllers\KnowledgeManagement;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use AdvisingApp\Portal\Enums\PortalType;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use AdvisingApp\Portal\Settings\PortalSettings;
use AdvisingApp\Portal\Models\PortalAuthentication;
use AdvisingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AdvisingApp\Portal\Notifications\AuthenticatePortalNotification;
use AdvisingApp\StudentDataModel\Actions\ResolveEducatableFromEmail;
use AdvisingApp\Portal\DataTransferObjects\KnowledgeBaseCategoryData;
use AdvisingApp\Portal\Http\Requests\KnowledgeManagementPortalAuthenticationRequest;

class KnowledgeManagementPortalController extends Controller
{
    public function show(): JsonResponse
    {
        $settings = resolve(PortalSettings::class);

        return response()->json([
            'primary_color' => Color::all()[$settings->knowledge_management_portal_primary_color ?? 'blue'],
            'rounding' => $settings->knowledge_management_portal_rounding,
            // TODO If service management is enabled, we will provide an authentication URL
            'service_management_enabled' => $settings->knowledge_management_portal_service_management,
            'authentication_url' => URL::signedRoute(
                name: 'portal.knowledge-management.request-authentication',
                absolute: false,
            ),
            'categories' => KnowledgeBaseCategoryData::collection(
                KnowledgeBaseCategory::query()
                    ->get()
                    ->map(function ($category) {
                        return [
                            'id' => $category->getKey(),
                            'name' => $category->name,
                            'description' => $category->description,
                        ];
                    })
                    ->toArray()
            ),
        ]);
    }

    // TODO Extract to invokeable controller
    public function requestAuthentication(KnowledgeManagementPortalAuthenticationRequest $request, ResolveEducatableFromEmail $resolveEducatableFromEmail): JsonResponse
    {
        $email = $request->safe()->email;

        $educatable = $resolveEducatableFromEmail($email);

        if (! $educatable) {
            throw ValidationException::withMessages([
                'email' => 'A student with that email address could not be found. Please contact your system administrator.',
            ]);
        }

        $code = random_int(100000, 999999);

        $authentication = new PortalAuthentication();
        $authentication->educatable()->associate($educatable);
        $authentication->portal_type = PortalType::KnowledgeManagement;
        $authentication->code = Hash::make($code);
        $authentication->save();

        Notification::route('mail', [
            $email => $educatable->getAttributeValue($educatable::displayNameKey()),
        ])->notify(new AuthenticatePortalNotification($authentication, $code));

        return response()->json([
            'message' => "We've sent an authentication code to {$email}.",
            'authentication_url' => URL::signedRoute(
                name: 'kmp.authenticate',
                parameters: [
                    'authentication' => $authentication,
                ],
                absolute: false,
            ),
        ]);
    }

    // TODO Extract to invokeable controller
    public function authenticate(Request $request, PortalAuthentication $authentication): JsonResponse
    {
        ray('authenticate()', $authentication);

        if ($authentication->isExpired()) {
            return response()->json([
                'is_expired' => true,
            ]);
        }

        $request->validate([
            'code' => ['required', 'integer', 'digits:6', function (string $attribute, int $value, Closure $fail) use ($authentication) {
                if (Hash::check($value, $authentication->code)) {
                    return;
                }

                $fail('The provided code is invalid.');
            }],
        ]);

        $educatable = $authentication->educatable;

        // TODO Authenticate via the correct guard (can be student or prospect)
        Auth::guard('student')->login($educatable);

        $token = $educatable->createToken('knowledge-management-portal-access-token');

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return response()->json([
            'success' => true,
            'token' => $token->plainTextToken,
        ]);
    }
}
