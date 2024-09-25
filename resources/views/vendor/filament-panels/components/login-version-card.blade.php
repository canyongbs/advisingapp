<div class="bg-turkish-500 text-white rounded-xl p-6 mb-6">
    <div class="flex md:flex-row flex-col justify-center items-center gap-6">
        <div>
            <p class="text-white text-sm font-semibold">Version {{ app('current-version') }} is now available!</p>
            <p class="text-white-500 text-sm my-4">Your instance of Advising App<sup>TM</sup> was automatically updated with our latest available features.</p>
            <a href="{{ $themeChangelogUrl }}" class="border-2 border-white px-4 py-2 text-white rounded-xl text-sm font-semibold inline-block">Learn More</a>
        </div>
        <img src="{{ Vite::asset('resources/images/version_image.svg') }}" class="block max-h-32" />
    </div>
</div>
