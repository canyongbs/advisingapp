<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Ai\Settings;

use Spatie\LaravelSettings\Settings;

class AiIntegrationsSettings extends Settings
{
    public ?string $open_ai_gpt_35_base_uri = null;

    public ?string $open_ai_gpt_35_api_key = null;

    public ?string $open_ai_gpt_35_model = null;

    public ?string $open_ai_gpt_4_base_uri = null;

    public ?string $open_ai_gpt_4_api_key = null;

    public ?string $open_ai_gpt_4_model = null;

    public ?string $open_ai_gpt_4o_base_uri = null;

    public ?string $open_ai_gpt_4o_api_key = null;

    public ?string $open_ai_gpt_4o_model = null;

    public ?string $open_ai_gpt_4o_mini_base_uri = null;

    public ?string $open_ai_gpt_4o_mini_api_key = null;

    public ?string $open_ai_gpt_4o_mini_model = null;

    public ?string $open_ai_gpt_o1_mini_base_uri = null;

    public ?string $open_ai_gpt_o1_mini_api_key = null;

    public ?string $open_ai_gpt_o1_mini_model = null;

    public ?string $open_ai_gpt_o3_mini_base_uri = null;

    public ?string $open_ai_gpt_o3_mini_api_key = null;

    public ?string $open_ai_gpt_o3_mini_model = null;

    public ?string $open_ai_gpt_41_mini_base_uri = null;

    public ?string $open_ai_gpt_41_mini_api_key = null;

    public ?string $open_ai_gpt_41_mini_model = null;

    public ?string $open_ai_gpt_41_nano_base_uri = null;

    public ?string $open_ai_gpt_41_nano_api_key = null;

    public ?string $open_ai_gpt_41_nano_model = null;

    public static function group(): string
    {
        return 'ai';
    }

    public static function encrypted(): array
    {
        return [
            'open_ai_gpt_35_base_uri',
            'open_ai_gpt_35_api_key',
            'open_ai_gpt_35_model',
            'open_ai_gpt_4_base_uri',
            'open_ai_gpt_4_api_key',
            'open_ai_gpt_4_model',
            'open_ai_gpt_4o_base_uri',
            'open_ai_gpt_4o_api_key',
            'open_ai_gpt_4o_model',
            'open_ai_gpt_4o_mini_base_uri',
            'open_ai_gpt_4o_mini_api_key',
            'open_ai_gpt_4o_mini_model',
            'open_ai_gpt_o1_mini_base_uri',
            'open_ai_gpt_o1_mini_api_key',
            'open_ai_gpt_o1_mini_model',
            'open_ai_gpt_o3_mini_base_uri',
            'open_ai_gpt_o3_mini_api_key',
            'open_ai_gpt_o3_mini_model',
            'open_ai_gpt_41_mini_base_uri',
            'open_ai_gpt_41_mini_api_key',
            'open_ai_gpt_41_mini_model',
            'open_ai_gpt_41_nano_base_uri',
            'open_ai_gpt_41_nano_api_key',
            'open_ai_gpt_41_nano_model',
        ];
    }
}
