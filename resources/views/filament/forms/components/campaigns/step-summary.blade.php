@php
    use Assist\Campaign\Enums\CampaignActionType;
    
    $actions = collect($getLivewire()->data['actions']);
    
    $sortedActions = $actions->sortBy(function ($item, $key) {
        return $item['data']['execute_at'];
    });
@endphp

<div>
    <div class="flex flex-col space-y-4">
        @foreach ($sortedActions as $action)
            @php
                $view = CampaignActionType::from($action['type'])->getStepSummaryView();
            @endphp

            @include($view, ['action' => $action['data']])
        @endforeach
    </div>

</div>
