<?php

use Filament\Support\Facades\FilamentAsset;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;

?>

<x-filament-panels::page
    class="max-h-screen"
    full-height="true"
>
    <div
        class="flex h-full flex-col"
        wire:init="determineIfConsentWasGiven"
    >
        @if ($consentedToTerms === true && $loading === false)
            <div class="grid flex-1 grid-cols-1 gap-6 md:grid-cols-4">
                <div class="col-span-1">
                    <div
                        class="hidden rounded-xl bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:block">
                        <li class="fi-sidebar-group flex flex-col gap-y-1">
                            <x-filament::button
                                icon="heroicon-m-plus"
                                wire:click="newChat"
                            >
                                {{ __('New Chat') }}
                            </x-filament::button>
                            <ul class="fi-sidebar-group-items flex flex-col gap-y-1">
                                @foreach ($chats as $chatItem)
                                    <li @class([
                                        'fi-sidebar-item cursor-pointer',
                                        'fi-active fi-sidebar-item-active' => $chat->id === $chatItem->id,
                                    ])>
                                        <a
                                            @class([
                                                'fi-sidebar-item-button relative flex items-center justify-center gap-x-3 rounded-lg px-2 py-2 text-sm outline-none transition duration-75 hover:bg-gray-100 focus:bg-gray-100 dark:hover:bg-white/5 dark:focus:bg-white/5',
                                                'bg-gray-100 dark:bg-white/5' => $chat->id === $chatItem->id,
                                            ])
                                            wire:click="selectChat('{{ $chatItem->id }}')"
                                        >
                                            <span @class([
                                                'fi-sidebar-item-label flex-1 truncate',
                                                'text-gray-700 dark:text-gray-200' => !$chat->id === $chatItem->id,
                                                'text-primary-600 dark:text-primary-400' => $chat->id === $chatItem->id,
                                            ])>
                                                {{ $chatItem->id }}
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </div>
                </div>

                <div class="col-span-1 flex h-full flex-col overflow-hidden md:col-span-3">
                    <div
                        class="flex max-h-[calc(100vh-23rem)] flex-1 flex-col-reverse overflow-y-scroll text-sm dark:bg-gray-800">
                        <div>
                            @foreach ($chat->messages as $message)
                                @switch($message->from)
                                    @case(AIChatMessageFrom::Assistant)
                                        <div
                                            class="text-token-text-primary group w-full border-b border-black/10 bg-gray-50 dark:border-gray-900/50 dark:bg-[#444654]"
                                            data-testid="conversation-turn-3"
                                            style="--avatar-color: #19c37d;"
                                        >
                                            <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                                <div
                                                    class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                    <div class="relative flex flex-shrink-0 flex-col items-end">
                                                        <div>
                                                            <img
                                                                class="relative flex h-12 w-12 items-center justify-center rounded-sm p-1 text-white"
                                                                src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/canyon-ai-headshot.jpg') }}"
                                                                alt="AI Assistant avatar"
                                                            >
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="agent-turn relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                                        <div class="flex max-w-full flex-grow flex-col gap-3">
                                                            <div
                                                                class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                                <div class="prose dark:prose-invert">
                                                                    {!! str($message->message)->markdown()->sanitizeHtml() !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex justify-between empty:hidden lg:block">
                                                            <div
                                                                class="visible mt-2 flex justify-center gap-2 self-end text-gray-400 md:gap-3 lg:absolute lg:right-0 lg:top-0 lg:mt-0 lg:translate-x-full lg:gap-1 lg:self-center lg:pl-2">
                                                                <x-filament::icon
                                                                    class="ml-auto flex h-6 w-6 cursor-pointer items-center gap-2 rounded-md p-1 text-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 disabled:dark:hover:text-gray-400"
                                                                    icon="heroicon-o-clipboard"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @break

                                    @case(AIChatMessageFrom::User)

                                        @default
                                            <div
                                                class="text-token-text-primary group w-full border-b border-black/10 dark:border-gray-900/50 dark:bg-gray-800">
                                                <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                                    <div
                                                        class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                        <div class="relative flex flex-shrink-0 flex-col items-end">
                                                            <div>
                                                                <img
                                                                    class="relative flex h-12 w-12 items-center justify-center rounded-sm p-1 text-white"
                                                                    src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/neutral-profile.jpg') }}"
                                                                    alt="User avatar"
                                                                >
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                                            <div class="flex max-w-full flex-grow flex-col gap-3">
                                                                <div
                                                                    class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                                    <div>{{ $message->message }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @break
                                    @endswitch
                                @endforeach

                                @if ($showCurrentResponse)
                                    <div
                                        class="text-token-text-primary group w-full border-b border-black/10 bg-gray-50 dark:border-gray-900/50 dark:bg-[#444654]"
                                        style="--avatar-color: #19c37d;"
                                        x-data="currentResponseData"
                                    >
                                        <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                            <div
                                                class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                <div class="relative flex flex-shrink-0 flex-col items-end">
                                                    <div>
                                                        <div
                                                            class="relative flex h-9 w-9 items-center justify-center rounded-sm p-1 text-white"
                                                            style="background-color: rgb(25, 195, 125); width: 36px; height: 36px;"
                                                        ><svg
                                                                class="icon-md"
                                                                role="img"
                                                                width="41"
                                                                height="41"
                                                                viewBox="0 0 41 41"
                                                                fill="none"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                            ><text
                                                                    x="-9999"
                                                                    y="-9999"
                                                                >ChatGPT</text>
                                                                <path
                                                                    d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z"
                                                                    fill="currentColor"
                                                                ></path>
                                                            </svg></div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="agent-turn relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                                    <div class="flex max-w-full flex-grow flex-col gap-3">
                                                        <div
                                                            class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                            <div
                                                                class="hidden"
                                                                id="hidden_current_response"
                                                                wire:stream="currentResponse"
                                                            >{{ $currentResponse }}</div>
                                                            <div
                                                                class="markdown light prose w-full break-words dark:prose-invert"
                                                                id="current_response"
                                                            ></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($renderError)
                                    <div class="my-4 w-full rounded-lg bg-gray-200 p-4 sm:p-6 lg:px-8">
                                        <h1 class="text-2xl font-bold text-red-400">Something went wrong</h1>
                                        <p class="text-black">{{ $error }}</p>
                                    </div>

                                    <div
                                        class="text-token-text-primary group w-full border-b border-black/10 bg-gray-50 dark:border-gray-900/50 dark:bg-[#444654]"
                                        data-testid="conversation-turn-3"
                                        style="--avatar-color: #19c37d;"
                                    >
                                        <div class="m-auto justify-center p-4 text-base md:gap-6 md:py-6">
                                            <div
                                                class="mx-auto flex flex-1 gap-4 text-base md:max-w-2xl md:gap-6 lg:max-w-[38rem] xl:max-w-3xl">
                                                <div class="relative flex flex-shrink-0 flex-col items-end">
                                                    <div>
                                                        <div
                                                            class="relative flex h-9 w-9 items-center justify-center rounded-sm p-1 text-white"
                                                            style="background-color: rgb(25, 195, 125); width: 36px; height: 36px;"
                                                        ><svg
                                                                class="icon-md"
                                                                role="img"
                                                                width="41"
                                                                height="41"
                                                                viewBox="0 0 41 41"
                                                                fill="none"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                            ><text
                                                                    x="-9999"
                                                                    y="-9999"
                                                                >ChatGPT</text>
                                                                <path
                                                                    d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z"
                                                                    fill="currentColor"
                                                                ></path>
                                                            </svg></div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="agent-turn relative flex w-[calc(100%-50px)] flex-col gap-1 md:gap-3 lg:w-[calc(100%-115px)]">
                                                    <div class="flex max-w-full flex-grow flex-col gap-3">
                                                        <div
                                                            class="flex min-h-[20px] flex-col items-start gap-3 overflow-x-auto break-words">
                                                            <h1 class="text-2xl font-bold text-red-400">Something went wrong
                                                            </h1>
                                                            <p class="text-black">{{ $error }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                        <form wire:submit.prevent="sendMessage">
                            <label
                                class="sr-only"
                                for="chat"
                            >Your message</label>
                            <div class="flex items-center rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-700">
                                @if (!$chat->id)
                                    <button
                                        class="inline-flex cursor-pointer justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                                        type="button"
                                        wire:loading.attr="disabled"
                                        wire:click="save"
                                    >
                                        <x-heroicon-s-bookmark class="h-6 w-6" />
                                        <span class="sr-only">Save</span>
                                    </button>
                                @endif
                                <div class="mx-4 block w-full p-2.5">
                                    <textarea
                                        class="mx-4 block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                        id="chat"
                                        wire:model.debounce="message"
                                        wire:loading.attr="disabled"
                                        rows="5"
                                        placeholder="Your message..."
                                    ></textarea>
                                    <div class="text-red-600">
                                        @error('message')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <button
                                    class="inline-flex cursor-pointer justify-center rounded-full p-2 text-primary-600 hover:bg-primary-100 dark:text-primary-500 dark:hover:bg-gray-600"
                                    type="submit"
                                    wire:loading.remove
                                    x-on:click="$wire.showCurrentResponse = true"
                                >
                                    <x-heroicon-s-paper-airplane class="h-6 w-6" />
                                    <span class="sr-only">Send message</span>
                                </button>
                                <svg
                                    class="-ml-1 mr-3 h-5 w-5 animate-spin text-primary-600"
                                    wire:loading
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif($consentedToTerms === false && $loading === false)
                <div class="flex flex-col justify-center">
                    <p class="mb-4">
                        You must agree to the terms and conditions before continuing use of this feature.
                    </p>
                </div>
            @else
                <div class="flex h-full w-full items-center justify-center">
                    <x-filament::loading-indicator class="h-12 w-12" />
                </div>
            @endif

            @if ($consentedToTerms === false)
                {{-- TODO potentially prevent closure of modal by pressing escape --}}
                <x-filament::modal
                    id="consent-agreement"
                    width="5xl"
                    alignment="center"
                    :close-by-clicking-away="false"
                    :close-button="false"
                >
                    @if ($loading === false)
                        <x-slot name="trigger">
                            <x-filament::button>
                                Terms and Conditions
                            </x-filament::button>
                        </x-slot>
                    @endif

                    <x-slot name="heading">
                        <h1 class="text-center text-xl">
                            {{ $consentAgreement->title }}
                        </h1>
                    </x-slot>

                    <x-slot name="description">
                        <div class="my-4 border-gray-100 text-center">
                            <p class="prose mx-auto text-gray-100">{{ $consentAgreement->description }}</p>
                        </div>

                        <x-filament::section>
                            <div class="text-center">
                                <p class="prose mx-auto text-gray-100">{{ $consentAgreement->body }}</p>
                            </div>
                        </x-filament::section>
                    </x-slot>

                    <x-slot name="footer">
                        <form
                            class="flex w-full flex-col"
                            wire:submit="confirmConsent"
                        >
                            <label class="mx-auto">
                                <x-filament::input.checkbox
                                    wire:model="consentedToTerms"
                                    required
                                />
                                <span class="ml-2">
                                    I agree to the terms and conditions
                                </span>
                            </label>

                            <div class="mt-4 flex justify-center space-x-4">
                                <x-filament::button
                                    class="mt-4 md:mt-0"
                                    wire:click="denyConsent"
                                    outlined
                                    color="warning"
                                >
                                    Cancel
                                </x-filament::button>
                                <x-filament::button
                                    class="mt-4 md:mt-0"
                                    type="submit"
                                    color="success"
                                >
                                    I understand
                                </x-filament::button>
                            </div>

                        </form>
                    </x-slot>
                </x-filament::modal>
            @endif
            <script src="{{ FilamentAsset::getScriptSrc('assistantCurrentResponse', 'canyon-gbs/assistant') }}"></script>
        </div>
    </x-filament-panels::page>
