@php
    use Assist\Form\Actions\GenerateFormEmbedCode;
@endphp

<div class="flex items-center justify-center py-16">
    <div class="w-full max-w-md">
        {!! resolve(GenerateFormEmbedCode::class)->handle($this->form) !!}
    </div>
</div>
