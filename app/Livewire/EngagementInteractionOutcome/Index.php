<?php

namespace App\Livewire\EngagementInteractionOutcome;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Livewire\WithSorting;
use Illuminate\Support\Facades\Gate;
use App\Livewire\WithConfirmation;
use App\Models\EngagementInteractionOutcome;

class Index extends Component
{
    use WithPagination;
    use WithSorting;
    use WithConfirmation;

    public int $perPage;

    public array $orderable;

    public string $search = '';

    public array $selected = [];

    public array $paginationOptions;

    protected $queryString = [
        'search' => [
            'except' => '',
        ],
        'sortBy' => [
            'except' => 'outcome',
        ],
        'sortDirection' => [
            'except' => 'desc',
        ],
    ];

    public function getSelectedCountProperty()
    {
        return count($this->selected);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetSelected()
    {
        $this->selected = [];
    }

    public function mount()
    {
        $this->sortBy = 'outcome';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new EngagementInteractionOutcome())->orderable;
    }

    public function render()
    {
        $query = EngagementInteractionOutcome::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $engagementInteractionOutcomes = $query->paginate($this->perPage);

        return view('livewire.engagement-interaction-outcome.index', compact('engagementInteractionOutcomes', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('engagement_interaction_outcome_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        EngagementInteractionOutcome::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(EngagementInteractionOutcome $engagementInteractionOutcome)
    {
        abort_if(Gate::denies('engagement_interaction_outcome_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $engagementInteractionOutcome->delete();
    }
}
