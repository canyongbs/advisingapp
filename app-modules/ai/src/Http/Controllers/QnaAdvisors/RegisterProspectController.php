<?php

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
                    'preferred' => $data['preferred'],
                    'full_name' => "{$data['first_name']} {$data['last_name']}",
                    'birthdate' => $data['birthdate'],
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

            $address = $prospect->addresses()->create([
                'line_1' => $data['address'],
                'line_2' => $data['address_2'],
                'city' => $data['city'],
                'state' => $data['state'],
                'postal' => $data['postal'],
                'type' => 'Home',
            ]);
            $prospect->primaryAddress()->associate($address);

            $prospect->save();

            return $prospect;
        });

        return response()->json([
            'message' => "We've sent an authentication code to {$prospect->primaryEmailAddress->address}.",
            'authentication_url' => $this->createPortalAuthentication($prospect, $advisor),
        ]);
    }
}
