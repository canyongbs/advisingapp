<?php

namespace App\Livewire\CaseUpdateItem;

use Livewire\Component;
use App\Models\RecordStudentItem;
use Assist\CaseModule\Models\CaseItem;
use Assist\CaseModule\Models\CaseUpdateItem;

class Create extends Component
{
    public array $listsForFields = [];

    public CaseUpdateItem $caseUpdateItem;

    public function mount(CaseUpdateItem $caseUpdateItem)
    {
        $this->caseUpdateItem = $caseUpdateItem;
        $this->caseUpdateItem->internal = 'Y';
        $this->caseUpdateItem->direction = '1';
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.case-update-item.create');
    }

    public function submit()
    {
        $this->validate();

        $this->caseUpdateItem->save();

        return redirect()->route('admin.case-update-items.index');
    }

    protected function rules(): array
    {
        return [
            'caseUpdateItem.student_id' => [
                'integer',
                'exists:record_student_items,id',
                'required',
            ],
            'caseUpdateItem.case_id' => [
                'integer',
                'exists:case_items,id',
                'required',
            ],
            'caseUpdateItem.update' => [
                'string',
                'required',
            ],
            'caseUpdateItem.internal' => [
                'required',
                'in:' . implode(',', array_keys($this->listsForFields['internal'])),
            ],
            'caseUpdateItem.direction' => [
                'required',
                'in:' . implode(',', array_keys($this->listsForFields['direction'])),
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['student'] = RecordStudentItem::pluck('full', 'id')->toArray();
        $this->listsForFields['case'] = CaseItem::pluck('casenumber', 'id')->toArray();
        $this->listsForFields['internal'] = $this->caseUpdateItem::INTERNAL_RADIO;
        $this->listsForFields['direction'] = $this->caseUpdateItem::DIRECTION_RADIO;
    }
}
