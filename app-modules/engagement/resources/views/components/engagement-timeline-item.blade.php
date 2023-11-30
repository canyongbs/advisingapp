{{--
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
--}}
@php
    use App\Filament\Resources\UserResource;
    use Assist\Engagement\Enums\EngagementDeliveryMethod;
    use Assist\Engagement\Enums\EngagementDeliveryStatus;
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
                @foreach ($record->deliverables as $deliverable)
                    @if ($deliverable->channel === EngagementDeliveryMethod::Email)
                        <div class="relative">
                            <x-filament::icon
                                class="h-5 w-5 text-gray-400 dark:text-gray-100"
                                icon="heroicon-o-envelope"
                            />
                            @php
                                $emailStatusColor = match ($deliverable->delivery_status) {
                                    EngagementDeliveryStatus::Awaiting => 'text-yellow-500',
                                    EngagementDeliveryStatus::Successful => 'text-green-500',
                                    EngagementDeliveryStatus::Failed => 'text-red-500',
                                };

                                $emailStatusIcon = match ($deliverable->delivery_status) {
                                    EngagementDeliveryStatus::Awaiting => 'heroicon-s-clock',
                                    EngagementDeliveryStatus::Successful => 'heroicon-s-check-circle',
                                    EngagementDeliveryStatus::Failed => 'heroicon-s-exclamation-circle',
                                };
                            @endphp
                            <x-filament::icon
                                class="{{ $emailStatusColor }} absolute bottom-0 right-0 h-2 w-2"
                                icon="{{ $emailStatusIcon }}"
                            />
                        </div>
                    @endif
                    @if ($deliverable->channel === EngagementDeliveryMethod::Sms)
                        <div class="relative">
                            <x-filament::icon
                                class="h-5 w-5 text-gray-400 dark:text-gray-100"
                                icon="heroicon-o-chat-bubble-left"
                            />
                            @php
                                $smsStatusColor = match ($deliverable->delivery_status) {
                                    EngagementDeliveryStatus::Awaiting => 'text-yellow-500',
                                    EngagementDeliveryStatus::Successful => 'text-green-500',
                                    EngagementDeliveryStatus::Failed => 'text-red-500',
                                };

                                $smsStatusIcon = match ($deliverable->delivery_status) {
                                    EngagementDeliveryStatus::Awaiting => 'heroicon-s-clock',
                                    EngagementDeliveryStatus::Successful => 'heroicon-s-check-circle',
                                    EngagementDeliveryStatus::Failed => 'heroicon-s-exclamation-circle',
                                };
                            @endphp
                            <x-filament::icon
                                class="{{ $smsStatusColor }} absolute bottom-0 right-0 h-2 w-2"
                                icon="{{ $smsStatusIcon }}"
                            />
                        </div>
                    @endif
                @endforeach
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
            <p>{{ $record->getBody() }}</p>
        </div>
    </div>
</div>
