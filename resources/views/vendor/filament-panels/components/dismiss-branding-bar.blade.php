            
    @php
    use App\Settings\CollegeBrandingSettings;
    $collegeBrandingSettings = app(CollegeBrandingSettings::class);
    @endphp
            <div  
                x-data="{ isVisible: @entangle('isVisible') }" x-show="isVisible"
                style="--c-600: {{ \Filament\Support\Colors\Color::all()[$collegeBrandingSettings->color][600] }}"
                class="sticky top-16 z-10 bg-custom-600 text-sm font-medium text-white px-6 py-2 flex items-center h-10"
            >
                {{ $collegeBrandingSettings->college_text }}
                @if ($collegeBrandingSettings->dismissible)
                <button @click="isVisible = false; $wire.dismiss()" class="hover:text-gray-400 ml-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                </button>
                @endif
            </div>
          
