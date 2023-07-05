<?php

namespace App\Http\Livewire\CaseItem;

use App\Http\Livewire\WithConfirmation;
use App\Http\Livewire\WithSorting;
use App\Models\CaseItem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithSorting, WithConfirmation;

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
        $this->sortBy            = 'id';
        $this->sortDirection     = 'desc';
        $this->perPage           = 25;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable         = (new CaseItem())->orderable;
    }

    public function render()
    {
        $query = CaseItem::with(['student', 'institution', 'state', 'type', 'priority', 'assignedTo', 'createdBy'])->advancedFilter([
            's'               => $this->search ?: null,
            'order_column'    => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $caseItems = $query->paginate($this->perPage);

        return view('livewire.case-item.index', compact('caseItems', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('case_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        CaseItem::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(CaseItem $caseItem)
    {
        abort_if(Gate::denies('case_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $caseItem->delete();
    }
}
