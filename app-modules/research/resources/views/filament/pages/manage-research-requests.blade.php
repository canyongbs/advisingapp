{{--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
    use AdvisingApp\Research\Filament\Pages\NewResearchRequest;
    use Filament\Support\Enums\ActionSize;
@endphp

<div class="h-[calc(100dvh-4rem)]">
    @if ($this->isConsented && $this->request)
        @capture($sidebarContent)
            <div class="flex select-none flex-col gap-y-2">
                <div class="relative">
                    <div class="flex flex-col gap-2">
                        <div class="grid w-full grid-cols-2 gap-2">
                            <x-filament::button
                                icon="heroicon-m-plus"
                                tag="a"
                                :href="NewResearchRequest::getUrl()"
                            >
                                New Research Request
                            </x-filament::button>
                        </div>
                    </div>
                </div>

                <template x-if="$wire.requestsWithoutAFolder.length">
                    <ul
                        class="flex flex-col gap-y-1 rounded-xl border border-gray-950/5 bg-white p-2 shadow-sm dark:border-white/10 dark:bg-gray-900"
                        id="folder-{{ null }}"
                        x-on:drop.prevent="drop('{{ null }}')"
                        x-on:dragenter.prevent
                        x-on:dragover.prevent
                    >
                        <template
                            x-for="request in $wire.requestsWithoutAFolder"
                            :key="request.id"
                        >
                            <li
                                :id="`request-${request.id}`"
                                :class="{
                                    'px-2 group flex rounded-lg w-full items-center outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 space-x-1': true,
                                    'bg-gray-100 dark:bg-white/5': request.id === $wire.request?.id
                                }"
                            >
                                <div class="flex flex-1 items-center gap-3">
                                    <template x-if="$wire.folders.length">
                                        <button
                                            type="button"
                                            draggable="true"
                                            x-on:dragstart="start(request.id, null)"
                                            x-on:dragend="end"
                                            :class="{
                                                'flex items-center cursor-move': true,
                                                'text-gray-700 dark:text-gray-200': request.id !== $wire.request?.id,
                                                'text-primary-600 dark:text-primary-400': request.id === $wire.request?.id
                                            }"
                                        >
                                            <template x-if="loading.type !== 'request' || loading.identifier !== request.id">
                                                <x-heroicon-m-bars-2 class="h-5 w-5" />
                                            </template>

                                            <template x-if="loading.type === 'request' && loading.identifier === request.id">
                                                <x-filament::loading-indicator class="h-5 w-5" />
                                            </template>
                                        </button>
                                    </template>

                                    <button
                                        class="relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-left text-sm"
                                        type="button"
                                        x-on:click="selectRequest(request)"
                                    >
                                        <span
                                            x-text="request.title"
                                            :class="{
                                                'flex-1 truncate': true,
                                                'text-gray-700 dark:text-gray-200': request.id !== $wire
                                                    .selectedRequestId,
                                                'text-primary-600 dark:text-primary-400': request.id === $wire
                                                    .selectedRequestId
                                            }"
                                        >
                                        </span>
                                    </button>
                                </div>

                                <div class="flex items-center gap-1">
                                    <template
                                        x-if="loading.type !== 'moveRequestAction' || loading.identifier !== request.id">
                                        <x-filament::icon-button
                                            class="relative hidden h-5 w-5 group-hover:inline-flex"
                                            icon="heroicon-m-arrow-down-on-square"
                                            x-on:click="moveRequest(request.id)"
                                            label="Move request to a different folder"
                                            color="warning"
                                            size="{{ ActionSize::ExtraSmall }}"
                                        />
                                    </template>
                                    <template
                                        x-if="loading.type === 'moveRequestAction' && loading.identifier === request.id">
                                        <x-filament::loading-indicator
                                            class="relative hidden h-5 w-5 group-hover:inline-flex"
                                        />
                                    </template>

                                    <template
                                        x-if="loading.type !== 'editRequestAction' || loading.identifier !== request.id">
                                        <x-filament::icon-button
                                            class="relative hidden h-5 w-5 group-hover:inline-flex"
                                            icon="heroicon-m-pencil"
                                            x-on:click="editRequest(request.id)"
                                            label="Edit name of the request"
                                            color="warning"
                                            size="{{ ActionSize::ExtraSmall }}"
                                        />
                                    </template>
                                    <template
                                        x-if="loading.type === 'editRequestAction' && loading.identifier === request.id">
                                        <x-filament::loading-indicator
                                            class="relative hidden h-5 w-5 group-hover:inline-flex"
                                        />
                                    </template>

                                    <template
                                        x-if="loading.type !== 'deleteRequestAction' || loading.identifier !== request.id"
                                    >
                                        <x-filament::icon-button
                                            class="relative hidden h-5 w-5 group-hover:inline-flex"
                                            icon="heroicon-m-trash"
                                            x-on:click="deleteRequest(request.id)"
                                            label="Delete the request"
                                            color="danger"
                                            size="{{ ActionSize::ExtraSmall }}"
                                        />
                                    </template>
                                    <template
                                        x-if="loading.type === 'deleteRequestAction' && loading.identifier === request.id"
                                    >
                                        <x-filament::loading-indicator
                                            class="relative hidden h-5 w-5 group-hover:inline-flex"
                                        />
                                    </template>
                                </div>
                            </li>
                        </template>
                    </ul>
                </template>
                <template x-if="!$wire.requestsWithoutAFolder.length">
                    <div
                        class="flex flex-col gap-y-1 rounded-xl border border-dashed border-gray-950/5 bg-white px-3 py-2 text-gray-500 shadow-sm dark:border-white/10 dark:bg-gray-900"
                        x-show="dragging"
                        x-on:drop.prevent="drop('{{ null }}')"
                        x-on:dragenter.prevent
                        x-on:dragover.prevent
                    >
                        <div class="text-sm">
                            Drag requests here to move them out of a folder
                        </div>
                    </div>
                </template>
                <template x-if="$wire.folders.length">
                    <div
                        class="flex flex-col gap-y-3 rounded-xl border border-gray-950/5 bg-white p-2 shadow-sm dark:border-white/10 dark:bg-gray-900">
                        <template
                            x-for="folder in $wire.folders"
                            :key="folder.id"
                        >
                            <ul
                                class="flex flex-col gap-y-1"
                                :id="`folder-${folder.id}`"
                                x-on:drop.prevent="drop(folder.id)"
                                x-on:dragenter.prevent
                                x-on:dragover.prevent
                            >
                                <span
                                    class="group flex w-full cursor-move items-center rounded-lg px-2 outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5"
                                >
                                    <x-filament::icon-button
                                        icon="heroicon-o-folder-open"
                                        x-show="expanded(folder.id)"
                                        x-on:click="expand(folder.id)"
                                    />
                                    <x-filament::icon-button
                                        icon="heroicon-o-folder"
                                        x-show="! expanded(folder.id)"
                                        x-on:click="expand(folder.id)"
                                    />

                                    <div
                                        class="group flex w-full cursor-pointer items-center space-x-1 rounded-lg px-2 outline-none transition duration-75 focus:bg-gray-100 dark:focus:bg-white/5">
                                        <div
                                            class="relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-sm"
                                            x-on:click="expand(folder.id)"
                                        >
                                            <div
                                                class="flex-1 truncate"
                                                x-bind:class="{
                                                    'text-primary-600 dark:text-primary-400': expanded(folder.id)
                                                }"
                                                x-text="`${folder.name} ${folder.requests.length ? '(' + folder.requests.length + ')' : ''}`"
                                            ></div>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            <template
                                                x-if="loading.type !== 'renameFolderAction' || loading.identifier !== folder.id"
                                            >
                                                <x-filament::icon-button
                                                    class="relative hidden h-5 w-5 group-hover:inline-flex"
                                                    icon="heroicon-m-pencil"
                                                    x-on:click="renameFolder(folder.id)"
                                                    label="Rename Folder"
                                                    color="warning"
                                                    size="{{ ActionSize::ExtraSmall }}"
                                                />
                                            </template>
                                            <template
                                                x-if="loading.type === 'renameFolderAction' && loading.identifier === folder.id"
                                            >
                                                <x-filament::loading-indicator
                                                    class="relative hidden h-5 w-5 group-hover:inline-flex"
                                                />
                                            </template>

                                            <template
                                                x-if="loading.type !== 'deleteFolderAction' || loading.identifier !== folder.id"
                                            >
                                                <x-filament::icon-button
                                                    class="relative hidden h-5 w-5 group-hover:inline-flex"
                                                    icon="heroicon-m-trash"
                                                    x-on:click="deleteFolder(folder.id)"
                                                    label="Delete Folder"
                                                    color="danger"
                                                    size="{{ ActionSize::ExtraSmall }}"
                                                />
                                            </template>
                                            <template
                                                x-if="loading.type === 'deleteFolderAction' && loading.identifier === folder.id"
                                            >
                                                <x-filament::loading-indicator
                                                    class="relative hidden h-5 w-5 group-hover:inline-flex"
                                                />
                                            </template>
                                        </div>
                                    </div>
                                </span>
                                <template
                                    x-for="request in folder.requests"
                                    :key="request.id"
                                >
                                    <li
                                        :id="`request-${request.id}`"
                                        x-show="expanded(folder.id)"
                                        :class="{
                                            'px-2 group flex rounded-lg w-full items-center outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5 space-x-1': true,
                                            'bg-gray-100 dark:bg-white/5': request.id === $wire.request?.id
                                        }"
                                    >
                                        <div class="flex flex-1 items-center gap-3">
                                            <button
                                                type="button"
                                                draggable="true"
                                                x-on:dragstart="start(request.id, folder.id)"
                                                x-on:dragend="end"
                                                :class="{
                                                    'flex items-center cursor-move': true,
                                                    'text-gray-700 dark:text-gray-200': request.id !== $wire.request?.id,
                                                    'text-primary-600 dark:text-primary-400': request.id === $wire.request
                                                        ?.id
                                                }"
                                            >
                                                <template
                                                    x-if="loading.type !== 'request' || loading.identifier !== request.id"
                                                >
                                                    <x-heroicon-m-bars-2 class="h-5 w-5" />
                                                </template>

                                                <template
                                                    x-if="loading.type === 'request' && loading.identifier === request.id"
                                                >
                                                    <x-filament::loading-indicator class="h-5 w-5" />
                                                </template>
                                            </button>

                                            <button
                                                class="relative flex flex-1 items-center justify-center gap-x-3 rounded-lg py-2 text-left text-sm"
                                                type="button"
                                                x-on:click="selectRequest(request)"
                                            >
                                                <span
                                                    x-text="request.title"
                                                    :class="{
                                                        'flex-1 truncate': true,
                                                        'text-gray-700 dark:text-gray-200': request.id !== $wire
                                                            .selectedRequestId,
                                                        'text-primary-600 dark:text-primary-400': request.id === $wire
                                                            .selectedRequestId
                                                    }"
                                                >
                                                </span>
                                            </button>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            <template
                                                x-if="loading.type !== 'moveRequestAction' || loading.identifier !== request.id"
                                            >
                                                <x-filament::icon-button
                                                    class="relative hidden h-5 w-5 group-hover:inline-flex"
                                                    icon="heroicon-m-arrow-down-on-square"
                                                    x-on:click="moveRequest(request.id)"
                                                    label="Move request to a different folder"
                                                    color="warning"
                                                    size="{{ ActionSize::ExtraSmall }}"
                                                />
                                            </template>
                                            <template
                                                x-if="loading.type === 'moveRequestAction' && loading.identifier === request.id"
                                            >
                                                <x-filament::loading-indicator
                                                    class="relative hidden h-5 w-5 group-hover:inline-flex"
                                                />
                                            </template>

                                            <template
                                                x-if="loading.type !== 'editRequestAction' || loading.identifier !== request.id"
                                            >
                                                <x-filament::icon-button
                                                    class="relative hidden h-5 w-5 group-hover:inline-flex"
                                                    icon="heroicon-m-pencil"
                                                    x-on:click="editRequest(request.id)"
                                                    label="Edit title of the request"
                                                    color="warning"
                                                    size="{{ ActionSize::ExtraSmall }}"
                                                />
                                            </template>
                                            <template
                                                x-if="loading.type === 'editRequestAction' && loading.identifier === request.id"
                                            >
                                                <x-filament::loading-indicator
                                                    class="relative hidden h-5 w-5 group-hover:inline-flex"
                                                />
                                            </template>

                                            <template
                                                x-if="loading.type !== 'deleteRequestAction' || loading.identifier !== request.id"
                                            >
                                                <x-filament::icon-button
                                                    class="relative hidden h-5 w-5 group-hover:inline-flex"
                                                    icon="heroicon-m-trash"
                                                    x-on:click="deleteRequest(request.id)"
                                                    label="Delete the request"
                                                    color="danger"
                                                    size="{{ ActionSize::ExtraSmall }}"
                                                />
                                            </template>
                                            <template
                                                x-if="loading.type === 'deleteRequestAction' && loading.identifier === request.id"
                                            >
                                                <x-filament::loading-indicator
                                                    class="relative hidden h-5 w-5 group-hover:inline-flex"
                                                />
                                            </template>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>
                </template>
            </div>
        @endcapture

        <div
            class="grid h-full flex-1 grid-cols-1 grid-rows-[1fr_auto] gap-2 lg:grid-cols-3 lg:gap-x-6 lg:gap-y-4 2xl:grid-cols-4"
            x-data="requests($wire)"
        >
            <div class="col-span-1 hidden overflow-y-auto px-px pt-3 lg:block lg:pt-6">
                {{ $sidebarContent() }}
            </div>

            <div
                class="col-span-1 flex flex-col gap-2 overflow-hidden pt-3 lg:col-span-2 lg:pt-6 2xl:col-span-3"
                wire:key="request{{ $this->request->id }}"
            >
                Content
            </div>

            <div class="col-span-full hidden md:block">
                <x-footer />
            </div>

            <div class="col-span-full mb-2 text-center text-xs md:hidden">
                © 2016-{{ date('Y') }} <a
                    class="text-blue-600 underline dark:text-blue-400"
                    href="https://canyongbs.com/"
                >Canyon GBS LLC</a>.
            </div>
        </div>
    @elseif (!$this->isConsented)
        <div x-init="$nextTick(() => $dispatch('open-modal', { id: 'consent-agreement' }))">
            <x-filament::modal
                id="consent-agreement"
                width="5xl"
                alignment="left"
                :close-by-clicking-away="false"
                :close-button="false"
            >
                <x-slot name="header">
                    <h2 class="text-left text-xl font-semibold text-gray-950 dark:text-white">
                        {{ $this->consentAgreement->title }}
                    </h2>
                </x-slot>

                <div class="prose max-w-none text-left dark:prose-invert">
                    {{ str($this->consentAgreement->description)->markdown()->sanitizeHtml()->toHtmlString() }}
                </div>

                <x-filament::section>
                    <div class="prose max-w-none text-left text-[.7rem] leading-4 dark:prose-invert">
                        {{ str($this->consentAgreement->body)->markdown()->sanitizeHtml()->toHtmlString() }}
                    </div>
                </x-filament::section>

                <x-slot name="footer">
                    <form
                        class="flex w-full flex-col gap-6"
                        wire:submit="confirmConsent"
                    >
                        <label>
                            <x-filament::input.checkbox required />

                            <span class="ml-2 text-sm font-medium">
                                I agree to the terms and conditions
                            </span>
                        </label>

                        <div class="flex justify-start gap-3">
                            <x-filament::button
                                wire:click="denyConsent"
                                outlined
                            >
                                Cancel
                            </x-filament::button>
                            <x-filament::button type="submit">
                                Continue
                            </x-filament::button>
                        </div>
                    </form>
                </x-slot>
            </x-filament::modal>
        </div>
    @elseif (!$this->request)
        <div
            class="flex h-full w-full items-center justify-center"
            wire:init="loadFirstRequest"
        >
            <x-filament::loading-indicator class="h-12 w-12" />
        </div>
    @endif

    <script src="{{ url('js/canyon-gbs/research/requests.js') . '?v=' . app('current-commit') }}"></script>
    <style>
        .footer {
            display: none
        }
    </style>

    <x-filament-actions::modals />
</div>
