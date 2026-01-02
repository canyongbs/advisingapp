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

namespace AdvisingApp\Ai\Http\Controllers\QnaAdvisors;

use AdvisingApp\Ai\Http\Controllers\QnaAdvisors\Concerns\CanGenerateAndDispatchQnaAdvisorWidgetAuthentications;
use AdvisingApp\Ai\Http\Requests\QnaAdvisors\RegisterProspectRequest;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Prospect\Enums\SystemProspectClassification;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RegisterProspectController
{
    use CanGenerateAndDispatchQnaAdvisorWidgetAuthentications;

    public function __invoke(RegisterProspectRequest $request, QnaAdvisor $advisor): JsonResponse
    {
        $data = $request->validated();

        $prospect = DB::transaction(function () use ($data): Prospect {
            $prospect = Prospect::query()
                ->make([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'preferred' => $data['preferred'] ?? null,
                    'full_name' => "{$data['first_name']} {$data['last_name']}",
                    'birthdate' => $data['birthdate'] ?? null,
                ]);

            $status = ProspectStatus::query()
                ->where('classification', SystemProspectClassification::New)
                ->first();

            if ($status) {
                $prospect->status()->associate($status);
            }

            $source = ProspectSource::query()
                ->where('name', 'Advising App')
                ->first();

            if ($source) {
                $prospect->source()->associate($source);
            }

            $prospect->save();

            $emailAddress = $prospect->emailAddresses()->create([
                'address' => $data['email'],
            ]);
            $prospect->primaryEmailAddress()->associate($emailAddress);

            $phoneNumber = $prospect->phoneNumbers()->create([
                'number' => $data['mobile'],
                'type' => 'Mobile',
                'can_receive_sms' => true,
            ]);
            $prospect->primaryPhoneNumber()->associate($phoneNumber);

            if (isset($data['address'])) {
                $address = $prospect->addresses()->create([
                    'line_1' => $data['address'],
                    'line_2' => $data['address_2'] ?? null,
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'postal' => $data['postal'],
                    'type' => 'Home',
                ]);
                $prospect->primaryAddress()->associate($address);
            }

            $prospect->save();

            return $prospect;
        });

        return response()->json([
            'message' => "We've sent an authentication code to {$prospect->primaryEmailAddress->address}.",
            'authentication_url' => $this->createPortalAuthentication($prospect, $advisor),
        ]);
    }
}
