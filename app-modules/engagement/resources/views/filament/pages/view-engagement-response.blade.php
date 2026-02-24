{{--
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
--}}
@use(AdvisingApp\Engagement\Models\Engagement)
@use(AdvisingApp\Prospect\Models\Prospect)
@use(AdvisingApp\StudentDataModel\Models\Student)
@use(AdvisingApp\Engagement\Enums\EngagementResponseType)

<x-filament-panels::page>
    <div>
        <x-filament::link :href="\AdvisingApp\Engagement\Filament\Pages\Inbox::getUrl()" icon="heroicon-m-arrow-left">
            Back to Inbox
        </x-filament::link>
    </div>

    {{ $this->infolist }}

    @if (

        ($this->record->sender instanceof Prospect || $this->record->sender instanceof Student) &&
        auth()
            ->user()
            ->can('create', [Engagement::class, $this->record->sender instanceof Prospect ? $this->record->sender : null])    )
        <div class="grid gap-6" x-data="{ isReplying: false }">
            <div class="flex items-center gap-3">
                <x-filament::button icon="heroicon-s-arrow-uturn-left" x-on:click="isReplying = !isReplying">
                    Reply
                </x-filament::button>

                <x-filament::button wire:click="changeStatus()">
                    Mark as {{ $this->getInvertedStatus()->name }}
                </x-filament::button>
            </div>

            <form class="grid gap-6" x-show="isReplying" wire:submit.prevent="reply">
                {{ $this->replyForm }}

                <div>
                    <x-filament::button type="submit" wire:target="reply" icon="heroicon-m-paper-airplane">
                        Send Reply
                    </x-filament::button>
                </div>
            </form>
        </div>
    @else
        <div class="grid gap-6">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1">
                    <x-filament::button icon="heroicon-s-arrow-uturn-left" disabled>Reply</x-filament::button>
                    <x-filament::icon-button
                        icon="heroicon-m-question-mark-circle"
                        :tooltip="$this->record->type === EngagementResponseType::Sms
                            ? 'The phone number this text message was sent from does not match any known student or prospect record. You may only reply to messages that have an associated prospect or student record.'
                        : 'The email address this message was sent from does not match any known student or prospect record. You may only reply to messages that have an associated prospect or student record.'"
                    />
                </div>
                <x-filament::button wire:click="changeStatus()">
                    Mark as {{ $this->getInvertedStatus()->name }}
                </x-filament::button>
            </div>
        </div>
    @endif
</x-filament-panels::page>
