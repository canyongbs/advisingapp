@props(['value', 'link' => null])
<span>
    @if ($link)
        <a
            class="hover:underline"
            href="{{ $link }}"
        >
            <span {{ $attributes->class('prose dark:prose-invert') }}> {{ str($value)->sanitizeHtml()->toHtmlString() }}
            </span>
        </a>
    @else
        <span {{ $attributes->class('prose dark:prose-invert') }}> {{ str($value)->sanitizeHtml()->toHtmlString() }}
        </span>
    @endif
</span>
