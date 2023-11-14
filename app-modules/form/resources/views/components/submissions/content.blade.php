@props(['content', 'submission'])

@php
    use Assist\Form\Actions\InjectSubmissionStateIntoTipTapContent;
    use Assist\Form\Filament\Blocks\FormFieldBlockRegistry;
    
    $content['content'] = app(InjectSubmissionStateIntoTipTapContent::class)($submission, $content['content']);
@endphp

<div class="prose max-w-none dark:prose-invert">
    {!! tiptap_converter()->blocks(FormFieldBlockRegistry::get())->asHTML($content) !!}
</div>
