<x-filament::modal id="about">
    <x-slot name="header">
    </x-slot>
    <div class="space-y-4 text-center">
        <div>
            <h2 class="text-xl font-bold">Advising App® by Canyon GBS </h2>
            <p>Version {{ config('sentry.release') }}</p>
        </div>
        <div>
            <p class="text-base text-gray-600 dark:text-gray-300">
                <strong>Full-Lifecycle CRM + Enterprise AI</strong><br>
                Empowering colleges and universities to engage, retain, and graduate every learner.
            </p>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-300">
            <p>© 2015-{{ date('Y') }} Canyon GBS LLC. All rights reserved.</p>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-300">
            <p>The product is ISO 27001:2022 certified and SOC 2 compliant.</p>
        </div>
    </div>
</x-filament::modal>
