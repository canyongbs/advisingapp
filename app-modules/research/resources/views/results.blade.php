<div wire:poll.5s>
    @if (filled($researchRequest?->results))
        <section class="prose max-w-none dark:prose-invert">
            @if (filled($researchRequest->title))
                <h1>{{ $researchRequest->title}}</h1>
            @endif

            {!! str($researchRequest->results)->markdown()->sanitizeHtml() !!}
        </section>
    @else
        <div class="flex items-center gap-2">
            <x-filament::loading-indicator class="h-5 w-5" /> Researching...
        </div>
    @endif
</div>