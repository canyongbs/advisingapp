@php
    use AdvisingApp\Form\Actions\GenerateCaseFeedbackFormEmbedCode;
@endphp

<div class="flex items-center justify-center">
    <div class="w-full max-w-full">
        {!! resolve(GenerateCaseFeedbackFormEmbedCode::class)->handle($this->case) !!}
    </div>
</div>
