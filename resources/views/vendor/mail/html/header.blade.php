@props(['url', 'emailTemplate' => null])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if ($emailTemplate?->hasMedia('logo'))
                {{-- TODO: Don't use temporary urls? --}}
                <img src="{{ $emailTemplate?->getFirstTemporaryUrl(now()->addDays(6), 'logo') }}"
                     class="logo"
                     alt="Logo">
            @else
                <img src="{{ Vite::asset('resources/images/default-logo-light.png') }}"
                     style="height: 75px; max-height: 75px; max-width: 100vw;"
                     alt="Logo">
            @endif
        </a>
    </td>
</tr>
