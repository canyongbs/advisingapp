{{--
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
--}}
@php
    use Carbon\Carbon;
    use Laravel\Pennant\Feature;
    use AdvisingApp\Division\Models\Division;
    use AdvisingApp\Campaign\Settings\CampaignSettings;
    use AdvisingApp\Interaction\Models\InteractionType;
    use AdvisingApp\Interaction\Models\InteractionStatus;
    use AdvisingApp\Interaction\Models\InteractionDriver;
    use App\Features\EnableInteractionInitiativesFeature;
    use AdvisingApp\Interaction\Models\InteractionOutcome;
    use AdvisingApp\Interaction\Models\InteractionCampaign;
    use AdvisingApp\Interaction\Models\InteractionRelation;
    use AdvisingApp\Interaction\Models\InteractionInitiative;
@endphp

<x-filament::fieldset>
    <x-slot name="label">
        Interaction
    </x-slot>

    <dl class="max-w-md divide-y divide-gray-200 text-gray-900 dark:divide-gray-700 dark:text-white">
        @if (Feature::active(EnableInteractionInitiativesFeature::class))
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Initiative</dt>
                <dd class="text-sm font-semibold">
                    {{ InteractionInitiative::find($action['interaction_initiative_id'])?->name }}</dd>
            </div>
        @else
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Initiative</dt>
                <dd class="text-sm font-semibold">
                    {{ InteractionCampaign::find($action['interaction_campaign_id'])?->name }}</dd>
            </div>
        @endif
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Driver</dt>
            <dd class="text-sm font-semibold">{{ InteractionDriver::find($action['interaction_driver_id'])?->name }}
            </dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Division</dt>
            <dd class="text-sm font-semibold">{{ Division::find($action['division_id'])?->name }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Outcome</dt>
            <dd class="text-sm font-semibold">{{ InteractionOutcome::find($action['interaction_outcome_id'])?->name }}
            </dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Relation</dt>
            <dd class="text-sm font-semibold">{{ InteractionRelation::find($action['interaction_relation_id'])?->name }}
            </dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Status</dt>
            <dd class="text-sm font-semibold">{{ InteractionStatus::find($action['interaction_status_id'])?->name }}
            </dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Type</dt>
            <dd class="text-sm font-semibold">{{ InteractionType::find($action['interaction_type_id'])?->name }}
            </dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Start Time</dt>
            <dd class="text-sm font-semibold">{{ Carbon::parse($action['start_datetime'])->format('M j, Y H:i:s') }}
            </dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">End Time</dt>
            <dd class="text-sm font-semibold">{{ Carbon::parse($action['end_datetime'])->format('M j, Y H:i:s') }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Subject</dt>
            <dd class="text-sm font-semibold">{{ $action['subject'] }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Description</dt>
            <dd class="text-sm font-semibold">{{ $action['description'] }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Execute At</dt>
            <dd class="text-sm font-semibold">{{ Carbon::parse($action['execute_at'])->format('M j, Y H:i:s') }}
                {{ app(CampaignSettings::class)->getActionExecutionTimezoneLabel() }}</dd>
        </div>
    </dl>

</x-filament::fieldset>
