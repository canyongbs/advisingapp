<?php

namespace AdvisingApp\Ai\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowThreadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->thread->user()->is(auth()->user());
    }
}
