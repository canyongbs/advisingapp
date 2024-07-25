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

use Closure;
use Exception;
use Throwable;
use JsonException;
use Livewire\Component;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Field;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Actions\Action;
use FilamentTiptapEditor\Actions\OEmbedAction;
use FilamentTiptapEditor\Actions\SourceAction;
use FilamentTiptapEditor\Actions\EditMediaAction;
use FilamentTiptapEditor\Concerns\CanStoreOutput;
use FilamentTiptapEditor\Actions\GridBuilderAction;
use FilamentTiptapEditor\Concerns\HasCustomActions;
use FilamentTiptapEditor\Concerns\InteractsWithMedia;
use FilamentTiptapEditor\Concerns\InteractsWithMenus;
use Filament\Forms\Components\Concerns\HasPlaceholder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;

class TiptapEditor extends Field
{
    use CanStoreOutput;
    use HasCustomActions;
    use HasExtraAlpineAttributes;
    use HasExtraInputAttributes;
    use HasPlaceholder;
    use InteractsWithMedia;
    use InteractsWithMenus;

    protected array $extensions = [];

    protected string | Closure | null $maxContentWidth = null;

    protected string $profile = 'default';

    protected ?bool $shouldDisableStylesheet = null;

    protected ?array $tools = [];

    protected array | Closure $blocks = [];

    protected array | Closure $mergeTags = [];

    protected string $view = 'filament-tiptap-editor::tiptap-editor';

    protected bool $shouldCollapseBlocksPanel = false;

    protected bool $shouldShowMergeTagsInBlocksPanel = true;

    protected ?string $recordAttribute = null;

    protected array $gridLayouts = [
        'two-columns',
        'three-columns',
        'four-columns',
        'five-columns',
        'fixed-two-columns',
        'fixed-three-columns',
        'fixed-four-columns',
        'fixed-five-columns',
        'asymmetric-left-thirds',
        'asymmetric-right-thirds',
        'asymmetric-left-fourths',
        'asymmetric-right-fourths',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->tools = config('filament-tiptap-editor.profiles.default');
        $this->extensions = config('filament-tiptap-editor.extensions') ?? [];

        $this->afterStateHydrated(function (TiptapEditor $component, string | array | null $state): void {
            if (! $state) {
                return;
            }

            if (! is_array($state)) {
                $state = tiptap_converter()->asJSON($state, decoded: true);
            }

            $state = $component->generateImageUrls($state);

            $state = $component->renderBlockPreviews($state);

            $component->state($state);
        });

        $this->afterStateUpdated(function (TiptapEditor $component, Component $livewire): void {
            $livewire->validateOnly($component->getStatePath());
        });

        $this->beforeStateDehydrated(function (TiptapEditor $component, string | array | null $state) {
            if (! is_array($state)) {
                return;
            }

            $component->state($component->processImages($state));
        });

        $this->dehydrateStateUsing(function (TiptapEditor $component, string | array | null $state): string | array | null {
            if (! $state) {
                return null;
            }

            if (! $component->expectsJSON()) {
                throw new Exception('TipTap content should be stored in JSON only, in order to process media and blocks correctly.');
            }

            if (! is_array($state)) {
                $state = tiptap_converter()->asJSON($state, decoded: true);
            }

            $state = $component->decodeBlocks($state);
            $state = $component->removeImageUrls($state);

            return $state;
        });

        $this->saveRelationshipsUsing(fn (TiptapEditor $component, Model $record) => $record->wasRecentlyCreated && $component->processImages());

        $this->registerListeners([
            'tiptap::setGridBuilderContent' => [
                fn (
                    TiptapEditor $component,
                    string $statePath,
                    array $arguments
                ) => $this->getCustomListener('filament_tiptap_grid', $component, $statePath, $arguments),
            ],
            'tiptap::setSourceContent' => [
                fn (
                    TiptapEditor $component,
                    string $statePath,
                    array $arguments
                ) => $this->getCustomListener('filament_tiptap_source', $component, $statePath, $arguments),
            ],
            'tiptap::setOEmbedContent' => [
                fn (
                    TiptapEditor $component,
                    string $statePath,
                    array $arguments
                ) => $this->getCustomListener('filament_tiptap_oembed', $component, $statePath, $arguments),
            ],
            'tiptap::setLinkContent' => [
                fn (
                    TiptapEditor $component,
                    string $statePath,
                    array $arguments
                ) => $this->getCustomListener('filament_tiptap_link', $component, $statePath, $arguments),
            ],
            'tiptap::setMediaContent' => [
                fn (
                    TiptapEditor $component,
                    string $statePath,
                    array $arguments
                ) => $this->getCustomListener('filament_tiptap_media', $component, $statePath, $arguments),
            ],
            'tiptap::editMediaContent' => [
                fn (
                    TiptapEditor $component,
                    string $statePath,
                    array $arguments
                ) => $this->getCustomListener('filament_tiptap_edit_media', $component, $statePath, $arguments),
            ],
            'tiptap::updateBlock' => [
                fn (
                    TiptapEditor $component,
                    string $statePath,
                    array $arguments
                ) => $this->getCustomListener('updateBlock', $component, $statePath, $arguments),
            ],
        ]);

        $this->registerActions([
            SourceAction::make(),
            OEmbedAction::make(),
            GridBuilderAction::make(),
            fn (): Action => $this->getFileAttachmentUrlAction(),
            fn (): Action => $this->getLinkAction(),
            fn (): Action => $this->getMediaAction(),
            fn (): Action => $this->getInsertBlockAction(),
            fn (): Action => $this->getUpdateBlockAction(),
            fn (): Action => EditMediaAction::make(),
        ]);
    }

