<?php

namespace AdvisingApp\Form\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSecretRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'value' => ['required_without:previous_secret_id', 'nullable', 'string', 'max:255'],
            'previous_secret_id' => ['nullable', 'uuid'],
        ];
    }
}
