@if($this->getRecord()->student()->exists())
<x-filament::icon-button
    icon="heroicon-m-lock-closed"
    color="gray"
    size="lg"
    tooltip="Prospect is converted to Student"
/>
@endif