    public function getFileAttachmentUrlAction(): Action
    {
        return Action::make('getFileAttachmentUrl')
            ->action(function (TiptapEditor $component, Component&HasForms $livewire, array $arguments): ?string {
                $livewire->skipRender();

                $file = $livewire->getFormComponentFileAttachment("{$component->getStatePath()}.{$arguments['fileKey']}");

                if (! $file) {
                    return null;
                }

                return $file->temporaryUrl();
            });
    }

    public function getCustomListener(string $name, TiptapEditor $component, string $statePath, array $arguments = []): void
    {
        if ($this->verifyListener($component, $statePath)) {
            return;
        }

        $component
            ->getLivewire()
            ->mountFormComponentAction($statePath, $name, $arguments);
    }

    public function generateImageUrls(array $document, ?Collection $images = null): array
    {
        $record = $this->getRecord();

        $images ??= ($record instanceof HasMedia) ?
            $record->getMedia(collectionName: $this->getRecordAttribute())->keyBy('uuid') :
            collect();

        $content = $document['content'] ?? [];

        foreach ($content as $blockIndex => $block) {
            if (array_key_exists('content', $block)) {
                $content[$blockIndex] = $this->generateImageUrls($block, $images);
            }

            if (($block['type'] ?? null) !== 'image') {
                continue;
            }

            $id = $block['attrs']['id'] ?? null;

            if (blank($id)) {
                continue;
            }

            if ($images->has($id)) {
                $content[$blockIndex]['attrs']['src'] = $images->get($id)->getTemporaryUrl(now()->addDay());

                continue;
            }

            $image = Media::findByUuid($id);

            if (! $image) {
                continue;
            }

            $images->put($id, $image);

            $content[$blockIndex]['attrs']['src'] = $image->getTemporaryUrl(now()->addDay());
        }

        $document['content'] = $content;

        return $document;
    }

    public function removeImageUrls(array $document): array
    {
        $content = $document['content'] ?? [];

        foreach ($content as $blockIndex => $block) {
            if (array_key_exists('content', $block)) {
                $content[$blockIndex] = $this->removeImageUrls($block);
            }

            if (($block['type'] ?? null) !== 'image') {
                continue;
            }

            if (! array_key_exists('attrs', $block)) {
                continue;
            }

            if (($block['attrs']['class'] ?? null) === 'filament-tiptap-loading-image') {
                unset($content[$blockIndex]);

                continue;
            }

            if (! array_key_exists('src', $block['attrs'])) {
                continue;
            }

            $id = $block['attrs']['id'] ?? null;

            if (blank($id)) {
                continue;
            }

            unset($content[$blockIndex]['attrs']['src']);
        }

        $document['content'] = $content;

        return $document;
    }

    public function processImages(?array $originalState = null): array
    {
        $record = $this->getRecord();

        $originalState ??= (data_get($record, $this->getRecordAttribute()) ?? $this->getState());

        if (! ($record instanceof HasMedia)) {
            return $originalState;
        }

        $images = $record->getMedia(collectionName: $this->getRecordAttribute())->keyBy('uuid');
        $unusedImageKeys = $images->keys()->all();

        $livewire = $this->getLivewire();

        [$newState, $unusedImageKeys] = tiptap_converter()->saveImages(
            $originalState,
            disk: $this->getDisk(),
            record: $record,
            recordAttribute: $this->getRecordAttribute(),
            newImages: $this->getTemporaryImages(),
            existingImages: $images,
            unusedImageKeys: $unusedImageKeys,
        );

        Media::query()
            ->whereIn('uuid', $unusedImageKeys)
            ->delete();

        data_forget($livewire->componentFileAttachments, $this->getStatePath());

        // We need to save the new state back to the record if the image IDs have changed.
        if (
            $record->wasRecentlyCreated &&
            ($originalState !== $newState)
        ) {
            $recordAttribute = $this->getRecordAttribute();

            if (str($recordAttribute)->contains('.')) {
                $attributeState = $record->getAttribute((string) str($recordAttribute)->before('.'));

                $record->fill([
                    ((string) str($recordAttribute)->before('.')) => data_set(
                        $attributeState,
                        (string) str($recordAttribute)->after('.'),
                        $newState,
                    ),
                ]);
            } else {
                $record->{$recordAttribute} = $newState;
            }

            $record->save();
        }

        return $newState;
    }

