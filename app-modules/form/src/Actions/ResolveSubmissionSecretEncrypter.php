<?php

namespace AdvisingApp\Form\Actions;

use AdvisingApp\Form\Models\Submission;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;

class ResolveSubmissionSecretEncrypter
{
    public function __invoke(Submission $submission): Encrypter
    {
        assert(filled($submission->secret_key));

        $key = Crypt::decryptString($submission->secret_key);

        return new Encrypter(base64_decode(substr($key, strlen('base64:'))), config('app.cipher'));
    }
}
