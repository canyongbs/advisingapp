<?php

namespace AdvisingApp\Form\Observers;

use AdvisingApp\Form\Models\Form;

class FormObserver
{
    public function created(Form $form): void
    {
        $form->emailAutoReply()->create();
    }
}
