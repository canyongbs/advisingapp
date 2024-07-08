<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

return [
    /*
    |---------------------------------------------------------------------------
    | Class Namespace
    |---------------------------------------------------------------------------
    |
    | This value sets the root class namespace for Livewire component classes in
    | your application. This value will change where component auto-discovery
    | finds components. It's also referenced by the file creation commands.
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |---------------------------------------------------------------------------
    | View Path
    |---------------------------------------------------------------------------
    |
    | This value is used to specify where Livewire component Blade templates are
    | stored when running file creation commands like `artisan make:livewire`.
    | It is also used if you choose to omit a component's render() method.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |---------------------------------------------------------------------------
    | Layout
    |---------------------------------------------------------------------------
    | The view that will be used as the layout when rendering a single component
    | as an entire page via `Route::get('/post/create', CreatePost::class);`.
    | In this case, the view returned by CreatePost will render into $slot.
    |
    */

    'layout' => 'components.layouts.app',

    /*
    |---------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |---------------------------------------------------------------------------
    | Livewire allows you to lazy load components that would otherwise slow down
    | the initial page load. Every component can have a custom placeholder or
    | you can define the default placeholder view for all components below.
    |
    */

    'lazy_placeholder' => null,

    /*
    |---------------------------------------------------------------------------
    | Temporary File Uploads
    |---------------------------------------------------------------------------
    |
    | Livewire handles file uploads by storing uploads in a temporary directory
    | before the file is stored permanently. All file uploads are directed to
    | a global endpoint for temporary storage. You may configure this below:
    |
    */

    'temporary_file_upload' => [
        'disk' => null,        // Example: 'local', 's3'              | Default: 'default'
        'rules' => null,       // Example: ['file', 'mimes:png,jpg']  | Default: ['required', 'file', 'max:12288'] (12MB)
        'directory' => null,   // Example: 'tmp'                      | Default: 'livewire-tmp'
        'middleware' => null,  // Example: 'throttle:5,1'             | Default: 'throttle:60,1'
        'preview_mimes' => [   // Supported file types for temporary pre-signed file URLs...
            'csv', 'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma', 'doc', 'docx',
            'json', 'c', 'cs', 'cpp', 'html', 'java', 'md', 'pdf', 'php',
            'pptx', 'py', 'rb', 'tex', 'txt', 'css', 'js', 'sh', 'ts',
        ],
        'max_upload_time' => 5, // Max duration (in minutes) before an upload is invalidated...
    ],

    /*
    |---------------------------------------------------------------------------
    | Render On Redirect
    |---------------------------------------------------------------------------
    |
    | This value determines if Livewire will run a component's `render()` method
    | after a redirect has been triggered using something like `redirect(...)`
    | Setting this to true will render the view once more before redirecting
    |
    */

    'render_on_redirect' => false,

    /*
    |---------------------------------------------------------------------------
    | Eloquent Model Binding
    |---------------------------------------------------------------------------
    |
    | Previous versions of Livewire supported binding directly to eloquent model
    | properties using wire:model by default. However, this behavior has been
    | deemed too "magical" and has therefore been put under a feature flag.
    |
    */

    'legacy_model_binding' => false,

    /*
    |---------------------------------------------------------------------------
    | Auto-inject Frontend Assets
    |---------------------------------------------------------------------------
    |
    | By default, Livewire automatically injects its JavaScript and CSS into the
    | <head> and <body> of pages containing Livewire components. By disabling
    | this behavior, you need to use @livewireStyles and @livewireScripts.
    |
    */

    'inject_assets' => true,

    /*
    |---------------------------------------------------------------------------
    | Navigate (SPA mode)
    |---------------------------------------------------------------------------
    |
    | By adding `wire:navigate` to links in your Livewire application, Livewire
    | will prevent the default link handling and instead request those pages
    | via AJAX, creating an SPA-like effect. Configure this behavior here.
    |
    */

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#2299dd',
    ],

    /*
    |---------------------------------------------------------------------------
    | HTML Morph Markers
    |---------------------------------------------------------------------------
    |
    | Livewire intelligently "morphs" existing HTML into the newly rendered HTML
    | after each update. To make this process more reliable, Livewire injects
    | "markers" into the rendered Blade surrounding @if, @class & @foreach.
    |
    */

    'inject_morph_markers' => true,

    /*
    |---------------------------------------------------------------------------
    | Pagination Theme
    |---------------------------------------------------------------------------
    |
    | When enabling Livewire's pagination feature by using the `WithPagination`
    | trait, Livewire will use Tailwind templates to render pagination views
    | on the page. If you want Bootstrap CSS, you can specify: "bootstrap"
    |
    */

    'pagination_theme' => 'tailwind',
];
