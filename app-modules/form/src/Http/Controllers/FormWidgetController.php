<?php

namespace Assist\Form\Http\Controllers;

use Assist\Form\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class FormWidgetController extends Controller
{
    public function view(Request $request, Form $form): JsonResponse
    {
        return response()->json($form->toArray());
    }
}
