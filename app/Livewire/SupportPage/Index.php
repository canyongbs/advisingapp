<?php

namespace App\Livewire\SupportPage;

use Livewire\Component;
use App\Models\SupportPage;
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
            'except' => 'title',
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
        $this->sortBy = 'title';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new SupportPage())->orderable;
    }

    public function render()
    {
        $query = SupportPage::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $supportPages = $query->paginate($this->perPage);

        return view('livewire.support-page.index', compact('query', 'supportPages'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('support_page_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        SupportPage::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(SupportPage $supportPage)
    {
        abort_if(Gate::denies('support_page_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $supportPage->delete();
    }
}
