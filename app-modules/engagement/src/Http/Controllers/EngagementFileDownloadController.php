<?php

namespace Assist\Engagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Assist\Engagement\Models\EngagementFile;
use Assist\Engagement\Http\Requests\EngagementFileDownloadRequest;

class EngagementFileDownloadController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EngagementFileDownloadRequest $request, EngagementFile $file)
    {
        return redirect(
            $file->getFirstMedia('file')
                ?->getTemporaryUrl(
                    expiration: now()->addMinute(),
                    options: ['ResponseContentDisposition' => 'attachment']
                )
        );
    }
}
