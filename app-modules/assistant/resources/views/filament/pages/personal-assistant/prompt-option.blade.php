<div>
    {{ $prompt->title }}

    {{-- The `prompt-upvotes-count` class is used to hide the upvote count when the prompt is selected. --}}
    (<span class="prompt-upvotes-count">{{ $prompt->upvotes_count }} {{ str('Like')->plural($prompt->upvotes_count) }} |
    </span>{{ $prompt->uses_count }} {{ str('Use')->plural($prompt->uses_count) }})
</div>

@if (filled($prompt->description))
    <div class="text-xs text-gray-600 dark:text-gray-300">
        {{ $prompt->description }}
    </div>
@endif
