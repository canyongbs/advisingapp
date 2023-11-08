@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
                {{--            @elseif(\Assist\Division\Models\Division::first()->emailTemplate->getFirstMedia('logo'))--}}
                {{--                <img--}}
                {{--                    src="{{ \Assist\Division\Models\Division::first()->emailTemplate->getFirstTemporaryUrl(now()->addMinutes(5), 'logo')}}"--}}
                {{--                    class="logo" alt="Logo"--}}
                {{--                >--}}
            @else
                {{ $notifiable }}
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
