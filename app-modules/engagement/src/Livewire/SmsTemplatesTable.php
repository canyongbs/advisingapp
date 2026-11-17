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

namespace AdvisingApp\Engagement\Livewire;

use AdvisingApp\Engagement\Filament\Resources\SmsTemplates\SmsTemplateResource;
use AdvisingApp\Engagement\Models\SmsTemplate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SmsTemplatesTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

  public function boot(): void
  {
    abort_unless(SmsTemplateResource::canViewAny(), 403);
  }

    public function table(Table $table): Table
    {
        return $table
            ->query(SmsTemplate::query())
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize('create', SmsTemplate::class)
                    ->url(fn (): string => SmsTemplateResource::getUrl('create')),
            ])
            ->recordActions([
                EditAction::make()
                    ->authorize('update')
                    ->url(fn (SmsTemplate $record): string => SmsTemplateResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize('deleteAny', SmsTemplate::class)
                        ->authorizeIndividualRecords('delete'),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('engagement::livewire.unified-inbox-table');
    }
}
