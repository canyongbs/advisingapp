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

use Tiptap\Editor;
use Tiptap\Nodes\Table;
use Tiptap\Nodes\TableRow;
use Illuminate\Support\Str;
use Tiptap\Marks\Highlight;
use Tiptap\Marks\Subscript;
use Tiptap\Marks\TextStyle;
use Tiptap\Marks\Underline;
use Tiptap\Nodes\TableCell;
use Tiptap\Marks\Superscript;
use Tiptap\Nodes\TableHeader;
use Spatie\MediaLibrary\HasMedia;
use Tiptap\Extensions\StarterKit;
use Illuminate\Support\Collection;
use Tiptap\Nodes\CodeBlockHighlight;
use Illuminate\Database\Eloquent\Model;
use FilamentTiptapEditor\Extensions\Marks;
use FilamentTiptapEditor\Extensions\Nodes;
use FilamentTiptapEditor\Extensions\Extensions;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use FilamentTiptapEditor\Exceptions\ImagesNotResolvableException;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;

class TiptapConverter
{
    protected Editor $editor;

    protected ?array $blocks = null;

    protected bool $tableOfContents = false;

    protected array $mergeTagsMap = [];

    protected ?Model $record = null;

    protected ?string $recordAttribute = null;

    public function getEditor(): Editor
    {
        return $this->editor ??= new Editor([
            'extensions' => $this->getExtensions(),
        ]);
    }

    public function blocks(array $blocks): static
    {
        $this->blocks = $blocks;

        return $this;
    }

    public function getExtensions(): array
    {
        $customExtensions = collect(config('filament-tiptap-editor.extensions', []))
            ->transform(function ($ext) {
                return new $ext['parser']();
            })->toArray();

        return [
            new StarterKit([
                'listItem' => false,
            ]),
            new TextStyle(),
            new Extensions\TextAlign([
                'types' => ['heading', 'paragraph'],
            ]),
            new Extensions\ClassExtension(),
            new Extensions\IdExtension(),
            new Extensions\StyleExtension(),
            new Extensions\Color(),
            new CodeBlockHighlight(),
            new Nodes\ListItem(),
            new Nodes\Lead(),
            new Nodes\Image(),
            new Nodes\CheckedList(),
            new Nodes\Details(),
            new Nodes\DetailsSummary(),
            new Nodes\DetailsContent(),
            new Nodes\Grid(),
            new Nodes\GridColumn(),
            new Nodes\GridBuilder(),
            new Nodes\GridBuilderColumn(),
            new Nodes\MergeTag(),
            new Nodes\Vimeo(),
            new Nodes\YouTube(),
            new Nodes\Video(),
            new Nodes\TiptapBlock(['blocks' => $this->blocks]),
            new Nodes\Hurdle(),
            new Table(),
            new TableHeader(),
            new TableRow(),
            new TableCell(),
            new Highlight(),
            new Underline(),
            new Superscript(),
            new Subscript(),
            new Marks\Link(),
            new Marks\Small(),
            ...$customExtensions,
        ];
    }

    public function mergeTagsMap(array $mergeTagsMap): static
    {
        $this->mergeTagsMap = $mergeTagsMap;

        return $this;
    }

    public function asHTML(string | array $content, bool $toc = false, int $maxDepth = 3, array $newImages = []): string
    {
        $editor = $this->getEditor()->setContent($content);

        if ($toc) {
            $this->parseHeadings($editor, $maxDepth);
        }

        if (filled($this->mergeTagsMap)) {
            $this->parseMergeTags($editor);
        }

        $this->generateImageUrls($editor, $newImages);

        return $editor->getHTML();
    }

    public function asJSON(string | array $content, bool $decoded = false, bool $toc = false, int $maxDepth = 3): string | array
    {
        $editor = $this->getEditor()->setContent($content);

        if ($toc) {
            $this->parseHeadings($editor, $maxDepth);
        }

        if (filled($this->mergeTagsMap)) {
            $this->parseMergeTags($editor);
        }

        return $decoded ? json_decode($editor->getJSON(), true) : $editor->getJSON();
    }

