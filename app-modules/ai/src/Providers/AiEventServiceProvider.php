<?php

namespace AdvisingApp\Ai\Providers;

use AdvisingApp\Ai\Events\AiMessageDeleted;
use AdvisingApp\Ai\Events\AiMessageFileDeleted;
use AdvisingApp\Ai\Events\AssistantFilesFinishedUploading;
use AdvisingApp\Ai\Models\AiMessageFile;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use AdvisingApp\Ai\Listeners\DeleteAiMessageRelatedAiMessageFiles;
use AdvisingApp\Ai\Listeners\HandleAssistantFilesFinishedUploading;

class AiEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        AssistantFilesFinishedUploading::class => [
            HandleAssistantFilesFinishedUploading::class,
        ],
        AiMessageDeleted::class => [
            DeleteAiMessageRelatedAiMessageFiles::class,
        ],
        AiMessageFileDeleted::class => [

        ],
    ];
}
