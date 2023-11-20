<div class="flex w-full flex-col">

    <div class="mt-4 flex w-full justify-center">
        <img
            class="hidden h-5 dark:block"
            src="{{ Vite::asset('resources/images/default-logo-dark.png') }}"
            alt="{{ config('app.name') }}"
        />
        <img
            class="block h-5 dark:hidden"
            src="{{ Vite::asset('resources/images/default-logo-light.png') }}"
            alt="{{ config('app.name') }}"
        />
    </div>

    <div class="flex w-full justify-center pb-4 pt-2">
        <span class="w-11/12 text-center text-xs lg:w-3/4 xl:w-7/12">
            © 2023 Canyon GBS LLC. All Rights Reserved. Canyon GBS™, Advanced Student Support & Interaction Servicing
            Technology™, ASSIST by Canyon GBS™ are trademarks of Canyon GBS LLC. For more information or inquiries,
            visit
            our website at <a
                class="text-blue-600 underline dark:text-blue-400"
                href="https://canyongbs.com/"
            >https://canyongbs.com/.</a>
        </span>
    </div>

</div>
