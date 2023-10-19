@php use Assist\Form\Actions\GenerateFormEmbedCode; @endphp
<div class="flex items-center justify-center pt-16">
    <x-filament::section>
        {!! resolve(GenerateFormEmbedCode::class)->handle($this->form) !!}
    </x-filament::section>
</div>
