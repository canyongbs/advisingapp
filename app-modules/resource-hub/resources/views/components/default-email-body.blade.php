<div>
    <p>Hi {{ $recipient?->full_name }},</p>

    @if (filled($record->article_details))
        {!! tiptap_converter()->record($record, attribute: 'article_details')->asHTML($record->article_details) !!}
    @endif
</div>
