<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace FilamentTiptapEditor\Actions;

use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Actions\Action;

class SourceAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->modalHeading(trans('filament-tiptap-editor::source-modal.heading'))
            ->fillForm(fn ($arguments) => ['source' => $arguments['html']])
            ->form([
                Textarea::make('source')
                    ->label(trans('filament-tiptap-editor::source-modal.labels.source'))
                    ->extraAttributes(['class' => 'source_code_editor'])
                    ->autosize(),
            ])
            ->modalWidth('screen')
            ->action(function (TiptapEditor $component, $data) {
                $content = $data['source'] ?? '<p></p>';

                $content = tiptap_converter()->asJSON($content, decoded: true);

                if ($component->shouldSupportBlocks()) {
                    $content = $component->renderBlockPreviews($content, $component);
                }

                $component->getLivewire()->dispatch(
                    event: 'insertFromAction',
                    type: 'source',
                    statePath: $component->getStatePath(),
                    source: $content,
                );

                $component->state($content);
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'filament_tiptap_source';
    }
}
