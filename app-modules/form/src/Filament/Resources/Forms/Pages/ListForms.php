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

namespace AdvisingApp\Form\Filament\Resources\Forms\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Actions\DuplicateForm;
use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Models\Form;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ListForms extends ListRecords
{
    protected ?string $heading = 'Online Forms';

    protected static string $resource = FormResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->description(fn (Form $record) => $record->title),
                TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->counts('submissions')
                    ->default(0),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ReplicateAction::make('Duplicate')
                    ->modalHeading('Duplicate Form')
                    ->excludeAttributes(['submissions_count'])
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['name'] = "Copy - {$data['name']}";

                        return $data;
                    })
                    ->schema(function (Schema $schema): Schema {
                        return $schema->components([
                            TextInput::make('name')
                                ->label('Name')
                                ->required(),
                        ]);
                    })
                    ->beforeReplicaSaved(function (Model $replica, array $data): void {
                        $replica->name = $data['name'];
                    })
                    ->after(function (Form $replica, Form $record): void {
                        resolve(DuplicateForm::class, ['original' => $record, 'replica' => $replica])();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('New')
                ->requiresConfirmation()
                ->modalHeading('Create New Form')
                ->modalDescription('Before creating your form, choose how people will access and submit it. These settings are permanent and cannot be changed later.')
                ->modalSubmitActionLabel('Continue')
                ->schema([
                    Toggle::make('is_authenticated')
                        ->label('Requires authentication')
                        ->helperText('If enabled, students and prospects must verify their email address before they can open and submit this form. When someone verifies their email, the form will automatically link their submission to their existing student or prospect record.'),
                    Toggle::make('generate_prospects')
                        ->label('Generate prospects')
                        ->helperText("If enabled, the system will check the email address submitted on the form. If it matches an existing student's institutional email address or prospect's primary email address, the form submission will be linked to that record. If no match is found, a new prospect will be created automatically. Forms that generate prospects must include an email address and name field.")
                        ->disabled(fn () => ! auth()->user()?->hasLicense(LicenseType::RecruitmentCrm))
                        ->hintIcon(fn () => ! auth()->user()?->hasLicense(LicenseType::RecruitmentCrm) ? 'heroicon-m-lock-closed' : null),
                ])
                ->action(fn (array $data) => redirect(FormResource::getUrl('create', [
                    'is_authenticated' => $data['is_authenticated'] ?? false,
                    'generate_prospects' => $data['generate_prospects'] ?? false,
                ]))),
        ];
    }
}
