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

    <x-filament::icon-button
        icon="icon-outlook"
        size="xl"
        outlined
        tooltip="Coming Soon!"
    >
        Outlook
    </x-filament::icon-button>
</div>
