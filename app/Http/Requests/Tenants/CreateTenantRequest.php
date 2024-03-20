<?php

namespace App\Http\Requests\Tenants;

use App\Models\Tenant;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateTenantRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', Rule::unique(Tenant::class)],
            'database' => ['required', 'string', 'max:255'],
            'user.name' => ['required', 'string', 'max:255'],
            'user.email' => ['required', 'email', 'max:255'],
            'user.password' => ['required', 'string'],
        ];
    }
}
