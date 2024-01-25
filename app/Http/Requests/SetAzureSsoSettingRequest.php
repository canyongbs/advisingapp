<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetAzureSsoSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'enabled' => ['required', 'boolean'],
            'tenant_id' => ['required', 'string', 'max:255'],
            'client_id' => ['required', 'string', 'max:255'],
            'client_secret' => ['required', 'string', 'max:255'],
        ];
    }
}
