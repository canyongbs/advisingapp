<?php

namespace AdvisingApp\Portal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KnowledgeManagementPortalAuthenticationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'isSpa' => ['required', 'boolean'],
        ];
    }
}
