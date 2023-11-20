<?php

namespace Assist\Form\Actions;

use Assist\Form\Models\Form;
use Illuminate\Support\Facades\URL;

class GenerateFormEmbedCode
{
    public function handle(Form $form): string
    {
        $scriptUrl = url('js/widgets/form/assist-form-widget.js?');
        $formDefinitionUrl = URL::signedRoute('forms.define', ['form' => $form]);

        return <<<EOD
        <form-embed url="{$formDefinitionUrl}"></form-embed>
        <script src="{$scriptUrl}"></script>
        EOD;
    }
}
