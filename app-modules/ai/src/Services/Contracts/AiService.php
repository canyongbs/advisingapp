<?php

namespace AdvisingApp\Ai\Services\Contracts;

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;

interface AiService
{
    /**
     * This method is passed an unsaved `AiAssistant` model and should return
     * the model with any additional data that associates it with
     * the AI service, such as the `assistant_id`.
     */
    public function createAssistant(AiAssistant $assistant): void;

    /**
     * This method is passed an unsaved `AiAssistant` model and should update the
     * AI service with the new details.
     */
    public function updateAssistant(AiAssistant $assistant): void;

    /**
     * This method is passed an unsaved `AiThread` model and should return
     * the model with any additional data that associates it with
     * the AI service, such as the `thread_id`.
     */
    public function createThread(AiThread $thread): void;

    /**
     * This method is passed an unsaved `AiMessage` model and should send the
     * message to the AI service.
     *
     * The method should return a new unsaved `AiMessage` model with the content
     * from the AI service set only, the other attributes will be set later.
     */
    public function sendMessage(AiMessage $message): AiMessage;

    public function getMaxAssistantInstructionsLength(): int;
}
