<?php

namespace AdvisingApp\Prospect\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProspectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['email', 'string', 'required', Rule::unique('prospects', 'email')],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'preferred' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'max:255'],
            'bitrhdate' => ['required', 'date'],
            'address' => ['required', 'string', 'max:255'],
            'address_2' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'postal' => ['required', 'max:255'],
        ];
    }
}
