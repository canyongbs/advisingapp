{{--
<COPYRIGHT>

    Copyright © 2022-2024, Canyon GBS LLC. All rights reserved.

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

<div>
    <div class="flex flex-row justify-between">
        <x-timeline::timeline.heading>
            <div class="flex">
                Maintenance Activity Scheduled - {{ $record->scheduled_date->format('M, d Y g:i A') }}

                <span class="ml-2 flex">
                    @if ($record->isCompleted())
                        <x-filament::badge color="success">
                            {{ $record->status->getLabel() }} {{ $record->completed_date?->format('M, d Y g:i A') }}
                        </x-filament::badge>
                    @else
                        <x-filament::badge>
                            {{ $record->status->getLabel() }}
                        </x-filament::badge>
                    @endif
                </span>
            </div>

        </x-timeline::timeline.heading>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    <x-timeline::timeline.time>
        Created {{ $record->created_at->diffForHumans() }}
    </x-timeline::timeline.time>

    <div class="mt-4 flex flex-col space-y-2">
        <x-timeline::timeline.labeled-field>
            <x-slot:label>
                Details
            </x-slot:label>

            {{ $record->details }}
        </x-timeline::timeline.labeled-field>
    </div>

    @if ($record->notes)
        <x-timeline::timeline.content>
            {{ $record->notes }}
        </x-timeline::timeline.content>
    @endif

</div>
