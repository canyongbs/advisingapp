<?php

namespace App\Livewire\EngagementInteractionItem;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Livewire\WithSorting;
use Illuminate\Support\Facades\Gate;
use App\Livewire\WithConfirmation;
use App\Models\EngagementInteractionItem;

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
        $this->perPage = 25;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new EngagementInteractionItem())->orderable;
    }

    public function render()
    {
        $query = EngagementInteractionItem::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $engagementInteractionItems = $query->paginate($this->perPage);

        return view('livewire.engagement-interaction-item.index', compact('engagementInteractionItems', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('engagement_interaction_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        EngagementInteractionItem::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(EngagementInteractionItem $engagementInteractionItem)
    {
        abort_if(Gate::denies('engagement_interaction_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $engagementInteractionItem->delete();
    }
}
