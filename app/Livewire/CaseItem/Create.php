<?php

namespace App\Livewire\CaseItem;

use App\Models\User;
use Livewire\Component;
use App\Models\Institution;
use App\Models\RecordStudentItem;
use Assist\CaseModule\Models\CaseItem;
use Assist\CaseModule\Models\CaseItemType;
use Assist\CaseModule\Models\CaseItemStatus;
use Assist\CaseModule\Models\CaseItemPriority;

class Create extends Component
{
    public CaseItem $caseItem;

    public array $listsForFields = [];

    public function mount(CaseItem $caseItem)
    {
        $this->caseItem = $caseItem;
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.case-item.create');
    }

    public function submit()
    {
        $this->validate();

        $this->caseItem->save();

        return redirect()->route('admin.case-items.index');
    }

    protected function rules(): array
    {
        return [
            'caseItem.casenumber' => [
                'integer',
                'min:-2147483648',
                'max:2147483647',
                'required',
            ],
            'caseItem.student_id' => [
                'integer',
                'exists:record_student_items,id',
                'required',
            ],
            'caseItem.institution_id' => [
                'integer',
                'exists:institutions,id',
                'required',
            ],
            'caseItem.state_id' => [
                'integer',
                'exists:case_item_statuses,id',
                'nullable',
            ],
            'caseItem.type_id' => [
                'integer',
                'exists:case_item_types,id',
                'required',
            ],
            'caseItem.priority_id' => [
                'integer',
                'exists:case_item_priorities,id',
                'required',
            ],
            'caseItem.assigned_to_id' => [
                'integer',
                'exists:users,id',
                'nullable',
            ],
            'caseItem.close_details' => [
                'string',
                'nullable',
            ],
            'caseItem.res_details' => [
                'string',
                'nullable',
            ],
            'caseItem.created_by_id' => [
                'integer',
                'exists:users,id',
                'nullable',
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['student'] = RecordStudentItem::pluck('full', 'id')->toArray();
        $this->listsForFields['institution'] = Institution::pluck('name', 'id')->toArray();
        $this->listsForFields['state'] = CaseItemStatus::pluck('status', 'id')->toArray();
        $this->listsForFields['type'] = CaseItemType::pluck('type', 'id')->toArray();
        $this->listsForFields['priority'] = CaseItemPriority::pluck('priority', 'id')->toArray();
        $this->listsForFields['assigned_to'] = User::pluck('name', 'id')->toArray();
        $this->listsForFields['created_by'] = User::pluck('name', 'id')->toArray();
    }
}
