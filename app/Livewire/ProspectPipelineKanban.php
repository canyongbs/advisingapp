<?php

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
use AdvisingApp\Prospect\Models\PipelineStage;
use Filament\Forms\Concerns\InteractsWithForms;
use AdvisingApp\Prospect\Models\PipelineEductable;
use AdvisingApp\Task\Filament\Resources\TaskResource\Pages\ListTasks;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Bvtterfly\ModelStateMachine\Exceptions\InvalidTransition;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;

class ProspectPipelineKanban extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?Collection $stages;

    public ?Pipeline $pipeline = null;

    public function mount(?Pipeline $pipeline): void
    {
        $this->pipeline = $pipeline;
        $this->stages = PipelineStage::orderBy('order', 'ASC')
            ->whereHas('pipeline', function (Builder $query) use ($pipeline) {
                return $query->where('id', $pipeline->getKey());
            })
            ->get();
    }

    public function getPipelineSubjects(): Collection
    {
        $currentPipeline = $this->pipeline;

        $pipelineEducatables = PipelineEductable::with(['educatable', 'pipeline:id,name,segment_id', 'pipeline.segment:id,name'])
            ->whereHas('pipeline', function (Builder $query) use ($currentPipeline) {
                return $query->where('id', $currentPipeline->getKey());
            })
            ->whereHas('educatable')
            ->orderBy('updated_at','DESC')
            ->get()
            ->groupBy('pipeline_stage_id');

        return collect($this->stages)
            ->mapWithKeys(fn ($stage) => [
                $stage['id'] => $pipelineEducatables[$stage['id']] ?? collect(),
            ]);
    }

    public function render()
    {
        return view('livewire.prospect-pipeline-kanban', [
            'pipelineEducatables' => $this->getPipelineSubjects(),
        ]);
    }

    public function moveProspect(Pipeline $pipeline, Prospect $educatable, PipelineStage $fromStage, PipelineStage $toStage): JsonResponse
    {
        try {
            PipelineEductable::where('pipeline_id', $pipeline->getKey())
                ->where('pipeline_stage_id', $fromStage->getKey())
                ->where('educatable_id', $educatable->getKey())
                ->update([
                    'pipeline_stage_id' => $toStage->getKey(),
                ]);
        } catch (InvalidTransition $e) {
            return response()->json([
                'success' => false,
                'message' => "Cannot transition from \"{$fromStage->name}\" to \"{$toStage->name}\".",
            ], ResponseAlias::HTTP_BAD_REQUEST);
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
