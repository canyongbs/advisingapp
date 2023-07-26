<?php

namespace App\Livewire\JourneyEmailItem;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Models\JourneyEmailItem;
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
        $this->orderable = (new JourneyEmailItem())->orderable;
    }

    public function render()
    {
        $query = JourneyEmailItem::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $journeyEmailItems = $query->paginate($this->perPage);

        return view('livewire.journey-email-item.index', compact('journeyEmailItems', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('journey_email_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        JourneyEmailItem::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(JourneyEmailItem $journeyEmailItem)
    {
        abort_if(Gate::denies('journey_email_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $journeyEmailItem->delete();
    }
}
