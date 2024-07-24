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

use Livewire\Component;
use Illuminate\Support\Str;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Contracts\HasForms;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Actions\Action;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MediaAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->arguments([
                'src' => '',
                'alt' => '',
                'title' => '',
                'width' => '',
                'height' => '',
            ])
            ->modalWidth('md')
            ->mountUsing(function (TiptapEditor $component, ComponentContainer $form, array $arguments) {
                $source = $arguments['src'] !== ''
                    ? $component->getDirectory() . Str::of($arguments['src'])
                        ->after($component->getDirectory())
                    : null;

                $form->fill([
                    'src' => $source,
                    'alt' => $arguments['alt'] ?? '',
                    'title' => $arguments['title'] ?? '',
                    'width' => $arguments['width'] ?? '',
                    'height' => $arguments['height'] ?? '',
                ]);
            })->modalHeading(function (TiptapEditor $component, array $arguments) {
                $context = blank($arguments['src'] ?? null) ? 'insert' : 'update';

                return trans('filament-tiptap-editor::media-modal.heading.' . $context);
            })->form(function (TiptapEditor $component, array $arguments) {
                return [
                    FileUpload::make('src')
                        ->label(blank($arguments['src'] ?? null) ? 'Upload file' : 'Replace file (optional)')
                        ->acceptedFileTypes($component->getAcceptedFileTypes())
                        ->maxSize($component->getMaxSize())
                        ->minSize($component->getMinSize())
                        ->imageResizeMode($component->getImageResizeMode())
                        ->imageCropAspectRatio($component->getImageCropAspectRatio())
                        ->imageResizeTargetWidth($component->getImageResizeTargetWidth())
                        ->imageResizeTargetHeight($component->getImageResizeTargetHeight())
                        ->required(blank($arguments['src'] ?? null))
                        ->live()
                        ->storeFiles(false)
                        ->afterStateUpdated(function (TemporaryUploadedFile $state, callable $set) {
                            if (Str::contains($state->getMimeType(), 'image')) {
                                $set('type', 'image');
                            } else {
                                $set('type', 'document');
                            }

                            if ($dimensions = $state->dimensions()) {
                                $set('width', $dimensions[0]);
                                $set('height', $dimensions[1]);
                            }
                        }),
                    TextInput::make('link_text')
                        ->label(trans('filament-tiptap-editor::media-modal.labels.link_text'))
                        ->required()
                        ->visible(fn (callable $get) => $get('type') == 'document'),
                    TextInput::make('alt')
                        ->label(trans('filament-tiptap-editor::media-modal.labels.alt'))
                        ->hidden(fn (callable $get) => $get('type') == 'document')
                        ->hintAction(
                            Action::make('alt_hint_action')
                                ->label('?')
                                ->color('primary')
                                ->url('https://www.w3.org/WAI/tutorials/images/decision-tree', true)
                        ),
                    TextInput::make('title')
                        ->label(trans('filament-tiptap-editor::media-modal.labels.title')),
                    Group::make([
                        TextInput::make('width'),
                        TextInput::make('height'),
                    ])->columns(),
                    Hidden::make('type')
                        ->default('document'),
                ];
            })->action(function (TiptapEditor $component, Component&HasForms $livewire, array $data, array $arguments) {
                if (filled($data['src'])) {
                    $id = (string) Str::uuid();

                    data_set($livewire->componentFileAttachments, "{$component->getStatePath()}.{$id}", $data['src']);
                }

                $livewire->dispatch(
                    event: 'insertFromAction',
                    type: 'media',
                    statePath: $component->getStatePath(),
                    media: [
                        'src' => filled($data['src']) ? $data['src']->temporaryUrl() : ($arguments['src'] ?? null),
                        'id' => filled($data['src']) ? $id : ($arguments['id'] ?? null),
                        'alt' => $data['alt'] ?? null,
                        'title' => $data['title'] ?? null,
                        'width' => $data['width'] ?? null,
                        'height' => $data['height'] ?? null,
                        'link_text' => $data['link_text'] ?? null,
                    ],
                );
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'filament_tiptap_media';
    }
}
