<?php

namespace App\Livewire\ProspectItem;

use Livewire\Component;
use App\Models\ProspectItem;
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
            'except' => 'full',
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
        $this->sortBy = 'full';
        $this->sortDirection = 'desc';
        $this->perPage = 25;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new ProspectItem())->orderable;
    }

    public function render()
    {
        $query = ProspectItem::with(['status', 'source', 'assignedTo', 'createdBy'])->advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $prospectItems = $query->paginate($this->perPage);

        return view('livewire.prospect-item.index', compact('prospectItems', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('prospect_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        ProspectItem::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(ProspectItem $prospectItem)
    {
        abort_if(Gate::denies('prospect_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $prospectItem->delete();
    }
}
