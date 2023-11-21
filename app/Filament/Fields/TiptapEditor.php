<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Fields;

use App\Filament\Actions\MediaAction;
use App\Support\MediaEncoding\TiptapMediaEncoder;
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
