<div class="flex justify-evenly">
    <x-filament::icon-button
        icon="icon-google"
        size="xl"
        outlined
        :href="route('calendar.google.login')"
        tag="a"
    >
        Google
    </x-filament::icon-button>

    <div class="grid justify-items-center">
        <x-filament::icon-button
            class="opacity-50"
            icon="icon-outlook"
            size="xl"
            outlined
        >
            Outlook
        </x-filament::icon-button>
        Coming Soon!
    </div>
</div>
