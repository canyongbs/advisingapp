<?php

namespace AdvisingApp\Form\Actions;

use AdvisingApp\Form\Models\Secret;
use AdvisingApp\Form\Models\Submission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class AttachSubmissionSecret
{
    public function __invoke(Submission $submission, Model $fieldSubmission, string $secretId): void
    {
        $secret = Secret::query()
            ->whereKey($secretId)
            ->whereNull('related_id')
            ->lockForUpdate()
            ->first();

        if (! $secret) {
            throw ValidationException::withMessages([
                'passwords' => 'One or more submitted passwords are no longer available.',
            ]);
        }

        if (blank($submission->secret_key)) {
            $submission->secret_key = Crypt::encryptString(
                'base64:' . base64_encode(Encrypter::generateKey(config('app.cipher')))
            );
            $submission->save();
        }

        $secret->value = app(ResolveSubmissionSecretEncrypter::class)($submission)
            ->encryptString(Crypt::decryptString($secret->value));
        $secret->related()->associate($fieldSubmission);
        $secret->save();
    }
}
