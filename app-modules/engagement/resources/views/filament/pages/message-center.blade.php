<div class="flex max-h-full w-full flex-row p-4">

    <x-engagement::message-center-inbox
        :selectedEducatable="$selectedEducatable"
        :educatables="$subscribedStudentsWithEngagements"
    />

    <x-engagement::message-center-content :educatable="$selectedEducatable" />

</div>
