<?php

namespace App\Livewire\ProspectItem;

use App\Models\User;
use Livewire\Component;
use App\Models\ProspectItem;
use App\Models\ProspectSource;
use App\Models\ProspectStatus;

class Edit extends Component
{
    public array $listsForFields = [];

    public ProspectItem $prospectItem;

    public function mount(ProspectItem $prospectItem)
    {
        $this->prospectItem = $prospectItem;
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.prospect-item.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->prospectItem->save();

        return redirect()->route('admin.prospect-items.index');
    }

    protected function rules(): array
    {
        return [
            'prospectItem.first' => [
                'string',
                'required',
            ],
            'prospectItem.last' => [
                'string',
                'required',
            ],
            'prospectItem.full' => [
                'string',
                'required',
            ],
            'prospectItem.preferred' => [
                'string',
                'nullable',
            ],
            'prospectItem.description' => [
                'string',
                'nullable',
            ],
            'prospectItem.email' => [
                'email:rfc',
                'nullable',
            ],
            'prospectItem.email_2' => [
                'email:rfc',
                'nullable',
            ],
            'prospectItem.mobile' => [
                'integer',
                'min:-2147483648',
                'max:2147483647',
                'nullable',
            ],
            'prospectItem.sms_opt_out' => [
                'nullable',
                'in:' . implode(',', array_keys($this->listsForFields['sms_opt_out'])),
            ],
            'prospectItem.email_bounce' => [
                'nullable',
                'in:' . implode(',', array_keys($this->listsForFields['email_bounce'])),
            ],
            'prospectItem.status_id' => [
                'integer',
                'exists:prospect_statuses,id',
                'nullable',
            ],
            'prospectItem.source_id' => [
                'integer',
                'exists:prospect_sources,id',
                'nullable',
            ],
            'prospectItem.phone' => [
                'integer',
                'min:-2147483648',
                'max:2147483647',
                'nullable',
            ],
            'prospectItem.address' => [
                'string',
                'nullable',
            ],
            'prospectItem.address_2' => [
                'string',
                'nullable',
            ],
            'prospectItem.birthdate' => [
                'nullable',
                'date_format:' . config('project.date_format'),
            ],
            'prospectItem.hsgrad' => [
                'string',
                'nullable',
            ],
            'prospectItem.hsdate' => [
                'nullable',
                'date_format:' . config('project.date_format'),
            ],
            'prospectItem.assigned_to_id' => [
                'integer',
                'exists:users,id',
                'nullable',
            ],
            'prospectItem.created_by_id' => [
                'integer',
                'exists:users,id',
                'nullable',
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['sms_opt_out'] = $this->prospectItem::SMS_OPT_OUT_RADIO;
        $this->listsForFields['email_bounce'] = $this->prospectItem::EMAIL_BOUNCE_RADIO;
        $this->listsForFields['status'] = ProspectStatus::pluck('status', 'id')->toArray();
        $this->listsForFields['source'] = ProspectSource::pluck('source', 'id')->toArray();
        $this->listsForFields['assigned_to'] = User::pluck('name', 'id')->toArray();
        $this->listsForFields['created_by'] = User::pluck('name', 'id')->toArray();
    }
}
