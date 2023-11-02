@php
    use Carbon\Carbon;
    use App\Models\User;
    use Assist\Division\Models\Division;
    use Assist\ServiceManagement\Models\ServiceRequestType;
    use Assist\ServiceManagement\Models\ServiceRequestStatus;
    use Assist\ServiceManagement\Models\ServiceRequestPriority;
@endphp

<x-filament::fieldset>
    <x-slot name="label">
        Service Request
    </x-slot>

    <dl class="max-w-md divide-y divide-gray-200 text-gray-900 dark:divide-gray-700 dark:text-white">
        <div class="flex flex-col pb-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Division</dt>
            <dd class="text-sm font-semibold">{{ Division::find($action['division_id'])?->name }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Status</dt>
            <dd class="text-sm font-semibold">{{ ServiceRequestStatus::find($action['status_id'])?->name }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Priority</dt>
            <dd class="text-sm font-semibold">{{ ServiceRequestPriority::find($action['priority_id'])?->name }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Type</dt>
            <dd class="text-sm font-semibold">{{ ServiceRequestType::find($action['type_id'])?->name }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Assigned To</dt>
            <dd class="text-sm font-semibold">{{ User::find($action['assigned_to_id'])?->name ?? 'No one' }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Close Details/Description</dt>
            <dd class="text-sm font-semibold">{{ $action['close_details'] }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Internal Service Request Details</dt>
            <dd class="text-sm font-semibold">{{ $action['res_details'] }}</dd>
        </div>
        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Execute At</dt>
            <dd class="text-sm font-semibold">{{ Carbon::parse($action['execute_at'])->format('m/d/Y H:i:s') }}</dd>
        </div>
    </dl>

</x-filament::fieldset>
