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

namespace Database\Seeders;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\BouncedEmailAddress;
use AdvisingApp\StudentDataModel\Models\BouncedPhoneNumber;
use AdvisingApp\StudentDataModel\Models\EmailAddressOptInOptOut;
use AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use Illuminate\Database\Seeder;

class ContactabilityTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedStudents();
        $this->seedProspects();
    }

    private function seedStudents(): void
    {
        // 1. Healthy student — valid email + valid SMS phone
        Student::factory()->create([
            'first' => 'Healthy',
            'last' => 'Student',
            'full_name' => 'Healthy Student',
        ]);

        // 2. No contact info at all
        $student = Student::factory()->create([
            'first' => 'NoContact',
            'last' => 'Student',
            'full_name' => 'NoContact Student',
        ]);
        $student->emailAddresses()->delete();
        $student->primary_email_id = null;
        $student->phoneNumbers()->delete();
        $student->primary_phone_id = null;
        $student->save();

        // 3. Bounced email, no phone
        $student = Student::factory()->create([
            'first' => 'BouncedEmail',
            'last' => 'Student',
            'full_name' => 'BouncedEmail Student',
        ]);
        BouncedEmailAddress::factory()->create([
            'address' => $student->primaryEmailAddress->address,
        ]);
        $student->phoneNumbers()->delete();
        $student->primary_phone_id = null;
        $student->save();

        // 4. Opted-out email, no phone
        $student = Student::factory()->create([
            'first' => 'OptedOutEmail',
            'last' => 'Student',
            'full_name' => 'OptedOutEmail Student',
        ]);
        EmailAddressOptInOptOut::factory()->optedOut()->create([
            'address' => $student->primaryEmailAddress->address,
        ]);
        $student->phoneNumbers()->delete();
        $student->primary_phone_id = null;
        $student->save();

        // 5. No email, valid SMS phone only
        $student = Student::factory()->create([
            'first' => 'SmsOnly',
            'last' => 'Student',
            'full_name' => 'SmsOnly Student',
        ]);
        $student->emailAddresses()->delete();
        $student->primary_email_id = null;
        $student->save();

        // 6. Valid email, bounced phone
        $student = Student::factory()->create([
            'first' => 'BouncedPhone',
            'last' => 'Student',
            'full_name' => 'BouncedPhone Student',
        ]);
        BouncedPhoneNumber::factory()->create([
            'number' => $student->primaryPhoneNumber->number,
        ]);

        // 7. Valid email, SMS opted-out phone
        $student = Student::factory()->create([
            'first' => 'SmsOptOut',
            'last' => 'Student',
            'full_name' => 'SmsOptOut Student',
        ]);
        SmsOptOutPhoneNumber::factory()->create([
            'number' => $student->primaryPhoneNumber->number,
        ]);

        // 8. Bounced email, but valid SMS phone (engage should still work via SMS)
        $student = Student::factory()->create([
            'first' => 'BouncedEmailValidSms',
            'last' => 'Student',
            'full_name' => 'BouncedEmailValidSms Student',
        ]);
        BouncedEmailAddress::factory()->create([
            'address' => $student->primaryEmailAddress->address,
        ]);

        // 9. Phone with can_receive_sms = false, no email
        $student = Student::factory()->create([
            'first' => 'NoSmsCapability',
            'last' => 'Student',
            'full_name' => 'NoSmsCapability Student',
        ]);
        $student->emailAddresses()->delete();
        $student->primary_email_id = null;
        $student->primaryPhoneNumber->update(['can_receive_sms' => false]);
        $student->save();

        // 10. Multiple emails: primary bounced, secondary valid
        $student = Student::factory()->create([
            'first' => 'SecondaryEmail',
            'last' => 'Student',
            'full_name' => 'SecondaryEmail Student',
        ]);
        BouncedEmailAddress::factory()->create([
            'address' => $student->primaryEmailAddress->address,
        ]);
        StudentEmailAddress::factory()->create([
            'sisid' => $student->getKey(),
            'order' => 2,
        ]);
        $student->phoneNumbers()->delete();
        $student->primary_phone_id = null;
        $student->save();

        // 11. Multiple phones: primary bounced, secondary valid
        $student = Student::factory()->create([
            'first' => 'SecondaryPhone',
            'last' => 'Student',
            'full_name' => 'SecondaryPhone Student',
        ]);
        BouncedPhoneNumber::factory()->create([
            'number' => $student->primaryPhoneNumber->number,
        ]);
        StudentPhoneNumber::factory()->canReceiveSms()->create([
            'sisid' => $student->getKey(),
            'order' => 2,
        ]);
        $student->emailAddresses()->delete();
        $student->primary_email_id = null;
        $student->save();

        // 12. All contact routes invalid: bounced email + opted-out SMS + no secondary
        $student = Student::factory()->create([
            'first' => 'AllInvalid',
            'last' => 'Student',
            'full_name' => 'AllInvalid Student',
        ]);
        BouncedEmailAddress::factory()->create([
            'address' => $student->primaryEmailAddress->address,
        ]);
        SmsOptOutPhoneNumber::factory()->create([
            'number' => $student->primaryPhoneNumber->number,
        ]);
    }

    private function seedProspects(): void
    {
        // 1. Healthy prospect — valid email + valid SMS phone
        Prospect::factory()->create([
            'first_name' => 'Healthy',
            'last_name' => 'Prospect',
            'full_name' => 'Healthy Prospect',
        ]);

        // 2. No contact info at all
        $prospect = Prospect::factory()->create([
            'first_name' => 'NoContact',
            'last_name' => 'Prospect',
            'full_name' => 'NoContact Prospect',
        ]);
        $prospect->emailAddresses()->delete();
        $prospect->primary_email_id = null;
        $prospect->phoneNumbers()->delete();
        $prospect->primary_phone_id = null;
        $prospect->save();

        // 3. Bounced email, no phone
        $prospect = Prospect::factory()->create([
            'first_name' => 'BouncedEmail',
            'last_name' => 'Prospect',
            'full_name' => 'BouncedEmail Prospect',
        ]);
        BouncedEmailAddress::factory()->create([
            'address' => $prospect->primaryEmailAddress->address,
        ]);
        $prospect->phoneNumbers()->delete();
        $prospect->primary_phone_id = null;
        $prospect->save();

        // 4. Opted-out email, no phone
        $prospect = Prospect::factory()->create([
            'first_name' => 'OptedOutEmail',
            'last_name' => 'Prospect',
            'full_name' => 'OptedOutEmail Prospect',
        ]);
        EmailAddressOptInOptOut::factory()->optedOut()->create([
            'address' => $prospect->primaryEmailAddress->address,
        ]);
        $prospect->phoneNumbers()->delete();
        $prospect->primary_phone_id = null;
        $prospect->save();

        // 5. No email, valid SMS phone only
        $prospect = Prospect::factory()->create([
            'first_name' => 'SmsOnly',
            'last_name' => 'Prospect',
            'full_name' => 'SmsOnly Prospect',
        ]);
        $prospect->emailAddresses()->delete();
        $prospect->primary_email_id = null;
        $prospect->save();

        // 6. Bounced email, but valid SMS phone
        $prospect = Prospect::factory()->create([
            'first_name' => 'BouncedEmailValidSms',
            'last_name' => 'Prospect',
            'full_name' => 'BouncedEmailValidSms Prospect',
        ]);
        BouncedEmailAddress::factory()->create([
            'address' => $prospect->primaryEmailAddress->address,
        ]);

        // 7. All invalid: bounced email + opted-out SMS
        $prospect = Prospect::factory()->create([
            'first_name' => 'AllInvalid',
            'last_name' => 'Prospect',
            'full_name' => 'AllInvalid Prospect',
        ]);
        BouncedEmailAddress::factory()->create([
            'address' => $prospect->primaryEmailAddress->address,
        ]);
        SmsOptOutPhoneNumber::factory()->create([
            'number' => $prospect->primaryPhoneNumber->number,
        ]);
    }
}
