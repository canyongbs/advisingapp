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

namespace AdvisingApp\Form\Filament\Resources\Forms\Actions;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Actions\DuplicateForm;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Filament\Resources\Forms\Schemas\FormAccessSettingsForm;
use AdvisingApp\Form\Models\Form;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class DuplicateFormAction
{
    public static function make(): ReplicateAction
    {
        return ReplicateAction::make('Duplicate')
            ->modalHeading('Duplicate Form')
            ->modalDescription('Choose how people will access and submit the duplicated form. These settings are permanent and cannot be changed later.')
            ->slideOver()
            ->excludeAttributes(['submissions_count'])
            ->mutateRecordDataUsing(function (array $data): array {
                $data['name'] = "Copy - {$data['name']}";

                if (! self::canGenerateProspects()) {
                    $data['generate_prospects'] = false;
                }

                return $data;
            })
            ->schema(fn (Form $record): array => [
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        table: 'forms',
                        column: 'name',
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->whereNull('archived_at'),
                    ),
                Group::make()
                    ->schema(fn (Schema $schema): Schema => FormAccessSettingsForm::configure($schema)),
                Callout::make('Mapped fields will be removed')
                    ->warning()
                    ->description('Mapped fields are only available on forms that require authentication or generate prospects, so they will not be copied to the duplicated form.')
                    ->visible(fn (Get $get): bool => (! $get('is_authenticated'))
                        && (! $get('generate_prospects'))
                        && $record->fields()->whereIn('type', FormFieldBlockRegistry::getMappedBlockTypes())->exists()),
            ])
            ->beforeReplicaSaved(function (Form $replica, array $data): void {
                $replica->name = $data['name'];
                $replica->is_authenticated = (bool) ($data['is_authenticated'] ?? false);
                $replica->generate_prospects = self::canGenerateProspects() && ((bool) ($data['generate_prospects'] ?? false));

                if (! $replica->is_authenticated) {
                    $replica->allow_view_past_submissions = false;
                }

                $replica->setAttribute('root_id', null);
                $replica->archived_at = null;
            })
            ->after(function (Form $replica, Form $record): void {
                resolve(DuplicateForm::class, ['original' => $record, 'replica' => $replica])();
            })
            ->successRedirectUrl(fn (Form $replica): string => FormResource::getUrl('view', ['record' => $replica, 'tab' => 'edit']));
    }

    private static function canGenerateProspects(): bool
    {
        return (bool) auth()->user()?->hasLicense(LicenseType::RecruitmentCrm);
    }
}
