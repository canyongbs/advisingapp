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
                $view = match ($action['type']) {
                    CampaignActionType::BulkEngagement->value => 'filament.forms.components.campaigns.actions.bulk-engagement',
                    CampaignActionType::ServiceRequest->value => 'filament.forms.components.campaigns.actions.service-request',
                    CampaignActionType::ProactiveAlert->value => 'filament.forms.components.campaigns.actions.proactive-alert',
                    CampaignActionType::Interaction->value => 'filament.forms.components.campaigns.actions.interaction',
                    CampaignActionType::CareTeam->value => 'filament.forms.components.campaigns.actions.care-team',
                    CampaignActionType::Task->value => 'filament.forms.components.campaigns.actions.task',
                };
            @endphp

            @include($view, ['action' => $action['data']])
        @endforeach
    </div>

</div>
