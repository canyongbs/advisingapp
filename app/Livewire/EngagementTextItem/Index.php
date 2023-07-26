<?php

namespace App\Livewire\EngagementTextItem;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Livewire\WithSorting;
use App\Models\EngagementTextItem;
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
        $this->orderable = (new EngagementTextItem())->orderable;
    }

    public function render()
    {
        $query = EngagementTextItem::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $engagementTextItems = $query->paginate($this->perPage);

        return view('livewire.engagement-text-item.index', compact('engagementTextItems', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('engagement_text_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        EngagementTextItem::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(EngagementTextItem $engagementTextItem)
    {
        abort_if(Gate::denies('engagement_text_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $engagementTextItem->delete();
    }
}
