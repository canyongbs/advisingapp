<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages;

use AdvisingApp\Ai\Actions\GenerateQnaAdvisorWidgetEmbedCode;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Form\Rules\IsDomain;
use App\Features\QnaAdvisorEmbedFeature;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use App\Models\User;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class QnaAdvisorEmbed extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = QnaAdvisorResource::class;

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $title = 'Embed';

    protected static ?string $breadcrumb = 'Embed';

    public static function canAccess(array $parameters = []): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return QnaAdvisorEmbedFeature::active() && $user->can('qna_advisor_embed.view-any') && $user->can('qna_advisor_embed.*.view') && parent::canAccess($parameters);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Embed Advisor')
                    ->schema([
                        Toggle::make('is_embed_enabled')
                            ->label('Enable Embed')
                            ->live(),
                        TagsInput::make('authorized_domains')
                            ->label('Authorized Domains')
                            ->helperText('Only these domains will be allowed to embed this QnA Advisor.')
                            ->placeholder('example.com')
                            ->hidden(fn (Get $get) => ! $get('is_embed_enabled'))
                            ->disabled(fn (Get $get) => ! $get('is_embed_enabled'))
                            ->nestedRecursiveRules(
                                [
                                    'string',
                                    new IsDomain(),
                                ]
                            ),
                        Actions::make([
                            Action::make('embed_snippet')
                                ->label('Embed Snippet')
                                ->infolist(
                                    [
                                        TextEntry::make('snippet')
                                            ->label('Click to Copy')
                                            ->state(function (): HtmlString {
                                                $code = resolve(GenerateQnaAdvisorWidgetEmbedCode::class)->handle($this->getRecord());

                                                $state = <<<EOD
                                                ```
                                                {$code}
                                                ```
                                                EOD;

                                                return str($state)->markdown()->toHtmlString();
                                            })
                                            ->copyable()
                                            ->copyableState(fn () => resolve(GenerateQnaAdvisorWidgetEmbedCode::class)->handle($this->getRecord()))
                                            ->copyMessage('Copied!')
                                            ->copyMessageDuration(1500)
                                            ->extraAttributes(['class' => 'embed-code-snippet'])
                                            ->columnSpanFull(),
                                    ]
                                )
                                ->modalSubmitAction(false)
                                ->modalCancelActionLabel('Close')
                                ->visible(fn (Get $get) => $get('is_embed_enabled')),
                        ]),
                    ]),
            ]);
    }

    public function getRecord(): QnaAdvisor
    {
        $record = parent::getRecord();
        assert($record instanceof QnaAdvisor, 'Record must be an instance of QnaAdvisor');

        return $record;
    }
}
