<div class="js-cookie-consent cookie-consent fixed bottom-0 inset-x-0 pb-2">
    <div class="max-w-7xl mx-auto px-6">
        <div class="p-2 rounded-lg bg-primary-100">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 items-center hidden md:inline">
                    <p class="ml-3 text-black cookie-consent__message">
                        {!! trans('cookie-consent::texts.message') !!}
                    </p>
                </div>
                
                <div class="mt-2 flex-shrink-0 w-full sm:mt-0 sm:w-auto">
                    <x-filament::button class="js-cookie-consent-agree cookie-consent__agree">
                        {{ trans('cookie-consent::texts.agree') }}
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>
</div>
