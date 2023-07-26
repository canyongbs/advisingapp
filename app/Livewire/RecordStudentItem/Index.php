<?php

namespace App\Livewire\RecordStudentItem;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Models\RecordStudentItem;
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
            'except' => 'sisid',
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
        $this->sortBy = 'sisid';
        $this->sortDirection = 'desc';
        $this->perPage = 25;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new RecordStudentItem())->orderable;
    }

    public function render()
    {
        $query = RecordStudentItem::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $recordStudentItems = $query->paginate($this->perPage);

        return view('livewire.record-student-item.index', compact('query', 'recordStudentItems'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('record_student_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        RecordStudentItem::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(RecordStudentItem $recordStudentItem)
    {
        abort_if(Gate::denies('record_student_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $recordStudentItem->delete();
    }
}
