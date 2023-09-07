<?php

namespace App\Filament\Fields;

use App\Support\TiptapMediaEncoder;
use App\Filament\Actions\MediaAction;
use FilamentTiptapEditor\TiptapEditor as BaseTiptapEditor;
use FilamentTiptapEditor\Actions\MediaAction as FilamentTiptapEditorMediaAction;

class TiptapEditor extends BaseTiptapEditor
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actions = collect($this->actions)
            ->filter(function ($action) {
                return $action::class !== FilamentTiptapEditorMediaAction::class;
            })
            ->push(MediaAction::make())
            ->toArray();

        $this->afterStateHydrated(function (BaseTiptapEditor $component, string | array | null $state) {
            if (! $state) {
                $component->state('<p></p>');
            }

            if (! empty($state)) {
                $component->state(TiptapMediaEncoder::decode($state));
            }

            $component->state($component->getHTML());
        });

        $this->dehydrateStateUsing(function (BaseTiptapEditor $component, string | array | null $state) {
            $state = TiptapMediaEncoder::encode($component->getDisk(), $state);

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
