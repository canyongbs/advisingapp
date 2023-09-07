<?php

namespace App\Filament\Fields;

use App\Support\TiptapMediaEncoder;
use FilamentTiptapEditor\TiptapEditor as BaseTiptapEditor;

class TiptapEditor extends BaseTiptapEditor
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (BaseTiptapEditor $component, string | array | null $state) {
            if (! $state) {
                $component->state('<p></p>');
            }

            $component->state(TiptapMediaEncoder::decode($component, $state));

            $component->state($component->getHTML());
        });

        $this->dehydrateStateUsing(function (BaseTiptapEditor $component, string | array | null $state) {
            $state = TiptapMediaEncoder::encode($component, $state);

            $component->state($state);

            if ($state && $this->expectsJSON()) {
                return $component->getJSON();
            }

            if ($state && $this->expectsText()) {
                return $component->getText();
            }

            return $state;
        });
    }
}
