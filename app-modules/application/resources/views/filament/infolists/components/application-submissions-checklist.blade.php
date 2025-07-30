@php
    $applicationSubmission = $this->submission ?? $this->cachedMountedTableActionRecord;
@endphp

<div wire:key="checklist-{{ $applicationSubmission->getKey() }}">
    @livewire('application::application-submissions-checklist', ['applicationSubmission' => $applicationSubmission])
</div>
