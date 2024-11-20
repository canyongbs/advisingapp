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

namespace App\Livewire;

use Exception;
use Livewire\Component;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Filament\Forms\Contracts\HasForms;
use AdvisingApp\Prospect\Models\Pipeline;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Contracts\HasActions;
use AdvisingApp\Audit\Overrides\MorphToMany;
use AdvisingApp\Prospect\Models\PipelineStage;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use AdvisingApp\Prospect\Models\EducatablePipelineStage;
use AdvisingApp\Segment\Actions\TranslateSegmentFilters;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProspectPipelineKanban extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?Pipeline $pipeline = null;

    public function mount(?Pipeline $pipeline): void
    {
        $this->pipeline = $pipeline;
    }

    public function getPipelineSubjects(): Collection
    {
        $currentPipeline = $this->pipeline;

        $pipelineEducatables = app(TranslateSegmentFilters::class)->handle($currentPipeline->segment)
            ->with(['educatablePipelineStages' => fn (MorphToMany $query) => $query->where('pipelines.id', $currentPipeline->getKey())])
            ->get()
            ->groupBy(fn (Prospect $prospect) => $prospect->educatablePipelineStages->first()?->pivot->pipeline_stage_id);

        return $pipelineEducatables;
    }

    public function getStages()
    {
        return PipelineStage::orderBy('order', 'ASC')
            ->whereHas('pipeline', function (Builder $query) {
                return $query->where('id', $this->pipeline->getKey());
            })
            ->pluck('name', 'id')
            ->prepend($this->pipeline->default_stage, '');
    }

    public function render()
    {
        return view('livewire.prospect-pipeline-kanban', [
            'pipelineEducatables' => $this->getPipelineSubjects(),
            'stages' => $this->getStages(),
        ]);
    }

    public function moveProspect(Pipeline $pipeline, Prospect $educatable, $fromStage = '', $toStage = ''): JsonResponse
    {
        try {
            if (blank($fromStage)) {
                $pipeline?->educatablePipelineStages()->attach($educatable, [
                    'pipeline_stage_id' => PipelineStage::find($toStage)->getKey(),
                ]);
            } elseif (blank($toStage)) {
                $pipeline?->educatablePipelineStages()->detach($educatable);
            } else {
                EducatablePipelineStage::where('pipeline_id', $pipeline->getKey())
                    ->where('pipeline_stage_id', $fromStage)
                    ->where('educatable_id', $educatable->getKey())
                    ->update([
                        'pipeline_stage_id' => $toStage,
                    ]);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Pipline could not be moved. Something went wrong, if this continues please contact support.',
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'message' => 'Prospect stage updated successfully.',
        ], ResponseAlias::HTTP_OK);
    }
}
