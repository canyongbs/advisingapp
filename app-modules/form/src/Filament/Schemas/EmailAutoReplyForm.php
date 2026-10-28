<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Form\Filament\Schemas;

use AdvisingApp\Engagement\Filament\Actions\LoadEmailTemplateAction;
use AdvisingApp\Engagement\Models\EmailTemplate;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EmailAutoReplyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_enabled')
                    ->label('Enabled')
                    ->live(),
                RichEditor::make('subject')
                    ->toolbarButtons([])
                    ->minHeight(null)
                    ->json()
                    ->required(fn (Get $get): bool => (bool) $get('is_enabled'))
                    ->helperText('You can insert recipient information by typing {{ and choosing a merge value to insert.')
                    ->columnSpanFull()
                    ->placeholder('Enter the email subject here...'),
                RichEditor::make('body')
                    ->fileAttachmentsDisk('s3-public')
                    ->toolbarButtons([
                        ['bold', 'italic', 'link'],
                        [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'bulletList', 'orderedList', 'horizontalRule'],
                        ['small', 'textColor'],
                        ['attachFiles'],
                        ['clearFormatting'],
                        ['mergeTags'],
                    ])
                    ->resizableImages()
                    ->activePanel('mergeTags')
                    ->json()
                    ->required(fn (Get $get): bool => (bool) $get('is_enabled'))
                    ->hintAction(LoadEmailTemplateAction::make())
                    ->getFileAttachmentUrlFromAnotherRecordUsing(function (mixed $file): ?string {
                        return Media::query()
                            ->where('uuid', $file)
                            ->where('model_type', (new EmailTemplate())->getMorphClass())
                            ->first()
                            ?->getUrl();
                    })
                    ->saveFileAttachmentFromAnotherRecordUsing(function (mixed $file, ?Model $record): ?string {
                        if (! $record instanceof HasMedia) {
                            return null;
                        }

                        return Media::query()
                            ->where('uuid', $file)
                            ->where('model_type', (new EmailTemplate())->getMorphClass())
                            ->first()
                            ?->copy($record, 'body', 's3-public')
                            ->uuid;
                    })
                    ->helperText('You can insert recipient information by typing {{ and choosing a merge value to insert.')
                    ->columnSpanFull()
                    ->live(),
            ]);
    }
}
