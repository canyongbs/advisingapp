<?php

namespace App\Livewire\CaseItemType;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Livewire\WithSorting;
use Illuminate\Support\Facades\Gate;
use App\Livewire\WithConfirmation;
use Assist\CaseModule\Models\CaseItemType;

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
        $this->perPage = 10;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new CaseItemType())->orderable;
    }

    public function render()
    {
        $query = CaseItemType::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $caseItemTypes = $query->paginate($this->perPage);

        return view('livewire.case-item-type.index', compact('caseItemTypes', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('case_item_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        CaseItemType::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(CaseItemType $caseItemType)
    {
        abort_if(Gate::denies('case_item_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $caseItemType->delete();
    }
}
