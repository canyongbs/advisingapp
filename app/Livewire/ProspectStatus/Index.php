<?php

namespace App\Livewire\ProspectStatus;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Models\ProspectStatus;
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
        $this->orderable = (new ProspectStatus())->orderable;
    }

    public function render()
    {
        $query = ProspectStatus::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $prospectStatuses = $query->paginate($this->perPage);

        return view('livewire.prospect-status.index', compact('prospectStatuses', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('prospect_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        ProspectStatus::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(ProspectStatus $prospectStatus)
    {
        abort_if(Gate::denies('prospect_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $prospectStatus->delete();
    }
}