    public function getTemporaryImages(): array
    {
        return data_get($this->getLivewire()->componentFileAttachments, $this->getStatePath()) ?? [];
    }

    /**
     * @throws Throwable
     * @throws JsonException
     */
    public function renderBlockPreviews(array $document): array
    {
        $content = $document['content'] ?? [];

        foreach ($content as $blockIndex => $block) {
            if (($block['type'] ?? null) === 'tiptapBlock') {
                $instance = $this->getBlock($block['attrs']['type'] ?? '');
                $orderedAttrs = [
                    'preview' => $instance->getPreview($block['attrs']['data'] ?? [], $this),
                    'statePath' => $this->getStatePath(),
                    'type' => $block['attrs']['type'] ?? '',
                    'label' => $instance->getLabel(),
                    'data' => Js::from($block['attrs']['data'] ?? [])->toHtml(),
                ];
                $content[$blockIndex]['attrs'] = $orderedAttrs;
            } elseif (array_key_exists('content', $block)) {
                $content[$blockIndex] = $this->renderBlockPreviews($block);
            }
        }

        $document['content'] = $content;

        return $document;
    }

    public function decodeBlocks(array $document): array
    {
        $content = $document['content'];

        foreach ($content as $k => $block) {
            if ($block['type'] === 'tiptapBlock') {
                if (is_string($block['attrs']['data'])) {
                    $data = Str::of(json_decode('"' . $block['attrs']['data'] . '"'))
                        ->after('JSON.parse(\'')
                        ->beforeLast('\')')
                        ->toString();

                    $content[$k]['attrs']['data'] = json_decode($data, true);
                }
                unset($content[$k]['attrs']['statePath'], $content[$k]['attrs']['preview'], $content[$k]['attrs']['label']);
            } elseif (array_key_exists('content', $block)) {
                $content[$k] = $this->decodeBlocks($block);
            }
        }

        $document['content'] = $content;

        return $document;
    }

    public function getInsertBlockAction(): Action
    {
        return Action::make('insertBlock')
            ->form(function (TiptapEditor $component, Component $livewire, array $arguments): ?array {
                $block = $component->getBlock($arguments['type']);

                if (empty($block->getFormSchema())) {
                    return null;
                }

                return $block->getFormSchema();
            })
            ->modalHeading(function (TiptapEditor $component, Component $livewire, array $arguments): ?string {
                if (isset($arguments['type'])) {
                    $block = $component->getBlock($arguments['type']);

                    if (empty($block->getFormSchema())) {
                        return null;
                    }

                    return trans('filament-tiptap-editor::editor.blocks.insert');
                }

                return trans('filament-tiptap-editor::editor.blocks.insert');
            })
            ->modalWidth(function (TiptapEditor $component, Component $livewire, array $arguments): ?string {
                if (isset($arguments['type'])) {
                    $block = $component->getBlock($arguments['type']);

                    if (empty($block->getFormSchema())) {
                        return null;
                    }

                    return $block->getModalWidth();
                }

                return 'sm';
            })
            ->slideOver(function (TiptapEditor $component, Component $livewire, array $arguments): bool {
                if (isset($arguments['type'])) {
                    $block = $component->getBlock($arguments['type']);

                    if (empty($block->getFormSchema())) {
                        return false;
                    }

                    return $block->isSlideOver();
                }

                return false;
            })
            ->action(function (TiptapEditor $component, Component $livewire, array $arguments, $data): void {
                $block = $component->getBlock($arguments['type']);

                $livewire->dispatch(
                    event: 'insertBlockFromAction',
                    statePath: $component->getStatePath(),
                    type: $arguments['type'],
                    data: Js::from($data)->toHtml(),
                    preview: $block->getPreview($data, $component),
                    label: $block->getLabel(),
                    coordinates: $arguments['coordinates'] ?? [],
                );
            });
    }

