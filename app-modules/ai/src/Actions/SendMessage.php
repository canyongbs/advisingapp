<?php

namespace AdvisingApp\Ai\Actions;

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;

class SendMessage
{
    public function __invoke(AiThread $thread, string $content): string
    {
        $message = new AiMessage();
        $message->content = $content;
        $message->thread()->associate($thread);
        $message->user()->associate(auth()->user());

        $response = $thread->assistant->model->getService()->sendMessage($message);
        $response->thread()->associate($thread);
        $response->save();

        return $response->content;
    }
}
