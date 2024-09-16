<?php

use AdvisingApp\Portal\Enums\GdprBannerButtonLabel;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add(
            'portal.gdpr_banner_text',
            [
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
            ]
        );

        $this->migrator->add('portal.gdpr_banner_button_label', GdprBannerButtonLabel::AllowCookies);
    }

    public function down(): void
    {
        $this->migrator->delete('portal.gdpr_banner_text');
        $this->migrator->delete('portal.gdpr_banner_button_label');
    }
};
