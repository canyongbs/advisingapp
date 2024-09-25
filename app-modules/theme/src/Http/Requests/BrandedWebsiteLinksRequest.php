<?php

namespace AdvisingApp\Theme\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandedWebsiteLinksRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'is_support_url_enabled' => ['nullable', 'boolean'],
            'support_url' => ['nullable', 'string', 'url'],
            'is_recent_updates_url_enabled' => ['nullable', 'boolean'],
            'recent_updates_url' => ['nullable', 'string', 'url'],
            'is_custom_link_url_enabled' => ['nullable', 'boolean'],
            'custom_link_label' => ['nullable', 'string'],
            'custom_link_url' => ['nullable', 'string', 'url'],
            'tenant_id' => ['string', 'required'],
        ];
    }
}
