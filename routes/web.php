<?php

use Assist\AssistDataModel\Models\Student;
use Assist\IntegrationAI\Client\AzureOpenAI;
use Assist\IntegrationAI\DataTransferObjects\DynamicContext;

Route::get('/test', function () {
    resolve(AzureOpenAI::class)
        ->ask('Tell me a story', function (?string $partial) {
            ray('partial', $partial);
        });
});

Route::get('/test-with-context', function () {
    resolve(AzureOpenAI::class)
        ->provideDynamicContext(DynamicContext::from([
            'record' => Student::first(),
        ]))
        ->ask('Tell me a story', function (?string $partial) {
            ray('partial', $partial);
        });
});
