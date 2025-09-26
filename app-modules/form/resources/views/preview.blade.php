@php
    use AdvisingApp\Form\Actions\GenerateSubmissibleEmbedCode;
@endphp

<x-layouts.app title="Form Preview">
    <div class="flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-4xl">
            <script
                src="{{ asset('js/widgets/form/advising-app-form-widget.js') }}"
                type="module"
            ></script>

            <form-embed
                url="{{ route('forms.api.preview', $form) }}"
                preview="true"
            ></form-embed>
        </div>
    </div>
</x-layouts.app>