<?php

namespace App\Livewire\RecordEnrollmentItem;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Livewire\WithSorting;
use App\Models\RecordEnrollmentItem;
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
        $this->orderable = (new RecordEnrollmentItem())->orderable;
    }

    public function render()
    {
        $query = RecordEnrollmentItem::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $recordEnrollmentItems = $query->paginate($this->perPage);

        return view('livewire.record-enrollment-item.index', compact('query', 'recordEnrollmentItems'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('record_enrollment_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        RecordEnrollmentItem::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(RecordEnrollmentItem $recordEnrollmentItem)
    {
        abort_if(Gate::denies('record_enrollment_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $recordEnrollmentItem->delete();
    }
}