    public function saveImages(array $document, string $disk, HasMedia $record, string $recordAttribute, array $newImages, ?Collection $existingImages = null, array $unusedImageKeys = []): array
    {
        $existingImages ??= collect([]);

        $document['content'] ??= [];

        return [json_decode($this->getEditor()->setContent($document)->descendants(function (&$node) use ($disk, $existingImages, $newImages, $record, $recordAttribute, &$unusedImageKeys) {
            if ($node->type !== 'image') {
                return;
            }

            $id = $node->attrs->id ?? null;

            if (blank($id)) {
                return;
            }

            if (($unusedImageIndex = array_search($id, $unusedImageKeys)) !== false) {
                unset($unusedImageKeys[$unusedImageIndex]);
            }

            if ($existingImages->has($id)) {
                return;
            }

            if (array_key_exists($id, $newImages)) {
                $newImage = $newImages[$id];

                $content = ($newImage instanceof TemporaryUploadedFile) ?
                    $newImage->get() :
                    FileUploadConfiguration::storage()->get($newImage['path']);

                $extension = ($newImage instanceof TemporaryUploadedFile) ?
                    $newImage->getClientOriginalExtension() :
                    $newImage['extension'];

                $image = $record
                    ->addMediaFromString($content)
                    ->usingFileName(((string) Str::ulid()) . '.' . $extension)
                    ->toMediaCollection($recordAttribute, diskName: $disk);

                $existingImages->put($image->uuid, $image);

                $node->attrs->id = $image->uuid;

                return;
            }

            $existingImage = Media::findByUuid($id);

            if (! $existingImage) {
                return;
            }

            $newImage = $existingImage->copy($record, collectionName: $recordAttribute, diskName: $disk);

            $existingImages->put($newImage->uuid, $newImage);

            $node->attrs->id = $newImage->uuid;
        })->getJSON(), associative: true), $unusedImageKeys];
    }

    public function copyImagesToNewRecord(array $content, Model $replica, string $disk): array
    {
        $editor = $this->getEditor()->setContent($content);

        $record = $this->getRecord();

        $recordAttribute = $this->getRecordAttribute();

        $images = $record instanceof HasMedia ?
            $record->getMedia(collectionName: $recordAttribute)->keyBy('uuid') :
            collect([]);

        $editor->descendants(function (&$node) use ($disk, $images, $record, $recordAttribute, $replica) {
            if ($node->type !== 'image') {
                return;
            }

            $id = $node->attrs?->id;

            if (blank($id)) {
                return;
            }

            if (
                (! ($record instanceof HasMedia)) ||
                blank($recordAttribute)
            ) {
                throw new ImagesNotResolvableException("Image [{$id}] attempted to be replicated, but the TipTap converter was not configured with the media record and attribute.");
            }

            if (! $images->has($id)) {
                return;
            }

            $newImage = $images->get($id)->copy($replica, collectionName: $recordAttribute, diskName: $disk);

            $node->attrs->id = $newImage->uuid;
        });

        return json_decode($editor->getJSON(), associative: true);
    }

    public function asText(string | array $content): string
    {
        $editor = $this->getEditor()->setContent($content);

        if (filled($this->mergeTagsMap)) {
            $this->parseMergeTags($editor);
        }

        return $editor->getText();
    }

    public function asTOC(string | array $content, int $maxDepth = 3): string
    {
        if (is_string($content)) {
            $content = $this->asJSON($content, decoded: true);
        }

        $headings = $this->parseTocHeadings($content['content'], $maxDepth);

        return $this->generateNestedTOC($headings, $headings[0]['level']);
    }

