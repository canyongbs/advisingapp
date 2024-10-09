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

namespace AdvisingApp\Portal\Settings;

use App\Settings\SettingsWithMedia;
use AdvisingApp\Portal\Enums\GdprBannerButtonLabel;
use AdvisingApp\Portal\Settings\SettingsProperties\PortalSettingsProperty;

class PortalSettings extends SettingsWithMedia
{
    public null $logo = null;

    public ?string $primary_color = null;

    public ?string $secondary_color = null;

    public bool $has_applications = false;

    public bool $has_message_center = false;

    public bool $has_user_chat = false;

    public bool $has_care_team = false;

    public bool $has_performance_alerts = false;

    public bool $has_emergency_alerts = false;

    public bool $has_notifications = false;

    public bool $has_knowledge_base = false;

    public bool $has_tasks = false;

    public bool $has_files_and_documents = false;

    public bool $has_forms = false;

    public bool $has_surveys = false;

    public ?string $footer_color = null;

    public ?array $footer_copyright_statement = null;

    /**
    * Knowledge Base Portal
    */
    public bool $knowledge_management_portal_enabled = false;

    public bool $knowledge_management_portal_service_management = false;

    public bool $knowledge_management_portal_requires_authentication = false;

    public ?string $knowledge_management_portal_primary_color = null;

    public ?string $knowledge_management_portal_rounding = null;

    public ?string $knowledge_management_portal_authorized_domain = null;

    public array $gdpr_banner_text = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'attrs' => [
                    'textAlign' => 'start',
                ],
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'We use cookies to personalize content, to provide social media features, and to analyze our traffic. We also share information about your use of our site with our partners who may combine it with other information that you\'ve provided to them or that they\'ve collected from your use of their services.',
                    ],
                ],
            ],
        ],
    ];

    public GdprBannerButtonLabel $gdpr_banner_button_label = GdprBannerButtonLabel::AllowCookies;

    public static function getSettingsPropertyModelClass(): string
    {
        return PortalSettingsProperty::class;
    }

    public static function group(): string
    {
        return 'portal';
    }
}
