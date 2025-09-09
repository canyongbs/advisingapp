<x-filament-panels::page>
    <div>
        <x-filament::link
            :href="\AdvisingApp\Engagement\Filament\Pages\SentItems::getUrl()"
            icon="heroicon-m-arrow-left"
        >
            Back to Sent Items
        </x-filament::link>
    </div>

    {{ $this->infolist }}
</x-filament-panels::page>
