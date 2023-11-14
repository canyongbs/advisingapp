<div class="tiptap-rendered-content flex flex-col gap-6">
    @if ($submission->form->is_wizard)
        @foreach ($submission->form->steps as $step)
            <x-filament::section>
                <x-slot name="heading">
                    {{ $step->label }}
                </x-slot>

                <x-form::submissions.content
                    :content="$step->content"
                    :submission="$submission"
                />
            </x-filament::section>
        @endforeach
    @else
        <x-form::submissions.content
            :content="$submission->form->content"
            :submission="$submission"
        />
    @endif
</div>
