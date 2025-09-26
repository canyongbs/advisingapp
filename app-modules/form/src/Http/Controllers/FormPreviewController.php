<?php

namespace AdvisingApp\Form\Http\Controllers;

use AdvisingApp\Form\Models\Form;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class FormPreviewController extends Controller
{
    public function __invoke(Form $form): View
    {
        return view('form::preview', [
            'form' => $form,
            'preview' => true,
        ]);
    }
}