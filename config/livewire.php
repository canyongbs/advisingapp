<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

return [
    /*
    |---------------------------------------------------------------------------
    | Component Locations
    |---------------------------------------------------------------------------
    |
    | This value sets the root directories that'll be used to resolve view-based
    | components like single and multi-file components. The make command will
    | use the first directory in this array to add new component files to.
    |
    */

    'component_locations' => [
        resource_path('views/components'),
        resource_path('views/livewire'),
    ],

    /*
    |---------------------------------------------------------------------------
    | Component Namespaces
    |---------------------------------------------------------------------------
    |
    | This value sets default namespaces that will be used to resolve view-based
    | components like single-file and multi-file components. These folders'll
    | also be referenced when creating new components via the make command.
    |
    */

    'component_namespaces' => [
        'layouts' => resource_path('views/layouts'),
        'pages' => resource_path('views/pages'),
    ],

    /*
    |---------------------------------------------------------------------------
    | Page Layout
    |---------------------------------------------------------------------------
    | The view that will be used as the layout when rendering a single component as
    | an entire page via `Route::livewire('/post/create', 'pages::create-post')`.
    | In this case, the content of pages::create-post will render into $slot.
    |
    */

    'component_layout' => 'components.layouts.app',

    /*
    |---------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |---------------------------------------------------------------------------
    | Livewire allows you to lazy load components that would otherwise slow down
    | the initial page load. Every component can have a custom placeholder or
    | you can define the default placeholder view for all components below.
    |
    */

    'component_placeholder' => null,

    /*
    |---------------------------------------------------------------------------
    | Make Command
    |---------------------------------------------------------------------------
    | This value determines the default configuration for the artisan make command
    | You can configure the component type (sfc, mfc, class) and whether to use
    | the high-voltage (⚡) emoji as a prefix in the sfc|mfc component names.
    |
    */

    'make_command' => [
        'type' => 'class',
        'emoji' => false,
        'with' => [
            'js' => false,
            'css' => false,
            'test' => false,
        ],
    ],

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
    | Class Path
    |---------------------------------------------------------------------------
    |
    | This value is used to specify the path where Livewire component class files
    | are created when running creation commands like `artisan make:livewire`.
    | This path is customizable to match your projects directory structure.
    |
    */

    'class_path' => app_path('Livewire'),

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
        'max_upload_time' => 5, // Max duration (in minutes) before an upload is invalidated...

        /**
         * Supported file types for temporary pre-signed file URLs
         *
         * @see https://docs.cloud.llamaindex.ai/llamaparse/features/supported_document_types
         */
        'preview_mimes' => [
            // Base types
            'pdf',

            // Documents and presentations
            '602',
            'abw',
            'cgm',
            'cwk',
            'doc',
            'docx',
            'docm',
            'dot',
            'dotm',
            'hwp',
            'key',
            'lwp',
            'mw',
            'mcw',
            'pages',
            'pbd',
            'ppt',
            'pptm',
            'pptx',
            'pot',
            'potm',
            'potx',
            'rtf',
            'sda',
            'sdd',
            'sdp',
            'sdw',
            'sgl',
            'sti',
            'sxi',
            'sxw',
            'stw',
            'sxg',
            'txt',
            'uof',
            'uop',
            'uot',
            'vor',
            'wpd',
            'wps',
            'xml',
            'zabw',
            'epub',

            // Images
            'jpg',
            'jpeg',
            'png',
            'gif',
            'bmp',
            'svg',
            'tiff',
            'webp',
            'web',
            'htm',
            'html',

            // Spreadsheets
            'xlsx',
            'xls',
            'xlsm',
            'xlsb',
            'xlw',
            'csv',
            'dif',
            'sylk',
            'slk',
            'prn',
            'numbers',
            'et',
            'ods',
            'fods',
            'uos1',
            'uos2',
            'dbf',
            'wk1',
            'wk2',
            'wk3',
            'wk4',
            'wks',
            '123',
            'wq1',
            'wq2',
            'wb1',
            'wb2',
            'wb3',
            'qpw',
            'xlr',
            'eth',
            'tsv',

            // Audio
            'mp3',
            'mp4',
            'mpeg',
            'mpga',
            'm4a',
            'wav',
            'webm',
        ],

        'cleanup' => true, // Should cleanup temporary uploads older than 24 hrs...
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
    | Smart Wire Keys
    |---------------------------------------------------------------------------
    |
    | Livewire uses loops and keys used within loops to generate smart keys that
    | are applied to nested components that don't have them. This makes using
    | nested components more reliable by ensuring that they all have keys.
    |
    */

    'smart_wire_keys' => true,

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

    /*
    |---------------------------------------------------------------------------
    | Release Token
    |---------------------------------------------------------------------------
    |
    | This token is stored client-side and sent along with each request to check
    | a users session to see if a new release has invalidated it. If there is
    | a mismatch it will throw an error and prompt for a browser refresh.
    |
    */

    'release_token' => 'a',

    /*
    |---------------------------------------------------------------------------
    | CSP Safe
    |---------------------------------------------------------------------------
    |
    | This config is used to determine if Livewire will use the CSP-safe version
    | of Alpine in its bundle. This is useful for applications that are using
    | strict Content Security Policy (CSP) to protect against XSS attacks.
    |
    */

    'csp_safe' => false,

    /*
    |---------------------------------------------------------------------------
    | Payload Guards
    |---------------------------------------------------------------------------
    |
    | These settings protect against malicious or oversized payloads that could
    | cause denial of service. The default values should feel reasonable for
    | most web applications. Each can be set to null to disable the limit.
    |
    */

    'payload' => [
        'max_size' => 5 * 1024 * 1024, // 5MB - raised from 1MB so large Filament forms and rich content are not rejected
        'max_nesting_depth' => 64,   // Raised from 10: Filament's rich editor syncs its TipTap document as deeply nested data (each nested list level adds 4 path segments)
        'max_calls' => 50,           // Maximum method calls per request
        'max_components' => 200,     // Maximum components per batch request
    ],
];
