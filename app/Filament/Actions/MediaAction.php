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

namespace App\Filament\Actions;

use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Hidden;
use Intervention\Image\Facades\Image;
use Filament\Forms\ComponentContainer;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\BaseFileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use FilamentTiptapEditor\Actions\MediaAction as BaseMediaAction;

class MediaAction extends BaseMediaAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
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
            })->form(function (TiptapEditor $component) {
                return [
                    FileUpload::make('src')
                        ->label(__('filament-tiptap-editor::media-modal.labels.file'))
                        ->disk($component->getDisk())
                        ->directory($component->getDirectory())
                        ->visibility(config('filament-tiptap-editor.visibility'))
                        ->preserveFilenames(config('filament-tiptap-editor.preserve_file_names'))
                        ->acceptedFileTypes($component->getAcceptedFileTypes())
                        ->maxFiles(1)
                        ->maxSize($component->getMaxFileSize())
                        ->imageCropAspectRatio(config('filament-tiptap-editor.image_crop_aspect_ratio'))
                        ->imageResizeTargetWidth(config('filament-tiptap-editor.image_resize_target_width'))
                        ->imageResizeTargetHeight(config('filament-tiptap-editor.image_resize_target_height'))
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (TemporaryUploadedFile $state, callable $set) {
                            if (Str::contains($state->getMimeType(), 'image')) {
                                $set('type', 'image');
                            } else {
                                $set('type', 'document');
                            }
                        })
                        ->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file, callable $set) {
                            $filename = $component->shouldPreserveFilenames() ? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) : Str::uuid();

                            $storeMethod = $component->getVisibility() === 'public' ? 'storePubliclyAs' : 'storeAs';

                            if (Storage::disk($component->getDiskName())->exists(ltrim($component->getDirectory() . '/' . $filename . '.' . $file->getClientOriginalExtension(), '/'))) {
                                $filename = $filename . '-' . time();
                            }

                            if (Str::contains($file->getMimeType(), 'image')) {
                                if (config('filesystems.disks.s3.driver') === 's3') {
                                    $image = Image::make($file->readStream());
                                } else {
                                    $image = Image::make($file->getRealPath());
                                }

                                $set('width', $image->getWidth());
                                $set('height', $image->getHeight());
                            }

                            $upload = $file->{$storeMethod}($component->getDirectory(), $filename . '.' . $file->getClientOriginalExtension(), $component->getDiskName());

                            return $component->getVisibility() === 'private' ? Storage::disk($component->getDiskName())->temporaryUrl($upload, now()->addMinutes(5)) : Storage::disk($component->getDiskName())->url($upload);
                            //return Storage::disk($component->getDiskName())->url($upload);
                        }),
                    TextInput::make('link_text')
                        ->label(__('filament-tiptap-editor::media-modal.labels.link_text'))
                        ->required()
                        ->visible(fn (callable $get) => $get('type') == 'document'),
                    TextInput::make('alt')
                        ->label(__('filament-tiptap-editor::media-modal.labels.alt'))
                        ->hidden(fn (callable $get) => $get('type') == 'document')
                        ->helperText(new HtmlString('<span class="text-xs"><a href="https://www.w3.org/WAI/tutorials/images/decision-tree" target="_blank" rel="noopener" class="underline text-primary-500 hover:text-primary-600 focus:text-primary-600">' . __('filament-tiptap-editor::media-modal.labels.alt_helper_text') . '</a></span>')),
                    TextInput::make('title')
                        ->label(__('filament-tiptap-editor::media-modal.labels.title')),
                    Hidden::make('width'),
                    Hidden::make('height'),
                    Hidden::make('type')
                        ->default('document'),
                ];
            })->action(function (TiptapEditor $component, $data) {
                $source = str_starts_with($data['src'], 'http')
                    ? $data['src']
                    : config('app.url') . Storage::url($data['src']);

                $component->getLivewire()->dispatch(
                    'insert-media',
                    statePath: $component->getStatePath(),
                    media: [
                        'src' => $source,
                        'alt' => $data['alt'] ?? null,
                        'title' => $data['title'],
                        'width' => $data['width'],
                        'height' => $data['height'],
                        'link_text' => $data['link_text'] ?? null,
                    ],
                );
            });
    }
}
