<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Engagement\Filament\Resources\EmailTemplates\Pages;

use AdvisingApp\Engagement\Filament\Resources\Actions\DraftTemplateWithAiAction;
use AdvisingApp\Engagement\Filament\Resources\EmailTemplates\EmailTemplateResource;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\StockMedia\Enums\StockMediaProvider;
use AdvisingApp\StockMedia\Settings\StockMediaSettings;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use CanyonGBS\Common\Filament\Forms\RichContentPlugins\StockImageRichContentPlugin;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\URL;

class EditEmailTemplate extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = EmailTemplateResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->string()
                    ->required()
                    ->autocomplete(false),
                Textarea::make('description')
                    ->string(),
                RichEditor::make('content')
                    ->fileAttachmentsDisk('s3-public')
                    ->toolbarButtons([
                        ['bold', 'italic', 'link'],
                        [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'bulletList', 'orderedList', 'horizontalRule'],
                        ['textColor', 'small'],
                        ['attachFiles', ...($this->getStockImagePlugin() ? ['stockImage'] : []), 'mergeTags'],
                        ['clearFormatting'],
                        ['undo', 'redo'],
                    ])
                    ->plugins(array_filter([
                        $this->getStockImagePlugin(),
                    ]))
                    ->activePanel('mergeTags')
                    ->resizableImages()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->json()
                    ->required(),
                Actions::make([
                    DraftTemplateWithAiAction::make()
                        ->channel(NotificationChannel::Email)
                        ->mergeTags(Engagement::getMergeTags()),
                ]),
            ]);
    }

    protected function getStockImagePlugin(): ?StockImageRichContentPlugin
    {
        $settings = app(StockMediaSettings::class);

        if (! $settings->is_active || $settings->provider !== StockMediaProvider::Pexels || blank($settings->pexels_api_key)) {
            return null;
        }

        return StockImageRichContentPlugin::make(URL::temporarySignedRoute('api.stock-images', now()->addHour()));
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
