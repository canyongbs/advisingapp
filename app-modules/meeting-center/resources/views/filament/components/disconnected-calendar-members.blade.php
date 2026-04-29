<ul class="list-none space-y-1">
    @foreach ($directUsers as $user)
        <li>{{ $user->name }}</li>
    @endforeach

    @foreach ($teamGroups as $teamName => $users)
        <li class="mt-2">
            <strong>{{ $teamName }}</strong>
            <ul class="list-none pl-4 mt-1 space-y-1">
                @foreach ($users as $user)
                    <li>{{ $user->name }}</li>
                @endforeach
            </ul>
        </li>
    @endforeach
</ul>