    public function getUpdateBlockAction(): Action
    {
        return Action::make('updateBlock')
            ->fillForm(fn (array $arguments) => $arguments['data'])
            ->modalHeading(fn () => trans('filament-tiptap-editor::editor.blocks.update'))
            ->modalWidth(function (TiptapEditor $component, Component $livewire, array $arguments): string {
                return isset($arguments['type'])
                    ? $component->getBlock($arguments['type'])->getModalWidth()
                    : 'sm';
            })
            ->slideOver(function (TiptapEditor $component, Component $livewire, array $arguments): string {
                return isset($arguments['type']) && $component->getBlock($arguments['type'])->isSlideOver();
            })
            ->form(function (TiptapEditor $component, Component $livewire, array $arguments): array {
                return $component
                    ->getBlock($arguments['type'])
                    ->getFormSchema();
            })
            ->action(function (TiptapEditor $component, Component $livewire, array $arguments, $data): void {
                $block = $component->getBlock($arguments['type']);

                $livewire->dispatch(
                    event: 'updateBlockFromAction',
                    statePath: $component->getStatePath(),
                    type: $arguments['type'],
                    data: Js::from($data)->toHtml(),
                    preview: $block->getPreview($data, $component),
                    label: $block->getLabel(),
                );
            });
    }

    public function maxContentWidth(string | Closure $width): static
    {
        $this->maxContentWidth = $width;

        return $this;
    }

    public function profile(string $profile): static
    {
        $this->profile = $profile;
        $this->tools = config('filament-tiptap-editor.profiles.' . $profile);

        return $this;
    }

    public function blocks(array | Closure $blocks): static
    {
        $this->blocks = $blocks;

        return $this;
    }

    public function tools(array $tools): static
    {
        $this->tools = $tools;

        return $this;
    }

    public function getMaxContentWidth(): string
    {
        return $this->maxContentWidth
            ? $this->evaluate($this->maxContentWidth)
            : config('filament-tiptap-editor.max_content_width');
    }

    public function disableStylesheet(): static
    {
        $this->shouldDisableStylesheet = true;

        return $this;
    }

    public function shouldDisableStylesheet(): bool
    {
        return $this->shouldDisableStylesheet ?? config('filament-tiptap-editor.disable_stylesheet');
    }

    public function getBlock(string $identifier): TiptapBlock
    {
        return $this->getBlocks()[$identifier];
    }

    public function getBlocks(): array
    {
        $blocks = $this->evaluate($this->blocks);

        return collect($blocks)->mapWithKeys(function ($block, $key) {
            $b = app($block);

            return [$b->getIdentifier() => $b];
        })->toArray();
    }

    public function getTools(): array
    {
        $extensions = collect($this->extensions);

        foreach ($this->tools as $k => $tool) {
            if ($ext = $extensions->where('id', $tool)->first()) {
                $this->tools[$k] = $ext;
            }
        }

        return $this->tools;
    }

    public function getExtensionScripts(): array
    {
        return collect(config('filament-tiptap-editor.extensions') ?? [])
            ->transform(function ($ext) {
                return $ext['source'];
            })->toArray();
    }

    public function verifyListener(TiptapEditor $component, string $statePath): bool
    {
        return $component->isDisabled() || $statePath !== $component->getStatePath();
    }

    public function shouldSupportBlocks(): bool
    {
        return filled($this->getBlocks()) && $this->expectsJSON() && in_array('blocks', $this->getTools());
    }

    public function collapseBlocksPanel(bool $condition = true): static
    {
        $this->shouldCollapseBlocksPanel = $condition;

        return $this;
    }

    public function shouldCollapseBlocksPanel(): bool
    {
        return $this->shouldCollapseBlocksPanel;
    }

    public function mergeTags(array | Closure $mergeTags): static
    {
        $this->mergeTags = $mergeTags;

        return $this;
    }

    public function getMergeTags(): ?array
    {
        return $this->evaluate($this->mergeTags) ?? [];
    }

    public function showMergeTagsInBlocksPanel(bool $condition = true): static
    {
        $this->shouldShowMergeTagsInBlocksPanel = $condition;

        return $this;
    }

    public function shouldShowMergeTagsInBlocksPanel(): bool
    {
        return $this->shouldShowMergeTagsInBlocksPanel;
    }

    public function gridLayouts(array $layouts): static
    {
        $this->gridLayouts = $layouts;

        return $this;
    }

    public function getGridLayouts(): array
    {
        return $this->gridLayouts;
    }

    public function recordAttribute(?string $attribute): static
    {
        $this->recordAttribute = $attribute;

        return $this;
    }

    public function getRecordAttribute(): string
    {
        return $this->recordAttribute ?? $this->getName();
    }
}
