<?php

namespace Assist\Form\Actions;

use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;

class ResolveSubmissionAuthorFromEmail
{
    public function __invoke(?string $email): Student | Prospect | null
    {
        if (blank($email)) {
            return null;
        }

        $student = Student::query()
            ->where('email', $email)
            ->orWhere('email_2', $email)
            ->first();

        if ($student) {
            return $student;
        }

        $prospect = Prospect::query()
            ->where('email', $email)
            ->orWhere('email_2', $email)
            ->first();

        if ($prospect) {
            return $prospect;
        }

        return null;
    }
}
