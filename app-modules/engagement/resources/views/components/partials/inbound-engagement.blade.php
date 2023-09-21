@php
    use App\Filament\Resources\UserResource;
    use Assist\Engagement\Enums\EngagementDeliveryMethod;
    use Assist\AssistDataModel\Filament\Resources\StudentResource;
@endphp

<div class="flex-auto rounded-md p-3 ring-1 ring-inset ring-gray-200">
    <div class="flex justify-between gap-x-4">
        <div class="py-0.5 text-xs leading-5 text-gray-500"><span class="font-medium text-gray-900">
                <a
                    class="font-medium text-green-300 underline hover:text-green-400"
                    href="{{ StudentResource::getUrl('view', ['record' => $engagement->sender]) }}"
                >
                    {{-- TODO We need to implement a method that returns they display name key value --}}
                    {{-- Not just the column name itself --}}
                    {{ $engagement->sender->full_name }}
                </a>
        </div>
        <p class="flex-none py-0.5 text-xs leading-5 text-gray-100">
            Sent {{ $engagement->sent_at->diffForHumans() }}
        </p>
    </div>
    <p class="mt-4 text-sm">{{ $engagement->content }}</p>
</div>
