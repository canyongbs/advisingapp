<?php

namespace App\Livewire\RecordProgramItem;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Models\RecordProgramItem;
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
            'except' => 'name',
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
        $this->sortBy = 'name';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new RecordProgramItem())->orderable;
    }

    public function render()
    {
        $query = RecordProgramItem::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $recordProgramItems = $query->paginate($this->perPage);

        return view('livewire.record-program-item.index', compact('query', 'recordProgramItems'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('record_program_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        RecordProgramItem::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(RecordProgramItem $recordProgramItem)
    {
        abort_if(Gate::denies('record_program_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $recordProgramItem->delete();
    }
}
