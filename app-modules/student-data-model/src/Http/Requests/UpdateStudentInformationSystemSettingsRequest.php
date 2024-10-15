<?php

namespace AdvisingApp\StudentDataModel\Http\Requests;

use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use AdvisingApp\StudentDataModel\Enums\SisSystem;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateStudentInformationSystemSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_enabled' => ['required', 'boolean'],
            'sis_system' => ['string', 'nullable', new Enum(SisSystem::class)],
        ];
    }
}
