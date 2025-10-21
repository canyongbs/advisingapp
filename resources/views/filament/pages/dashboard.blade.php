@php
    use AdvisingApp\Authorization\Filament\Widgets\UnlicensedNotice;
    use App\Filament\Widgets\Features;
    use App\Filament\Widgets\Notifications;
@endphp

<x-filament-panels::page>
    <div class="grid gap-6">
        <div class="col-span-full flex flex-col lg:col-span-5 items-center px-16 py-8 h-64 bg-black bg-cover bg-no-repeat rounded-lg"
            style="background-image: url('{{ asset('images/banner.png') }}')">
            <div class="grid gap-1 text-xl text-center w-full md:text-3xl md:text-start">
                <div class="text-white font-bold text-2xl">
                    Welcome,
                </div>
                <div class="text-white font-bold text-3xl">
                    {{ auth()->user()->name }}!
                </div>

                <div class="text-gray-200 text-lg">
                    <p id="current-date"></p>
                </div>
                <div class="text-gray-200 text-lg">
                    <p id="current-time"></p>
                </div>
            </div>
        </div>

        <div class="col-span-full lg:col-span-5 flex flex-col gap-3">
            @if (UnlicensedNotice::canView())
                @livewire(UnlicensedNotice::class)
            @else
                @livewire(Features::class)

                @livewire(Notifications::class)
            @endif
            
        </div>
    </div>
</x-filament-panels::page>

<script>
  document.getElementById('current-date').textContent = (new Date()).toLocaleDateString('en-US', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});

  document.getElementById('current-time').textContent = (new Date()).toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true});
</script>