<?php

namespace Assist\Engagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EngagementFileDownloadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('file'));
    }

    public function rules(): array
    {
        return [];
    }
}
