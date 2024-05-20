<?php

namespace AdvisingApp\IntegrationOpenAi\Services;

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\IntegrationAI\Settings\AISettings;
use OpenAI;
use OpenAI\Client;

abstract class BaseOpenAiService implements AiService
{
    protected Client $client;

    abstract public function getModel(): string;

    const FORMATTING_INSTRUCTIONS = 'When you answer, it is crucial that you format your response using rich text in markdown format. Do not ever mention in your response that the answer is being formatted/rendered in markdown.';

    public function createAssistant(AiAssistant $assistant): void
    {
        $response = $this->client->assistants()->create([
            'instructions' => $this->generateAssistantInstructions($assistant),
            'name' => $assistant->name,
            'model' => $this->getModel(),
        ]);

        $assistant->assistant_id = $response->id;
    }

    public function updateAssistant(AiAssistant $assistant): void
    {
        $this->client->assistants()->modify($assistant->assistant_id, [
            'instructions' => $this->generateAssistantInstructions($assistant),
            'name' => $assistant->name,
            'model' => $this->getModel(),
        ]);
    }

    public function createThread(AiThread $thread): void
    {
        $response = $this->client->threads()->create([]);

        $thread->thread_id = $response->id;
    }

    public function sendMessage(AiMessage $message): AiMessage
    {
        $response = $this->client->threads()->messages()->create($message->thread->thread_id, [
            'role' => 'user',
            'content' => $message->content,
        ]);

        $message->message_id = $response->id;

        $this->client->threads()->runs()->create($message->thread->thread_id, [
            'assistant_id' => $message->thread->assistant->assistant_id,
        ]);

        $response = $this->client->threads()->messages()->list($message->thread->thread_id, [
            'order' => 'desc',
            'limit' => 1,
        ]);
        $responseContent = $response->data[0]->content[0]->text->value;

        $response = new AiMessage();
        $response->content = $responseContent;

        return $response;
    }

    protected function generateAssistantInstructions(AiAssistant $assistant): string
    {
        $assistantInstructions = rtrim($assistant->instructions, '. ');

        $maxAssistantInstructionsLength = $this->getMaxAssistantInstructionsLength();
        if (strlen($assistantInstructions) > $maxAssistantInstructionsLength) {
            $truncationEnd = '... [truncated]';

            $assistantInstructions = (string) str($assistantInstructions)
                ->limit($maxAssistantInstructionsLength - strlen($truncationEnd), $truncationEnd);
        }

        $systemContext = rtrim(resolve(AISettings::class)->prompt_system_context, '. ');
        $dynamicContext = rtrim(auth()->user()->getDynamicContext(), '. ');
        $formattingInstructions = static::FORMATTING_INSTRUCTIONS;

        return "{$systemContext}. {$assistantInstructions}. {$dynamicContext}. {$formattingInstructions}";
    }

    public function getMaxAssistantInstructionsLength(): int
    {
        $limit = 32768;

        $limit -= strlen(resolve(AISettings::class)->prompt_system_context);
        $limit -= strlen(auth()->user()->getDynamicContext());
        $limit -= strlen(static::FORMATTING_INSTRUCTIONS);

        $limit -= 250; // For good measure.
        $limit -= ($limit % 100); // Round down to the nearest 100.

        return $limit;
    }
}
