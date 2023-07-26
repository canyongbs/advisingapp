<?php

namespace App\Livewire\JourneyTargetList;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Models\JourneyTargetList;
use App\Livewire\WithSorting;
use Illuminate\Support\Facades\Gate;
use App\Livewire\WithConfirmation;

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
            'except' => 'id',
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
        $this->sortBy = 'id';
        $this->sortDirection = 'desc';
        $this->perPage = 100;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new JourneyTargetList())->orderable;
    }

    public function render()
    {
        $query = JourneyTargetList::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $journeyTargetLists = $query->paginate($this->perPage);

        return view('livewire.journey-target-list.index', compact('journeyTargetLists', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('journey_target_list_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        JourneyTargetList::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(JourneyTargetList $journeyTargetList)
    {
        abort_if(Gate::denies('journey_target_list_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $journeyTargetList->delete();
    }
}
