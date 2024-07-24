<?php

namespace FilamentTiptapEditor;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;
use JsonException;
use Livewire\Component;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Filament\Forms\Components\Field;
use Filament\Forms\Contracts\HasForms;
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
            $component->processImages();

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

    public function generateImageUrls(array $document): array
    {
        $record = $this->getRecord();

        if (! ($record instanceof HasMedia)) {
            return $document;
        }

        $media = $record->getMedia(collectionName: $this->getName())->keyBy('uuid');

        $content = $document['content'] ?? [];

        foreach ($content as $blockIndex => $block) {
            if (array_key_exists('content', $block)) {
                $content[$blockIndex] = $this->generateImageUrls($block);
            }

            if (($block['type'] ?? null) !== 'image') {
                continue;
            }

            $id = $block['attrs']['id'] ?? null;

            if (blank($id)) {
                continue;
            }

            if (! $media->has($id)) {
                continue;
            }

            $content[$blockIndex]['attrs']['src'] = $media->get($id)->getTemporaryUrl(now()->addDay());
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

    public function processImages(): void
    {
        $record = $this->getRecord();

        if (! ($record instanceof HasMedia)) {
            return;
        }

        $images = $record->getMedia(collectionName: $this->getName())->keyBy('uuid');
        $unusedImageKeys = $images->keys()->all();

        $livewire = $this->getLivewire();
        $statePath = $this->getStatePath();

        $unusedImageKeys = $this->processImagesInDocument(
            $this->getState(),
            $record,
            existingImages: $images,
            newImages: data_get($livewire->componentFileAttachments, $statePath) ?? [],
            unusedImageKeys: $unusedImageKeys,
        );

        Media::query()
            ->whereIn('uuid', $unusedImageKeys)
            ->delete();

        data_forget($livewire->componentFileAttachments, $statePath);
    }

    protected function processImagesInDocument(array $document, HasMedia $record, MediaCollection $existingImages, array $newImages, array $unusedImageKeys): array
    {
        $content = $document['content'] ?? [];

        foreach ($content as $block) {
            if (array_key_exists('content', $block)) {
                $unusedImageKeys = $this->processImagesInDocument($block, $record, $existingImages, $newImages, $unusedImageKeys);
            }

            if (($block['type'] ?? null) !== 'image') {
                continue;
            }

            $id = $block['attrs']['id'] ?? null;

            if (blank($id)) {
                continue;
            }

            if (($unusedMediaIndex = array_search($id, $unusedImageKeys)) !== false) {
                unset($unusedImageKeys[$unusedMediaIndex]);
            }

            if ($existingImages->has($id)) {
                continue;
            }

            if (array_key_exists($id, $newImages)) {
                $image = $record
                    ->addMediaFromString($newImages[$id]->get())
                    ->usingFileName(((string) Str::ulid()) . '.' . $newImages[$id]->getClientOriginalExtension())
                    ->withAttributes(['uuid' => $id])
                    ->toMediaCollection($this->getName(), diskName: $this->getDisk());

                $existingImages->put($id, $image);

                continue;
            }

            $existingMedia = Media::findByUuid($id);

            if (! $existingMedia) {
                continue;
            }

            $existingMedia->copy($record, collectionName: $this->getName(), diskName: $this->getDisk());
        }

        return $unusedImageKeys;
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
}