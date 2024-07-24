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

namespace FilamentTiptapEditor\Blocks;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Str;
use Filament\Forms\Components\Group;
use FilamentTiptapEditor\TiptapBlock;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\CheckboxList;

class VideoBlock extends TiptapBlock
{
    public string $preview = 'filament-tiptap-editor::components.blocks.previews.video';

    public string $width = 'lg';

    public function getFormSchema(): array
    {
        return [
            TextInput::make('url')
                ->label(trans('filament-tiptap-editor::oembed-modal.labels.url'))
                ->live(onBlur: true)
                ->required()
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('url', $this->convertUrl($state));
                }),
            CheckboxList::make('native_options')
                ->hiddenLabel()
                ->gridDirection('row')
                ->columns(3)
                ->visible(function (callable $get) {
                    return ! (str_contains($get('url'), 'vimeo') || str_contains($get('url'), 'youtube') || str_contains($get('url'), 'youtu.be'));
                })
                ->options([
                    'autoplay' => trans('filament-tiptap-editor::oembed-modal.labels.autoplay'),
                    'loop' => trans('filament-tiptap-editor::oembed-modal.labels.loop'),
                    'controls' => trans('filament-tiptap-editor::oembed-modal.labels.controls'),
                ]),
            CheckboxList::make('vimeo_options')
                ->hiddenLabel()
                ->gridDirection('row')
                ->columns(3)
                ->visible(function (callable $get) {
                    return str_contains($get('url'), 'vimeo');
                })
                ->options([
                    'autoplay' => trans('filament-tiptap-editor::oembed-modal.labels.autoplay'),
                    'loop' => trans('filament-tiptap-editor::oembed-modal.labels.loop'),
                    'show_title' => trans('filament-tiptap-editor::oembed-modal.labels.title'),
                    'byline' => trans('filament-tiptap-editor::oembed-modal.labels.byline'),
                    'portrait' => trans('filament-tiptap-editor::oembed-modal.labels.portrait'),
                ]),
            Group::make([
                CheckboxList::make('youtube_options')
                    ->hiddenLabel()
                    ->gridDirection('row')
                    ->columns(3)
                    ->options([
                        'controls' => trans('filament-tiptap-editor::oembed-modal.labels.controls'),
                        'nocookie' => trans('filament-tiptap-editor::oembed-modal.labels.nocookie'),
                    ]),
                TimePicker::make('start_at')
                    ->label(trans('filament-tiptap-editor::oembed-modal.labels.start_at'))
                    ->reactive()
                    ->date(false)
                    ->afterStateHydrated(function (TimePicker $component, $state): void {
                        if (! $state) {
                            return;
                        }

                        $state = CarbonInterval::seconds($state)->cascade();
                        $component->state(Carbon::parse($state->h . ':' . $state->i . ':' . $state->s)->format('Y-m-d H:i:s'));
                    })
                    ->dehydrateStateUsing(function ($state): int {
                        if (! $state) {
                            return 0;
                        }

                        return Carbon::parse($state)->diffInSeconds('00:00:00');
                    }),
            ])->visible(function (callable $get) {
                return str_contains($get('url'), 'youtube') || str_contains($get('url'), 'youtu.be');
            }),
            Checkbox::make('responsive')
                ->default(true)
                ->reactive()
                ->label(trans('filament-tiptap-editor::oembed-modal.labels.responsive'))
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state) {
                        $set('width', '16');
                        $set('height', '9');
                    } else {
                        $set('width', '640');
                        $set('height', '480');
                    }
                })
                ->columnSpan('full'),
            Group::make([
                TextInput::make('width')
                    ->reactive()
                    ->required()
                    ->label(trans('filament-tiptap-editor::oembed-modal.labels.width'))
                    ->default('16'),
                TextInput::make('height')
                    ->reactive()
                    ->required()
                    ->label(trans('filament-tiptap-editor::oembed-modal.labels.height'))
                    ->default('9'),
            ])->columns(['md' => 2]),
        ];
    }

    public function convertUrl(string $url, array $options = []): string
    {
        if (Str::of($url)->contains('/video/')) {
            return $url;
        }

        preg_match('/\.com\/([0-9]+)/', $url, $matches);

        if (! $matches || ! $matches[1]) {
            return '';
        }

        $query = http_build_query([
            'autoplay' => $options['autoplay'] ?? false,
            'loop' => $options['loop'] ?? false,
            'title' => $options['show_title'] ?? false,
            'byline' => $options['byline'] ?? false,
            'portrait' => $options['portrait'] ?? false,
        ]);

        return "https://player.vimeo.com/video/{$matches[1]}?{$query}";
    }
}
