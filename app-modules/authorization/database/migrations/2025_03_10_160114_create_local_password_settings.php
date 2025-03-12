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

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('local-password.minPasswordLength', 8);
        $this->migrator->add('local-password.maxPasswordLength', 64);
        $this->migrator->add('local-password.minUppercaseLetters', 1);
        $this->migrator->add('local-password.minLowercaseLetters', 1);
        $this->migrator->add('local-password.minDigits', 1);
        $this->migrator->add('local-password.minSpecialCharacters', 1);
        $this->migrator->add('local-password.numPreviousPasswords', 5);
        $this->migrator->add('local-password.maxPasswordAge', null);
        $this->migrator->add('local-password.blacklistCommonPasswords', true);
    }

    public function down(): void
    {
        $this->migrator->delete('local-password.minPasswordLength');
        $this->migrator->delete('local-password.maxPasswordLength');
        $this->migrator->delete('local-password.minUppercaseLetters');
        $this->migrator->delete('local-password.minLowercaseLetters');
        $this->migrator->delete('local-password.minDigits');
        $this->migrator->delete('local-password.minSpecialCharacters');
        $this->migrator->delete('local-password.numPreviousPasswords');
        $this->migrator->delete('local-password.maxPasswordAge');
        $this->migrator->delete('local-password.blacklistCommonPasswords');
    }
};
