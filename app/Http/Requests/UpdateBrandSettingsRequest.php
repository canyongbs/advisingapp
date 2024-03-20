<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'color_overrides' => ['nullable', 'array'],
            'custom_css' => ['nullable', 'string'],
            'has_dark_mode' => ['nullable', 'boolean'],
        ];
    }
}
