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

namespace FilamentTiptapEditor;

use Throwable;
use Illuminate\Support\Str;
use Filament\Forms\Components\Component;
use Filament\Support\Concerns\EvaluatesClosures;

abstract class TiptapBlock
{
    use EvaluatesClosures;

    public string $preview = 'filament-tiptap-editor::tiptap-block-preview';

    public string $rendered = 'filament-tiptap-editor::tiptap-block-preview';

    public ?string $identifier = null;

    public ?string $label = null;

    public string $width = 'sm';

    public bool $slideOver = false;

    public ?string $icon = null;

    public function getIdentifier(): string
    {
        return $this->identifier ?? Str::camel(class_basename($this));
    }

    public function getLabel(): string
    {
        return $this->label ?? Str::of(class_basename($this))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public function getModalWidth(): string
    {
        return $this->width ?? 'sm';
    }

    public function isSlideOver(): bool
    {
        return $this->slideOver ?? false;
    }

    public function getFormSchema(): array
    {
        return [];
    }

    /**
     * @throws Throwable
     */
    public function getPreview(?array $data = null, ?Component $component = null): string
    {
        $data = $data ?? [];

        return view($this->preview, [
            ...$data,
            'component' => $component,
        ])->render();
    }

    /**
     * @throws Throwable
     */
    public function getRendered(?array $data = null): string
    {
        $data = $data ?? [];

        return view($this->rendered, $data)->render();
    }

    public function getIcon(): ?string
    {
        return $this->evaluate($this->icon);
    }
}
