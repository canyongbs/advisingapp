@props(['value'])
<span>
    Changed from
    <x-timeline::timeline.history.content.value
        class="font-semibold"
        :value="$value['old']"
        :link="data_get($value, 'extra.old.link')"
    />
    to
    <x-timeline::timeline.history.content.value
        class="font-semibold"
        :value="$value['new']"
        :link="data_get($value, 'extra.new.link')"
    />
</span>
