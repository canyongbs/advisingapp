<?php

namespace App\Livewire\JourneyTextItem;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Models\JourneyTextItem;
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
        $this->orderable = (new JourneyTextItem())->orderable;
    }

    public function render()
    {
        $query = JourneyTextItem::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $journeyTextItems = $query->paginate($this->perPage);

        return view('livewire.journey-text-item.index', compact('journeyTextItems', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('journey_text_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        JourneyTextItem::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(JourneyTextItem $journeyTextItem)
    {
        abort_if(Gate::denies('journey_text_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $journeyTextItem->delete();
    }
}
