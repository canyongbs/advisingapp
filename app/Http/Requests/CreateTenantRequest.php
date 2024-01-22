<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', 'unique:landlord.tenants,domain', 'regex:/^((?:[a-zA-Z0-9-]*?\.)?[a-zA-Z0-9-]+\.[a-zA-Z]{2,})$/i'],
        ];
    }
}
