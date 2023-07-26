<?php

namespace App\Livewire\EngagementEmailItem;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Livewire\WithSorting;
use App\Models\EngagementEmailItem;
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
            'except' => 'email',
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
        $this->sortBy = 'email';
        $this->sortDirection = 'desc';
        $this->perPage = 25;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new EngagementEmailItem())->orderable;
    }

    public function render()
    {
        $query = EngagementEmailItem::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $engagementEmailItems = $query->paginate($this->perPage);

        return view('livewire.engagement-email-item.index', compact('engagementEmailItems', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('engagement_email_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        EngagementEmailItem::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(EngagementEmailItem $engagementEmailItem)
    {
        abort_if(Gate::denies('engagement_email_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $engagementEmailItem->delete();
    }
}
