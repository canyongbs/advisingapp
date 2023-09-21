<div wire:key="task-edit-{{ $task->id }}">
    {{ $this->editAction }}

    <x-filament-actions::modals wire:key="task-edit-modal-{{ $task->id }}" />
</div>
