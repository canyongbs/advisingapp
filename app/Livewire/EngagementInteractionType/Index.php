<?php

namespace App\Livewire\EngagementInteractionType;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Livewire\WithSorting;
use Illuminate\Support\Facades\Gate;
use App\Livewire\WithConfirmation;
use App\Models\EngagementInteractionType;

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
            'except' => 'type',
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
        $this->sortBy = 'type';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new EngagementInteractionType())->orderable;
    }

    public function render()
    {
        $query = EngagementInteractionType::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $engagementInteractionTypes = $query->paginate($this->perPage);

        return view('livewire.engagement-interaction-type.index', compact('engagementInteractionTypes', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('engagement_interaction_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        EngagementInteractionType::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(EngagementInteractionType $engagementInteractionType)
    {
        abort_if(Gate::denies('engagement_interaction_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $engagementInteractionType->delete();
    }
}
