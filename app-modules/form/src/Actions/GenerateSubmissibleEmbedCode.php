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

namespace Assist\Form\Actions;

use Exception;
use Assist\Form\Models\Form;
use Assist\Form\Models\Submissible;
use Illuminate\Support\Facades\URL;
use Assist\Application\Models\Application;

class GenerateSubmissibleEmbedCode
{
    public function handle(Submissible $submissible): string
    {
        return match ($submissible::class) {
            Form::class => (function () use ($submissible) {
                $scriptUrl = url('js/widgets/form/assist-form-widget.js?');
                $formDefinitionUrl = URL::signedRoute('forms.define', ['form' => $submissible]);

                return <<<EOD
                <form-embed url="{$formDefinitionUrl}"></form-embed>
                <script src="{$scriptUrl}"></script>
                EOD;
            })(),
            Application::class => (function () use ($submissible) {
                // TODO: Implement this.
                //$scriptUrl = url('js/widgets/application/assist-application-widget.js?');
                //$formDefinitionUrl = URL::signedRoute('applications.define', ['application' => $submissible]);
                //
                //return <<<EOD
                //<application-embed url="{$formDefinitionUrl}"></application-embed>
                //<script src="{$scriptUrl}"></script>
                //EOD;
            })(),
            default => throw new Exception('Unsupported submissible type.'),
        };
    }
}
