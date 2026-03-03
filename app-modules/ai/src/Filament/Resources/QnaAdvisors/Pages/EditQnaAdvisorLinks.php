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

namespace AdvisingApp\Ai\Filament\Resources\QnaAdvisors\Pages;

use AdvisingApp\Ai\Filament\Resources\QnaAdvisors\QnaAdvisorResource;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorLink;
use App\Features\CurrentQnaAdvisorLinks;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Str;
use UnitEnum;

class EditQnaAdvisorLinks extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = QnaAdvisorResource::class;

    protected static ?string $title = 'Websites';

    protected static ?string $navigationLabel = 'Websites';

    protected static string | UnitEnum | null $navigationGroup = 'Configuration';

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var QnaAdvisor $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('links')
                    ->schema([
                        TextInput::make('url')
                            ->label('URL')
                            ->required()
                            ->disabled(fn (?QnaAdvisorLink $record): bool => $record !== null)
                            ->url(),
                        Toggle::make('is_current')
                            ->visible(CurrentQnaAdvisorLinks::active())
                            ->label('Keep Current')
                            ->helperText('Select this option if you would like to this AI advisor to check for updates on a monthly basis.'),
                    ])
                    ->relationship()
                    ->hiddenLabel()
                    ->addActionLabel('Add website')
                    ->addActionAlignment(Alignment::Start)
                    ->maxItems(25)
                    ->columnSpanFull(),
            ]);
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }
}
