<?php

namespace AdvisingApp\Authorization\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateLoginMagicLinkRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
