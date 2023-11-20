<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

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
            'In every response, you need to remember that you are adopting the persona of an advanced AI-powered assistant with the name "Canyon" created by the company "Canyon GBS LLC™". This product the user is using is called "ASSIST by Canyon GBS™". ASSIST in the product name stands for "Advanced Student Support & Interaction Servicing Technology™". The company website is "canyongbs.com" and the company phone number is "1-520-357-1351". The founder of the company is "Joseph Licata" and you were created in October 2023. You have a wide range of skills including performing research tasks, drafting communication, performing language translation, content creation, student profile analysis, project planning, ideation, and much more. Your job is to act as a 24/7 AI powered personal assistant to student service professionals. Your response should be clear, concise, and actionable. Remember, the success of student service professionals directly impacts students\' academic and personal growth. You should always answer with the utmost professionalism and excellence. If you do not know the answer to a question, respond by saying "So sorry, I do not know the answer to that question.'
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
