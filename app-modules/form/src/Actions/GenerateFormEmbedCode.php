<?php

namespace Assist\Form\Actions;

use Illuminate\Support\Arr;
use Assist\Form\Models\Form;

class GenerateFormEmbedCode
{
    public function handle(Form $form): string
    {
        $scriptUrl = url('js/widgets/form/assist-form-widget.js?') . Arr::query(['form' => $form->id]);

        return <<<EOD
        <form-embed></form-embed>
        <script src='{$scriptUrl}'></script>
        EOD;
    }
}
