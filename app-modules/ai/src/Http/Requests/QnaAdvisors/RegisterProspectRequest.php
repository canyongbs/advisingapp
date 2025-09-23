<?php

namespace AdvisingApp\Ai\Http\Requests\QnaAdvisors;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterProspectRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['email', 'string', 'required', Rule::unique('prospect_email_addresses', 'address')],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'preferred' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'max:255'],
            'birthdate' => ['required', 'date'],
            'address' => ['required', 'string', 'max:255'],
            'address_2' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'postal' => ['required', 'max:255'],
        ];
    }
}
