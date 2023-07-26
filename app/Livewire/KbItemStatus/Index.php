<?php

namespace App\Livewire\KbItemStatus;

use Livewire\Component;
use App\Models\KbItemStatus;
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
            'except' => 'status',
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
        $this->sortBy = 'status';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new KbItemStatus())->orderable;
    }

    public function render()
    {
        $query = KbItemStatus::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $kbItemStatuses = $query->paginate($this->perPage);

        return view('livewire.kb-item-status.index', compact('kbItemStatuses', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('kb_item_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        KbItemStatus::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(KbItemStatus $kbItemStatus)
    {
        abort_if(Gate::denies('kb_item_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $kbItemStatus->delete();
    }
}
