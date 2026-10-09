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

namespace AdvisingApp\Application\Filament\Resources\Applications\Pages;

use AdvisingApp\Application\Actions\CreateApplicationVersion;
use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\Concerns\HasSharedFormConfiguration;
use AdvisingApp\Application\Livewire\ApplicationFormManager;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationStep;
use AdvisingApp\Form\Actions\SaveSubmissibleFieldsFromContent;
use CanyonGBS\Common\Filament\Actions\ArchiveAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditApplication extends ApplicationFormManager
{
    use HasSharedFormConfiguration;

    /** @var array<string, mixed>|null */
    protected ?array $versioningFormData = null;

    public function save(): void
    {
        parent::save();

        $this->redirect(ApplicationResource::getUrl('view', [
            'record' => $this->record,
            'tab' => 'edit',
        ]));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->record)
            ->statePath('data')
            ->components($this->fields());
    }

    public function archiveAction(): Action
    {
        return ArchiveAction::make()
            ->record($this->record)
            ->authorize(fn (): bool => ApplicationResource::canDelete($this->record))
            ->successRedirectUrl(ApplicationResource::getUrl('index'));
    }

    protected function beforeSave(): void
    {
        $this->versioningFormData = $this->data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        assert($record instanceof Application);

        return DB::transaction(function () use ($record, $data) {
            $newVersion = app(CreateApplicationVersion::class)->execute($record, $data);

            $this->record = $newVersion;

            app(SaveSubmissibleFieldsFromContent::class)->execute($newVersion, $this->versioningFormData);

            $this->copyMedia($record, $newVersion);

            return $newVersion;
        });
    }

    protected function saveRelationships(): void
    {
        $this->data = $this->versioningFormData ?? [];
        $this->data['content'] = $this->record->content;

        if ($this->record->is_wizard) {
            $oldStepKeys = array_keys($this->data['steps'] ?? []);
            $oldAttachments = $this->componentFileAttachments['data']['steps'] ?? [];
            $this->data['steps'] = [];
            $this->componentFileAttachments['data']['steps'] = [];

            foreach ($this->record->steps->sortBy('sort')->values() as $index => $step) {
                assert($step instanceof ApplicationStep);
                $newKey = "record-{$step->getKey()}";
                $this->data['steps'][$newKey] = $step->attributesToArray();
                $oldKey = $oldStepKeys[$index] ?? null;

                if ($oldKey !== null && isset($oldAttachments[$oldKey])) {
                    $this->componentFileAttachments['data']['steps'][$newKey] = $oldAttachments[$oldKey];
                }
            }

            $repeater = $this->form->getComponent(
                fn (Component $component): bool => $component instanceof Repeater && $component->getName() === 'steps',
                withActions: false,
                withHidden: true,
            );
            assert($repeater instanceof Repeater);
            $repeater->clearCachedExistingRecords();
        }

        parent::saveRelationships();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Save')->submit('save'),
            $this->archiveAction(),
            Action::make('cancel')
                ->label('Cancel')
                ->color('gray')
                ->url(fn () => ApplicationResource::getUrl('view', ['record' => $this->record])),
        ];
    }

    private function copyMedia(Application $oldVersion, Application $newVersion): void
    {
        if ($newVersion->is_wizard) {
            $this->copyStepMedia($oldVersion, $newVersion);
        } else {
            $this->copyApplicationMedia($oldVersion, $newVersion);
        }
    }

    private function copyApplicationMedia(Application $oldVersion, Application $newVersion): void
    {
        $media = $oldVersion->getMedia('content');

        if ($media->isEmpty()) {
            return;
        }

        $uuidMap = [];

        foreach ($media as $item) {
            $newMedia = $item->copy($newVersion, 'content', 's3-public');
            $uuidMap[$item->uuid] = $newMedia->uuid;
        }

        $newVersion->content = $this->remapMediaUuids($newVersion->content, $uuidMap);
        $newVersion->save();
    }

    private function copyStepMedia(Application $oldVersion, Application $newVersion): void
    {
        $oldSteps = $oldVersion->steps()->orderBy('sort')->get();
        $newSteps = $newVersion->steps->sortBy('sort')->values();

        foreach ($oldSteps as $index => $oldStep) {
            $newStep = $newSteps[$index] ?? null;

            if (! $newStep) {
                continue;
            }

            assert($newStep instanceof ApplicationStep);

            $media = $oldStep->getMedia('content');

            if ($media->isEmpty()) {
                continue;
            }

            $uuidMap = [];

            foreach ($media as $item) {
                $newMedia = $item->copy($newStep, 'content', 's3-public');
                $uuidMap[$item->uuid] = $newMedia->uuid;
            }

            $newStep->content = $this->remapMediaUuids($newStep->content, $uuidMap);
            $newStep->save();
        }
    }

    /**
     * @param array<string, mixed>|null $content
     * @param array<string, string> $uuidMap
     *
     * @return array<string, mixed>|null
     */
    private function remapMediaUuids(?array $content, array $uuidMap): ?array
    {
        if (! $content) {
            return $content;
        }

        $json = json_encode($content);
        $json = str_replace(array_keys($uuidMap), array_values($uuidMap), $json);

        return json_decode($json, true);
    }
}
