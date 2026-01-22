@php
    use App\Models\User;
    use AdvisingApp\CareTeam\Models\CareTeamRole;
@endphp

<x-filament::fieldset>
    <x-slot name="label">
        Please confirm that you would like to add the following users to the {{ $educatable->getMorphClass() }} {{ $educatable->display_name }}:
    </x-slot>

    <dl class="max-w-md divide-y divide-gray-200 text-gray-900 dark:divide-gray-700 dark:text-white">
        <div class="flex flex-col pb-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Users to be assigned to the care team</dt>
            @foreach ($careTeams as $careTeam)
                <dd class="text-sm font-semibold">
                    @php
                        $user = User::find($careTeam['user_id']);

                        $userName = $user?->name;
                        if(! is_null($user?->job_title)) {
                            $userName .= ' (' . $user->job_title . ')';
                        }
                    @endphp
                    @if (filled($careTeam['care_team_role_id']))
                        {{ $userName . ' with ' . CareTeamRole::find($careTeam['care_team_role_id'])?->name . ' role.' }}
                    @else
                        {{ $userName }}
                    @endif
                </dd>
            @endforeach
        </div>
    </dl>

</x-filament::fieldset>