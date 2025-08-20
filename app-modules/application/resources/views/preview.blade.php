@php
    use AdvisingApp\Form\Actions\GenerateSubmissibleEmbedCode;
@endphp

<x-layouts.app title="Application Preview">
    <div class="flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-4xl">
            <script
                src="{{ asset('js/widgets/application/advising-app-application-widget.js') }}"
                type="module"
            ></script>

            <application-embed
                url="{{ route('applications.api.preview', $application) }}"
                preview="true"
            ></application-embed>
        </div>
    </div>
</x-layouts.app>