    public function parseHeadings(Editor $editor, int $maxDepth = 3): Editor
    {
        $editor->descendants(function (&$node) use ($maxDepth) {
            if ($node->type !== 'heading') {
                return;
            }

            if ($node->attrs->level > $maxDepth) {
                return;
            }

            if (! property_exists($node->attrs, 'id') || $node->attrs->id === null) {
                $node->attrs->id = str(collect($node->content)->map(function ($node) {
                    return $node?->text ?? null;
                })->implode(' '))->kebab()->toString();
            }

            array_unshift($node->content, (object) [
                'type' => 'text',
                'text' => '#',
                'marks' => [
                    [
                        'type' => 'link',
                        'attrs' => [
                            'href' => '#' . $node->attrs->id,
                        ],
                    ],
                ],
            ]);
        });

        return $editor;
    }

    public function parseTocHeadings(array $content, int $maxDepth = 3): array
    {
        $headings = [];

        foreach ($content as $node) {
            if ($node['type'] === 'heading') {
                if ($node['attrs']['level'] <= $maxDepth) {
                    $text = collect($node['content'])->map(function ($node) {
                        return $node['text'] ?? null;
                    })->implode(' ');

                    if (! isset($node['attrs']['id'])) {
                        $node['attrs']['id'] = str($text)->kebab()->toString();
                    }

                    $headings[] = [
                        'level' => $node['attrs']['level'],
                        'id' => $node['attrs']['id'],
                        'text' => $text,
                    ];
                }
            } elseif (array_key_exists('content', $content)) {
                $this->parseTocHeadings($content, $maxDepth);
            }
        }

        return $headings;
    }

    public function parseMergeTags(Editor $editor): Editor
    {
        $editor->descendants(function (&$node) {
            if ($node->type !== 'mergeTag') {
                return;
            }

            if (filled($this->mergeTagsMap)) {
                $node->content = [
                    (object) [
                        'type' => 'text',
                        'text' => $this->mergeTagsMap[$node->attrs->id] ?? null,
                    ],
                ];
            }
        });

        return $editor;
    }

    public function generateImageUrls(Editor $editor, array $newImages = []): Editor
    {
        $record = $this->getRecord();

        $recordAttribute = $this->getRecordAttribute();

        $images = $record instanceof HasMedia ? $record->getMedia(collectionName: $recordAttribute)->keyBy('uuid') : collect([]);

        $editor->descendants(function (&$node) use ($images, $newImages) {
            if ($node->type !== 'image') {
                return;
            }

            $id = $node->attrs?->id;

            if (blank($id)) {
                return;
            }

            unset($node->attrs->id);

            if ($newImage = ($newImages[$id] ?? null)) {
                $node->attrs->src = $newImage->temporaryUrl();

                return;
            }

            if (! $images->has($id)) {
                return;
            }

            $image = $images->get($id);

            if (config("filesystems.disks.{$image->disk}.media_library_visibility") === 'public') {
                $node->attrs->src = $image->getUrl();

                return;
            }

            $node->attrs->src = $image->getTemporaryUrl(now()->addDay());
        });

        return $editor;
    }

    public function generateNestedTOC(array $headings, int $parentLevel = 0): string
    {
        $result = '<ul>';
        $prev = $parentLevel;

        foreach ($headings as $item) {
            $prev <= $item['level'] ?: $result .= str_repeat('</ul>', $prev - $item['level']);
            $prev >= $item['level'] ?: $result .= '<ul>';

            $result .= '<li><a href="#' . $item['id'] . '">' . $item['text'] . '</a></li>';

            $prev = $item['level'];
        }

        $result .= '</ul>';

        return $result;
    }

    public function record(?Model $record, ?string $attribute): static
    {
        $this->record = $record;
        $this->recordAttribute = $attribute;

        return $this;
    }

    public function getRecord(): ?Model
    {
        return $this->record;
    }

    public function getRecordAttribute(): ?string
    {
        return $this->recordAttribute;
    }
}
