<?php

namespace AdvisingApp\Application\Http\Controllers;

use AdvisingApp\Application\Models\Application;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ApplicationPreviewController extends Controller
{
    public function __invoke(Application $application): View
    {
        return view('application::preview', [
            'application' => $application,
            'preview' => true,
        ]);
    }
}
