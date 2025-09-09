<x-filament-panels::page>
    <div>
        <x-filament::link
            :href="\AdvisingApp\Engagement\Filament\Pages\Inbox::getUrl()"
            icon="heroicon-m-arrow-left"
        >
            Back to Inbox
        </x-filament::link>
    </div>

    {{ $this->infolist }}
</x-filament-panels::page>
