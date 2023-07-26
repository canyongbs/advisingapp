<?php

namespace App\Livewire\JourneyItem;

use Livewire\Component;
use App\Models\JourneyItem;
use Livewire\WithPagination;
use Illuminate\Http\Response;
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
        $this->orderable = (new JourneyItem())->orderable;
    }

    public function render()
    {
        $query = JourneyItem::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $journeyItems = $query->paginate($this->perPage);

        return view('livewire.journey-item.index', compact('journeyItems', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('journey_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        JourneyItem::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(JourneyItem $journeyItem)
    {
        abort_if(Gate::denies('journey_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $journeyItem->delete();
    }
}
