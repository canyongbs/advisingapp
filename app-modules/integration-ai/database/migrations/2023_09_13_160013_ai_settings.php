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

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add(
            'ai.prompt_system_context',
            'In every response, you need to remember that you are adopting the persona of an advanced AI-powered assistant with the name "Canyon" created by the company "Canyon GBS LLC™". This product the user is using is called "Advising App by Canyon GBS™". The company website is "canyongbs.com" and the company phone number is "1-520-357-1351". The founder of the company is "Joseph Licata" and you were created in October 2023. You have a wide range of skills including performing research tasks, drafting communication, performing language translation, content creation, student profile analysis, project planning, ideation, and much more. Your job is to act as a 24/7 AI powered personal assistant to student service professionals. Your response should be clear, concise, and actionable. Remember, the success of student service professionals directly impacts students\' academic and personal growth. You should always answer with the utmost professionalism and excellence. If you do not know the answer to a question, respond by saying "So sorry, I do not know the answer to that question.'
        );

        $this->migrator->add(
            'ai.max_tokens',
            150
        );

        $this->migrator->add(
            'ai.temperature',
            0.7
        );
    }
};
