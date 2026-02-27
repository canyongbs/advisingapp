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

namespace AdvisingApp\Group\Filament\Resources\Groups\Pages;

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\GroupType;
use AdvisingApp\Group\Filament\Resources\Groups\GroupResource;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Scopes\UnhealthyEducatablePrimaryEmailAddress;
use AdvisingApp\StudentDataModel\Models\Scopes\UnhealthyEducatablePrimaryPhoneNumber;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends EditRecord<Group>
 */
class EditGroup extends EditRecord implements HasTable
{
    use InteractsWithTable {
        bootedInteractsWithTable as baseBootedInteractsWithTable;
    }
    use EditPageRedirection;

    protected static string $resource = GroupResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->autocomplete(false)
                    ->string()
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Grid::make()
                    ->schema([
                        Select::make('type')
                            ->options(GroupType::class)
                            ->disabled(),
                        Select::make('model')
                            ->label('Population')
                            ->options(GroupModel::class)
                            ->disabled()
                            ->visible(auth()->user()->hasLicense([Student::getLicenseType(), Prospect::getLicenseType()])),
                        TextInput::make('user.name')
                            ->label('User')
                            ->disabled(),
                    ])
                    ->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        $group = $this->getRecord();

        $table = $group->model->table($table);

        if ($group->type === GroupType::Static) {
            $keys = $group->subjects()->pluck('subject_id');

            $table->modifyQueryUsing(fn (Builder $query) => $query->whereKey($keys));
        }

        return $table;
    }

    public function bootedInteractsWithTable(): void
    {
        if ($this->shouldMountInteractsWithTable) {
            $this->tableFilters = $this->getRecord()->filters;
        }

        $this->baseBootedInteractsWithTable();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
                $this->getRelationManagersContentComponent(),
                Callout::make('Warning')
                    ->description(function (): string {
                        $count = $this->getFilteredTableQuery()->count();

                        $unhealthyEmailCount = $this->getFilteredTableQuery()
                            ->tap(new UnhealthyEducatablePrimaryEmailAddress())
                            ->count();
                        $unhealthyEmailPercent = bcround(($count > 0) ? bcmul(bcdiv((string) $unhealthyEmailCount, (string) $count, 4), '100', 2) : '0', 2);

                        $unhealthyPhoneCount = $this->getFilteredTableQuery()
                            ->tap(new UnhealthyEducatablePrimaryPhoneNumber())
                            ->count();
                        $unhealthyPhonePercent = bcround(($count > 0) ? bcmul(bcdiv((string) $unhealthyPhoneCount, (string) $count, 4), '100', 2) : '0', 2);

                        $emailLabel = match ($this->getRecord()->model->class()) {
                            Student::class => 'institutional',
                            Prospect::class => 'primary',
                            default => 'provided',
                        };

                        return "Of the {$count} {$this->getRecord()->model->getPluralLabel()} who will be a member of this group, {$unhealthyEmailPercent}% are unable to receive emails to their {$emailLabel} email address and {$unhealthyPhonePercent}% are unable to receive SMS to their primary phone on file. Campaigns or bulk messages will skip these {$this->getRecord()->model->getPluralLabel()}.";
                    })
                    ->warning()
                    ->visible(function (): bool {
                        if ($this->getFilteredTableQuery()
                            ->tap(new UnhealthyEducatablePrimaryEmailAddress())
                            ->exists()) {
                            return true;
                        }

                        if ($this->getFilteredTableQuery()
                            ->tap(new UnhealthyEducatablePrimaryPhoneNumber())
                            ->exists()) {
                            return true;
                        }

                        return false;
                    }),
                EmbeddedTable::make(),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $group = $this->getRecord();

        $data['model'] = $group->model;
        $data['type'] = $group->type;
        $data['user']['name'] = $group->user->name ?? 'N/A';

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (GroupType::parse($this->data['type']) === GroupType::Dynamic) {
            $data['filters'] = $this->tableFilters ?? [];
        } else {
            $data['filters'] = [];
        }

        return $data;
    }
}
