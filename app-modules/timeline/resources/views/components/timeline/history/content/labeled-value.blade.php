@props(['value'])
<div>
    <x-timeline::timeline.labeled-field>
        <x-slot:label>
            {{ $value['key'] }}
        </x-slot:label>

        <x-timeline::timeline.history.content.value
            :value="$value['new']"
            :link="data_get($value, 'extra.new.link')"
        />
    </x-timeline::timeline.labeled-field>
</div>
