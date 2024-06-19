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
    use App\Filament\Resources\UserResource;
    use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;
    use AdvisingApp\Engagement\Enums\EngagementDeliveryStatus;
@endphp

<div>
    <div class="flex flex-row justify-between">
        <h3 class="mb-1 flex items-center text-lg font-semibold text-gray-500 dark:text-gray-100">
            <a
                class="font-medium underline"
                href="{{ UserResource::getUrl('view', ['record' => $record->createdBy]) }}"
            >
                {{ $record->createdBy->name }}
            </a>
            <span class="ml-2 flex space-x-2">
                @php
                    $deliverable = $record->deliverable;
                @endphp

                @if ($deliverable->channel === EngagementDeliveryMethod::Email)
                    <div class="relative">
                        <x-filament::icon
                            class="h-5 w-5 text-gray-400 dark:text-gray-100"
                            icon="heroicon-o-envelope"
                        />
                        <x-filament::icon
                            class="{{ $deliverable->delivery_status->getTextColorClass() }} absolute bottom-0 right-0 h-2 w-2"
                            icon="{{ $deliverable->delivery_status->getIconClass() }}"
                        />
                    </div>
                @endif
                @if ($deliverable->channel === EngagementDeliveryMethod::Sms)
                    <div class="relative">
                        <x-filament::icon
                            class="h-5 w-5 text-gray-400 dark:text-gray-100"
                            icon="heroicon-o-chat-bubble-left"
                        />
                        <x-filament::icon
                            class="{{ $deliverable->delivery_status->getTextColorClass() }} absolute bottom-0 right-0 h-2 w-2"
                            icon="{{ $deliverable->delivery_status->getIconClass() }}"
                        />
                    </div>
                @endif
            </span>
        </h3>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
        Sent {{ $record->deliver_at->diffForHumans() }}
    </time>

    <div
        class="my-4 rounded-lg border-2 border-gray-200 p-2 text-base font-normal text-gray-500 dark:border-gray-800 dark:text-gray-400">
        @if (!blank($record->subject))
            <div class="mb-2 flex flex-col">
                <p class="text-xs text-gray-400 dark:text-gray-500">Subject:</p>
                <p>{{ $record->subject }}</p>
            </div>
        @endif
        <div class="flex flex-col">
            <p class="text-xs text-gray-400 dark:text-gray-500">Body:</p>
            <div class="prose dark:prose-invert">
                {{ $record->getBody() }}
            </div>
        </div>
    </div>
</div>
