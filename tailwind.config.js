import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    safelist: [
        'text-black',
    ],
    content: [
        './app/Filament/**/*.php',
        './app-modules/**/src/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/awcodes/filament-tiptap-editor/resources/**/*.blade.php',
    ],
}
