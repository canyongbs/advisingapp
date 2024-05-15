<?php

namespace AdvisingApp\Ai\Services\Contracts;

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiAssistant;

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
     * This method is passed an unsaved `AiThread` model and should fill
     * the model with any additional data that associates it with
     * the AI service, such as the `thread_id`.
     */
    public function createThread(AiThread $thread): void;

    /**
     * This method is passed an `AiThread` model and should trigger
     * it for deletion from the AI service.
     */
    public function deleteThread(AiThread $thread): void;

    /**
     * This method is passed an unsaved `AiMessage` model and should send the
     * message to the AI service. If that is successful, it should save the
     * message before fetching the response, in case the response fails
     * to generate.
     *
     * The method should return a new unsaved `AiMessage` model with the content
     * from the AI service set only, the other attributes will be set later.
     */
    public function sendMessage(AiMessage $message): AiMessage;

    public function getMaxAssistantInstructionsLength(): int;
}
