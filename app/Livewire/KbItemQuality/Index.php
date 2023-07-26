<?php

namespace App\Livewire\KbItemQuality;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KbItemQuality;
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
            'except' => 'rating',
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
        $this->sortBy = 'rating';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new KbItemQuality())->orderable;
    }

    public function render()
    {
        $query = KbItemQuality::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $kbItemQualities = $query->paginate($this->perPage);

        return view('livewire.kb-item-quality.index', compact('kbItemQualities', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('kb_item_quality_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        KbItemQuality::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(KbItemQuality $kbItemQuality)
    {
        abort_if(Gate::denies('kb_item_quality_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $kbItemQuality->delete();
    }
}
