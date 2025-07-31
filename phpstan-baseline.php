<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/ declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$thread of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/database/migrations/2024_06_26_095418_data_update_value_of_saved_at_field_in_ai_threads_table.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$key of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/database/migrations/2024_06_26_095418_data_update_value_of_saved_at_field_in_ai_threads_table.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/ai/database/migrations/2024_07_31_185847_seed_permissions_add_ai_integrated_assistant_settings\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/database/migrations/2024_07_31_185847_seed_permissions_add_ai_integrated_assistant_settings.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/ai/database/migrations/2024_07_31_185847_seed_permissions_add_ai_integrated_assistant_settings\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/database/migrations/2024_07_31_185847_seed_permissions_add_ai_integrated_assistant_settings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Console\\\\Commands\\\\DeleteUnsavedAiThreads\\:\\:handle\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Console/Commands/DeleteUnsavedAiThreads.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Enums\\\\AiMaxTokens\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Enums/AiMaxTokens.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Enums\\\\AiModel\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Enums/AiModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Events\\\\AssistantFilesFinishedUploading\\:\\:__construct\\(\\) has parameter \\$files with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Events/AssistantFilesFinishedUploading.php',
];
$ignoreErrors[] = [
    'message' => '#^Trait AdvisingApp\\\\Ai\\\\Filament\\\\Pages\\\\Assistant\\\\Concerns\\\\CanManageThreads has PHPDoc tag @property\\-read for property \\$customAssistants with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Pages/Assistant/Concerns/CanManageThreads.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Filament\\\\Pages\\\\ManageAiIntegratedAssistantSettings\\:\\:getFormActions\\(\\) has invalid return type AdvisingApp\\\\Ai\\\\Filament\\\\Pages\\\\Action\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Pages/ManageAiIntegratedAssistantSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Filament\\\\Pages\\\\ManageAiIntegratedAssistantSettings\\:\\:getFormActions\\(\\) has invalid return type AdvisingApp\\\\Ai\\\\Filament\\\\Pages\\\\ActionGroup\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Pages/ManageAiIntegratedAssistantSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Filament\\\\Pages\\\\ManageAiIntegratedAssistantSettings\\:\\:getFormActions\\(\\) should return array\\<AdvisingApp\\\\Ai\\\\Filament\\\\Pages\\\\Action\\|AdvisingApp\\\\Ai\\\\Filament\\\\Pages\\\\ActionGroup\\> but returns array\\<Filament\\\\Actions\\\\Action\\|Filament\\\\Actions\\\\ActionGroup\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Pages/ManageAiIntegratedAssistantSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return int but returns bool\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Resources/AiAssistantResource/Forms/AiAssistantForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
    'identifier' => 'nullCoalesce.expr',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Resources/AiAssistantResource/Forms/AiAssistantForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$archived_at\\.$#',
    'identifier' => 'property.notFound',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Resources/AiAssistantResource/Pages/EditAiAssistant.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$model\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Resources/AiAssistantResource/Pages/EditAiAssistant.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var above a method has no effect\\.$#',
    'identifier' => 'varTag.misplaced',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Resources/AiAssistantResource/Pages/EditAiAssistant.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$assistant of callable AdvisingApp\\\\Ai\\\\Actions\\\\ReInitializeAiServiceAssistant expects AdvisingApp\\\\Ai\\\\Models\\\\AiAssistant, Illuminate\\\\Database\\\\Eloquent\\\\Model given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Resources/AiAssistantResource/Pages/EditAiAssistant.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$assistant of callable AdvisingApp\\\\Ai\\\\Actions\\\\ResetAiServiceIdsForAssistant expects AdvisingApp\\\\Ai\\\\Models\\\\AiAssistant, Illuminate\\\\Database\\\\Eloquent\\\\Model given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Resources/AiAssistantResource/Pages/EditAiAssistant.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Ai\\\\Models\\\\Prompt\\:\\:\\$my_upvotes_count\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/ai/src/Filament/Resources/PromptResource/Pages/ListPrompts.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Filament\\\\Notifications\\\\Notification\\:\\:error\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Jobs/PrepareAiThreadCloning.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AdvisingApp\\\\Ai\\\\Models\\\\AiAssistant\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/ai/src/Listeners/CreateAiMessageLog.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AdvisingApp\\\\Ai\\\\Models\\\\AiThread\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/ai/src/Listeners/CreateAiMessageLog.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:withTrashed\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/ai/src/Models/AiMessage.php',
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:withTrashed\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiMessage.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Models\\\\AiMessage\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiMessage.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Ai\\\\Models\\\\AiMessage\\:\\:\\$dispatchesEvents type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiMessage.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Ai\\\\Models\\\\AiMessageFile\\:\\:\\$dispatchesEvents type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiMessageFile.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:withTrashed\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiThread.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Models\\\\AiThread\\:\\:fromLivewire\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiThread.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Models\\\\AiThread\\:\\:fromLivewire\\(\\) has parameter \\$value with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiThread.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Models\\\\AiThread\\:\\:lastEngagedAt\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiThread.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Models\\\\AiThread\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiThread.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Models\\\\AiThread\\:\\:toLivewire\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiThread.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Ai\\\\Models\\\\AiThread\\:\\:\\$dispatchesEvents type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiThread.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Models\\\\AiThreadFolder\\:\\:defaults\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/AiThreadFolder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Models\\\\Scopes\\\\AuditableAiMessages\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/Scopes/AuditableAiMessages.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Models\\\\Scopes\\\\AuditableAiMessages\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation but does not specify its types\\: TRelatedModel, TDeclaringModel, TResult$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Models/Scopes/AuditableAiMessages.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Ai\\\\Providers\\\\AiServiceProvider\\:\\:\\$listen has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Providers/AiServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Services\\\\Contracts\\\\AiService\\:\\:retryMessage\\(\\) has parameter \\$files with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Services/Contracts/AiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Services\\\\Contracts\\\\AiService\\:\\:sendMessage\\(\\) has parameter \\$files with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Services/Contracts/AiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Services\\\\TestAiService\\:\\:retryMessage\\(\\) has parameter \\$files with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Services/TestAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Services\\\\TestAiService\\:\\:sendMessage\\(\\) has parameter \\$files with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Services/TestAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Ai\\\\Settings\\\\AiIntegrationsSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Settings/AiIntegrationsSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Mockery\\\\ExpectationInterface\\|Mockery\\\\HigherOrderMessage\\:\\:once\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Actions/CompletePromptTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Actions/CompleteResponseTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Mockery\\\\ExpectationInterface\\|Mockery\\\\HigherOrderMessage\\:\\:once\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Actions/CreateThreadTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
    'identifier' => 'argument.templateType',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Actions/RetryMessageTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Actions/SendMessageTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Filament/Resources/AiAssistantResource/CreateAiAssistantTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Filament/Resources/AiAssistantResource/CreateAiAssistantTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Filament/Resources/AiAssistantResource/EditAiAssistantTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Filament/Resources/AiAssistantResource/EditAiAssistantTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$properties of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Filament/Resources/AiAssistantResource/RequestFactories/CreateAiAssistantRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$properties of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Filament/Resources/AiAssistantResource/RequestFactories/EditAiAssistantRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Mockery\\\\ExpectationInterface\\|Mockery\\\\HigherOrderMessage\\:\\:once\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Http/Controllers/CompleteResponseControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Mockery\\\\ExpectationInterface\\|Mockery\\\\HigherOrderMessage\\:\\:once\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Http/Controllers/RetryMessageControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Mockery\\\\ExpectationInterface\\|Mockery\\\\HigherOrderMessage\\:\\:once\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Feature/Http/Controllers/SendMessageControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Filament/Resources/QnaAdvisor/Pages/CreateQnaAdvisorTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Filament/Resources/QnaAdvisor/Pages/CreateQnaAdvisorTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Filament/Resources/QnaAdvisor/Pages/EditQnaAdvisorTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Filament/Resources/QnaAdvisor/Pages/EditQnaAdvisorTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Filament/Resources/QnaAdvisor/Pages/ManageCategoriesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Filament/Resources/QnaAdvisor/Pages/ManageCategoriesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Filament/Resources/QnaAdvisor/Pages/ManageQuestionsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Filament/Resources/QnaAdvisor/Pages/ManageQuestionsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Pest\\\\Mixins\\\\Expectation\\<bool\\|null\\> but returns Pest\\\\Mixins\\\\Expectation\\<bool\\|null\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Unit/AiMessageCascadeDeleteAiMessageFilesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Pest\\\\Mixins\\\\Expectation\\<bool\\|null\\> but returns Pest\\\\Mixins\\\\Expectation\\<bool\\|null\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Unit/AiThreadCascadeDeleteAiMessagesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Mockery\\\\ExpectationInterface\\|Mockery\\\\HigherOrderMessage\\:\\:once\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Unit/Listeners/DeleteExternalAiMessageFileTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Mockery\\\\ExpectationInterface\\|Mockery\\\\HigherOrderMessage\\:\\:with\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Unit/Listeners/DeleteExternalAiThreadTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/alert/database/migrations/2024_11_14_130353_seed_permissions_for_alert_status\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/database/migrations/2024_11_14_130353_seed_permissions_for_alert_status.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/alert/database/migrations/2024_11_14_130353_seed_permissions_for_alert_status\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/database/migrations/2024_11_14_130353_seed_permissions_for_alert_status.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Enums\\\\AlertSeverity\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Enums/AlertSeverity.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Enums\\\\SystemAlertStatusClassification\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Enums/SystemAlertStatusClassification.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Alert\\\\Histories\\\\AlertHistory\\:\\:\\$formatted\\.$#',
    'identifier' => 'property.notFound',
    'count' => 8,
    'path' => __DIR__ . '/app-modules/alert/src/Filament/Actions/AlertHistoryCreatedViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Alert\\\\Histories\\\\AlertHistory\\:\\:\\$formatted\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Filament/Actions/AlertHistoryUpdatedViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Filament\\\\Resources\\\\AlertResource\\\\Pages\\\\ListAlerts\\:\\:segmentFilter\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Filament/Resources/AlertResource/Pages/ListAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Filament\\\\Resources\\\\AlertResource\\\\Pages\\\\ListAlerts\\:\\:segmentFilter\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Filament/Resources/AlertResource/Pages/ListAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Alert\\\\Models\\\\AlertStatus\\:\\:\\$is_default\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Filament/Resources/AlertStatusResource/Pages/CreateAlertStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Filament/Resources/AlertStatusResource/Pages/CreateAlertStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Alert\\\\Models\\\\AlertStatus\\:\\:\\$is_default\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Filament/Resources/AlertStatusResource/Pages/EditAlertStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Filament/Resources/AlertStatusResource/Pages/EditAlertStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Alert\\\\Models\\\\AlertStatus\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AdvisingApp\\\\Alert\\\\Models\\\\AlertStatus\\>\\:\\:\\$name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/alert/src/Histories/AlertHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:alertHistories\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Histories/AlertHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Histories\\\\AlertHistory\\:\\:getFormattedValueForKey\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Histories/AlertHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Histories\\\\AlertHistory\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Histories/AlertHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AdvisingApp\\\\Notification\\\\Models\\\\Subscription\\>\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Listeners/NotifySubscribersOfAlertCreated.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:concern\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:createdBy\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:processCustomHistories\\(\\) has parameter \\$new with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:processCustomHistories\\(\\) has parameter \\$old with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:processCustomHistories\\(\\) has parameter \\$pending with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:processHistory\\(\\) has parameter \\$new with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:processHistory\\(\\) has parameter \\$old with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:recordHistory\\(\\) has parameter \\$new with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:recordHistory\\(\\) has parameter \\$old with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:recordHistory\\(\\) has parameter \\$pending with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Alert\\\\Models\\\\Alert\\:\\:\\$ignoredAttributes type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Notifications\\\\AlertCreatedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Notifications/AlertCreatedNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$concern\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Observers/AlertHistoryObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Alert\\\\Models\\\\AlertStatus\\:\\:\\$order\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Observers/AlertStatusObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Alert\\\\Rules\\\\ConcernIdExistsRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Rules/ConcernIdExistsRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Alert\\\\Rules\\\\ConcernIdExistsRule\\:\\:\\$data has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/alert/src/Rules/ConcernIdExistsRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int,AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\>\\:\\:map\\(\\) expects callable\\(AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField, int\\)\\: array\\{type\\: \'tiptapBlock\', attrs\\: array\\{id\\: string, type\\: string, data\\: non\\-empty\\-array\\}\\}, Closure\\(AdvisingApp\\\\Application\\\\Models\\\\ApplicationField\\)\\: array\\{type\\: \'tiptapBlock\', attrs\\: array\\{id\\: string, type\\: string, data\\: non\\-empty\\-array\\}\\} given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/database/factories/ApplicationFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Application\\\\Models\\\\ApplicationStep\\:\\:\\$application_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Actions/DuplicateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Actions\\\\DuplicateApplication\\:\\:replaceIdsInContent\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Actions/DuplicateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Actions\\\\DuplicateApplication\\:\\:replaceIdsInContent\\(\\) has parameter \\$content with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Actions/DuplicateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Actions\\\\DuplicateApplication\\:\\:replaceIdsInContent\\(\\) has parameter \\$fieldMap with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Actions/DuplicateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Actions\\\\DuplicateApplication\\:\\:replicateFields\\(\\) has parameter \\$stepMap with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Actions/DuplicateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Actions\\\\DuplicateApplication\\:\\:replicateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Actions/DuplicateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Actions\\\\DuplicateApplication\\:\\:replicateSteps\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Actions/DuplicateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Actions\\\\DuplicateApplication\\:\\:updateStepContent\\(\\) has parameter \\$fieldMap with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Actions/DuplicateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Enums\\\\ApplicationSubmissionStateClassification\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Enums/ApplicationSubmissionStateClassification.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$submissible\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Exports/ApplicationSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Class AdvisingApp\\\\Application\\\\Exports\\\\ApplicationSubmissionExport implements generic interface Maatwebsite\\\\Excel\\\\Concerns\\\\WithMapping but does not specify its types\\: RowType$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Exports/ApplicationSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Exports\\\\ApplicationSubmissionExport\\:\\:__construct\\(\\) has parameter \\$submissions with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Exports/ApplicationSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Exports\\\\ApplicationSubmissionExport\\:\\:collection\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection does not specify its types\\: TKey, TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Exports/ApplicationSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Exports\\\\ApplicationSubmissionExport\\:\\:headings\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Exports/ApplicationSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Exports\\\\ApplicationSubmissionExport\\:\\:map\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Exports/ApplicationSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Filament\\\\Resources\\\\ApplicationResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Filament\\\\Resources\\\\ApplicationResource\\\\Actions\\\\ApplicationAdmissionActions\\:\\:get\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Actions/ApplicationAdmissionActions.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Filament\\\\Resources\\\\ApplicationResource\\\\Pages\\\\CreateApplication\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/CreateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Filament\\\\Resources\\\\ApplicationResource\\\\Pages\\\\CreateApplication\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/CreateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Filament\\\\Resources\\\\ApplicationResource\\\\Pages\\\\CreateApplication\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/CreateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$application of method AdvisingApp\\\\Application\\\\Filament\\\\Resources\\\\ApplicationResource\\\\Pages\\\\CreateApplication\\:\\:saveFieldsFromComponents\\(\\) expects AdvisingApp\\\\Application\\\\Models\\\\Application, AdvisingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/CreateApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Filament\\\\Resources\\\\ApplicationResource\\\\Pages\\\\EditApplication\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/EditApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Filament\\\\Resources\\\\ApplicationResource\\\\Pages\\\\EditApplication\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/EditApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Filament\\\\Resources\\\\ApplicationResource\\\\Pages\\\\EditApplication\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/EditApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$application of method AdvisingApp\\\\Application\\\\Filament\\\\Resources\\\\ApplicationResource\\\\Pages\\\\EditApplication\\:\\:saveFieldsFromComponents\\(\\) expects AdvisingApp\\\\Application\\\\Models\\\\Application, AdvisingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/EditApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/ListApplications.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/ManageApplicationSubmissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$submissions\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/ManageApplicationSubmissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$records of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Filament/Resources/ApplicationResource/Pages/ManageApplicationSubmissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Application\\\\Livewire\\\\RenderApplication\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Livewire/RenderApplication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\ApplicationSubmission\\:\\:accessNestedRelations\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/ApplicationSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\ApplicationSubmission\\:\\:accessNestedRelations\\(\\) has parameter \\$relations with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/ApplicationSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\ApplicationSubmission\\:\\:dynamicMethodChain\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/ApplicationSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\ApplicationSubmission\\:\\:dynamicMethodChain\\(\\) has parameter \\$methods with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/ApplicationSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\ApplicationSubmission\\:\\:getStateMachineFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/ApplicationSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\Scopes\\\\ClassifiedAs\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/Scopes/ClassifiedAs.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\State\\\\StateMachine\\:\\:accessNestedRelations\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/State/StateMachine.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\State\\\\StateMachine\\:\\:accessNestedRelations\\(\\) has parameter \\$relations with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/State/StateMachine.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\State\\\\StateMachine\\:\\:checkValidEnum\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/State/StateMachine.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\State\\\\StateMachine\\:\\:dynamicMethodChain\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/State/StateMachine.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\State\\\\StateMachine\\:\\:dynamicMethodChain\\(\\) has parameter \\$methods with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/State/StateMachine.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\State\\\\StateMachine\\:\\:getAllStates\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/State/StateMachine.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\State\\\\StateMachine\\:\\:getStateTransitions\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/State/StateMachine.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\State\\\\StateMachine\\:\\:transitionTo\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/State/StateMachine.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Models\\\\State\\\\StateMachine\\:\\:transitionTo\\(\\) has parameter \\$additionalData with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Models/State/StateMachine.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining values\\: \\(class\\-string\\<AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\>&literal\\-string\\)\\|\\(class\\-string\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\>&literal\\-string\\)\\|null$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Notifications/AuthorLinkedApplicationSubmissionCreatedNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Notifications\\\\AuthorLinkedApplicationSubmissionCreatedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Notifications/AuthorLinkedApplicationSubmissionCreatedNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Application\\\\Providers\\\\ApplicationServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/src/Providers/ApplicationServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int\\<100000, 999999\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/application/tests/Tenant/ApplicationWidgetApiTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Assistant\\\\Filament\\\\Pages\\\\InstitutionalAdvisor\\:\\:customAssistants\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/assistant/src/Filament/Pages/InstitutionalAdvisor.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Assistant\\\\Filament\\\\Pages\\\\InstitutionalAdvisor\\:\\:getCanManageThreadsForms\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/assistant/src/Filament/Pages/InstitutionalAdvisor.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Assistant\\\\Filament\\\\Pages\\\\InstitutionalAdvisor\\:\\:getFolders\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/assistant/src/Filament/Pages/InstitutionalAdvisor.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Assistant\\\\Filament\\\\Pages\\\\InstitutionalAdvisor\\:\\:getThreadsWithoutAFolder\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/assistant/src/Filament/Pages/InstitutionalAdvisor.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Assistant\\\\Filament\\\\Pages\\\\InstitutionalAdvisor\\:\\:selectThread\\(\\) has parameter \\$thread with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/assistant/src/Filament/Pages/InstitutionalAdvisor.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$component of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/assistant/src/Filament/Pages/InstitutionalAdvisor.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$relations of method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<AdvisingApp\\\\Ai\\\\Models\\\\AiThreadFolder\\>\\:\\:with\\(\\) expects array\\<array\\|\\(Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation\\<\\*, \\*, \\*\\>\\)\\: mixed\\)\\|string\\>\\|string, array\\{threads\\: Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\)\\: Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\} given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/assistant/src/Filament/Pages/InstitutionalAdvisor.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/assistant/src/Filament/Pages/InstitutionalAdvisor.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Assistant\\\\Filament\\\\Pages\\\\InstitutionalAdvisor\\:\\:\\$folders type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/assistant/src/Filament/Pages/InstitutionalAdvisor.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Assistant\\\\Filament\\\\Pages\\\\InstitutionalAdvisor\\:\\:\\$threadsWithoutAFolder type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/assistant/src/Filament/Pages/InstitutionalAdvisor.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Actions\\\\Finders\\\\AuditableModels\\:\\:all\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Actions/Finders/AuditableModels.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:auditAttach\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Filament/Actions/AuditAttachAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Filament/Actions/AuditAttachAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Filament/Actions/AuditAttachAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:auditDetach\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Filament/Actions/AuditDetachAction.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$relationship contains generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany but does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Filament/Actions/AuditDetachAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Filament/Exports/AuditExporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Models\\\\Audit\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Models/Audit.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomNew\\.$#',
    'identifier' => 'property.notFound',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomOld\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditEvent\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$isCustomEvent\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Class AdvisingApp\\\\Audit\\\\Overrides\\\\BelongsToMany extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany but does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:attach\\(\\) has parameter \\$attributes with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:isAuditable\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:sync\\(\\) has parameter \\$ids with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:sync\\(\\) has parameter \\$ids with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:sync\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomNew\\.$#',
    'identifier' => 'property.notFound',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomOld\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditEvent\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$isCustomEvent\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:attach\\(\\) has parameter \\$attributes with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:isAuditable\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:sync\\(\\) has parameter \\$ids with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:sync\\(\\) has parameter \\$ids with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:sync\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Audit\\\\Settings\\\\AuditSettings\\:\\:\\$audited_models_exclude type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Settings/AuditSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\|Pest\\\\Support\\\\HigherOrderTapProxy\\:\\:expect\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/tests/Tenant/AuditTraitUsageTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/authorization/database/migrations/2024_12_30_142107_data_seed_role_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/database/migrations/2024_12_30_142107_data_seed_role_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/authorization/database/migrations/2024_12_30_142107_data_seed_role_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/database/migrations/2024_12_30_142107_data_seed_role_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/authorization/database/migrations/2024_12_30_142107_data_seed_role_permissions\\.php\\:40\\:\\:\\$permissionsToDelete type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/database/migrations/2024_12_30_142107_data_seed_role_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Enums\\\\AzureMatchingProperty\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Enums/AzureMatchingProperty.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:role\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Enums/LicenseType.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Enums\\\\LicenseType\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Enums/LicenseType.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Enums/LicenseType.php',
];
$ignoreErrors[] = [
    'message' => '#^Match arm comparison between \'google\' and \'google\' is always true\\.$#',
    'identifier' => 'match.alwaysTrue',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Match arm comparison between AdvisingApp\\\\Authorization\\\\Enums\\\\AzureMatchingProperty\\:\\:Mail and AdvisingApp\\\\Authorization\\\\Enums\\\\AzureMatchingProperty\\:\\:Mail is always true\\.$#',
    'identifier' => 'match.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Instanceof between App\\\\Models\\\\User and Filament\\\\Models\\\\Contracts\\\\FilamentUser will always evaluate to true\\.$#',
    'identifier' => 'instanceof.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/Login.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Filament\\\\Pages\\\\Auth\\\\Login\\:\\:getMultifactorQrCode\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/Login.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Filament\\\\Pages\\\\Auth\\\\Login\\:\\:getSsoFormActions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/Login.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of function collect expects Illuminate\\\\Contracts\\\\Support\\\\Arrayable\\<\\(int\\|string\\), mixed\\>\\|iterable\\<\\(int\\|string\\), mixed\\>\\|null, string\\|null given\\.$#',
    'identifier' => 'argument.type',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/Login.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/Login.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/Login.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Authorization\\\\Filament\\\\Pages\\\\Auth\\\\SetPassword\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/SetPassword.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method isSuperAdmin\\(\\) on an unknown class AdvisingApp\\\\Authorization\\\\Filament\\\\Pages\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/ManageLocalPasswordSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AdvisingApp\\\\Authorization\\\\Filament\\\\Pages\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/ManageLocalPasswordSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Filament\\\\Resources\\\\RoleResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\Builder\\:\\:api\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/ListRoles.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\Builder\\:\\:web\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/ListRoles.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Laravel\\\\Socialite\\\\Contracts\\\\Provider\\|Mockery\\\\MockInterface\\:\\:setConfig\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
    'message' => '#^Call to method isSuperAdmin\\(\\) on an unknown class AdvisingApp\\\\Authorization\\\\Filament\\\\Resources\\\\RoleResource\\\\Pages\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/ListRoles.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AdvisingApp\\\\Authorization\\\\Filament\\\\Resources\\\\RoleResource\\\\Pages\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/ListRoles.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type AdvisingApp\\\\Authorization\\\\Filament\\\\Resources\\\\RoleResource\\\\Pages\\\\User\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/ListRoles.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Laravel\\\\Socialite\\\\Contracts\\\\Provider\\|Mockery\\\\MockInterface\\:\\:setConfig\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Http\\\\Controllers\\\\SocialiteController\\:\\:callback\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Http\\\\Controllers\\\\SocialiteController\\:\\:redirect\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type App\\\\Models\\\\User\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Laravel\\\\Socialite\\\\Contracts\\\\Provider\\|Mockery\\\\MockInterface\\:\\:getLogoutUrl\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Http/Responses/Auth/SocialiteLogoutResponse.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Models\\\\Role\\:\\:scopeApi\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Models\\\\Role\\:\\:scopeSuperAdmin\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Models\\\\Role\\:\\:scopeWeb\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Models\\\\Role\\:\\:users\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TRelatedModel in call to method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:morphedByMany\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Settings\\\\AzureSsoSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Settings/AzureSsoSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Authorization\\\\Settings\\\\GoogleSsoSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Settings/GoogleSsoSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$view of function view expects view\\-string\\|null, string given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/View/Components/Login.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$licenseType of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/User/Actions/AssignLicensesBulkActionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\.\\.\\.\\$data of method Pest\\\\PendingCalls\\\\TestCall\\:\\:with\\(\\) expects array\\<Closure\\|iterable\\<int\\|string, mixed\\>\\|string\\>\\|Closure\\|string, array\\{AdvisingApp\\\\Authorization\\\\Enums\\\\LicenseType\\:\\:ConversationalAi, AdvisingApp\\\\Authorization\\\\Enums\\\\LicenseType\\:\\:RetentionCrm, AdvisingApp\\\\Authorization\\\\Enums\\\\LicenseType\\:\\:RecruitmentCrm\\} given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/User/Actions/AssignLicensesBulkActionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$user of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 11,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/User/ListUsersTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\.\\.\\.\\$data of method Pest\\\\PendingCalls\\\\TestCall\\:\\:with\\(\\) expects array\\<Closure\\|iterable\\<int\\|string, mixed\\>\\|string\\>\\|Closure\\|string, array\\{AdvisingApp\\\\Authorization\\\\Enums\\\\LicenseType\\:\\:ConversationalAi, AdvisingApp\\\\Authorization\\\\Enums\\\\LicenseType\\:\\:RetentionCrm, AdvisingApp\\\\Authorization\\\\Enums\\\\LicenseType\\:\\:RecruitmentCrm\\} given\\.$#',
    'identifier' => 'argument.type',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Unit/LicenceTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/basic\\-needs/database/migrations/2024_06_18_120111_seed_permissions_for_basic_needs_category\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/basic-needs/database/migrations/2024_06_18_120111_seed_permissions_for_basic_needs_category.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/basic\\-needs/database/migrations/2024_06_18_120111_seed_permissions_for_basic_needs_category\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/basic-needs/database/migrations/2024_06_18_120111_seed_permissions_for_basic_needs_category.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/basic\\-needs/database/migrations/2024_06_18_120143_seed_permissions_for_basic_needs_program\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/basic-needs/database/migrations/2024_06_18_120143_seed_permissions_for_basic_needs_program.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/basic\\-needs/database/migrations/2024_06_18_120143_seed_permissions_for_basic_needs_program\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/basic-needs/database/migrations/2024_06_18_120143_seed_permissions_for_basic_needs_program.php',
];
$ignoreErrors[] = [
    'message' => '#^Dead catch \\- App\\\\Exceptions\\\\SoftDeleteContraintViolationException is never thrown in the try block\\.$#',
    'identifier' => 'catch.neverThrown',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/basic-needs/src/Filament/Resources/BasicNeedsCategoryResource/Pages/EditBasicNeedsCategory.php',
];
$ignoreErrors[] = [
    'message' => '#^Comparison operation "\\>" between 0 and 0 is always false\\.$#',
    'identifier' => 'greater.alwaysFalse',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/basic-needs/src/Filament/Resources/BasicNeedsCategoryResource/Pages/ListBasicNeedsCategories.php',
];
$ignoreErrors[] = [
    'message' => '#^Dead catch \\- App\\\\Exceptions\\\\SoftDeleteContraintViolationException is never thrown in the try block\\.$#',
    'identifier' => 'catch.neverThrown',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/basic-needs/src/Filament/Resources/BasicNeedsCategoryResource/Pages/ListBasicNeedsCategories.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\BasicNeeds\\\\Providers\\\\BasicNeedsServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/basic-needs/src/Providers/BasicNeedsServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:catchPhrase\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/database/factories/CampaignFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$campaigns of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/database/migrations/2024_11_15_054949_data_fill_created_by.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\DataTransferObjects\\\\CampaignActionCreationData\\:\\:__construct\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/DataTransferObjects/CampaignActionCreationData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\DataTransferObjects\\\\CampaignActionsCreationData\\:\\:__construct\\(\\) has parameter \\$actions with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/DataTransferObjects/CampaignActionsCreationData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Enums\\\\CampaignActionType\\:\\:blocks\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Enums/CampaignActionType.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Enums\\\\CampaignActionType\\:\\:getEditFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Enums/CampaignActionType.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Enums\\\\CampaignActionType\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Enums/CampaignActionType.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\Actions\\\\DraftCampaignEngagementBlockWithAi\\:\\:getMergeTags\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/Actions/DraftCampaignEngagementBlockWithAi.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\Actions\\\\DraftCampaignEngagementBlockWithAi\\:\\:mergeTags\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/Actions/DraftCampaignEngagementBlockWithAi.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\Actions\\\\DraftCampaignEngagementBlockWithAi\\:\\:\\$mergeTags type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/Actions/DraftCampaignEngagementBlockWithAi.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\CampaignActionBlock\\:\\:createFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/CampaignActionBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\CampaignActionBlock\\:\\:editFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/CampaignActionBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\CampaignActionBlock\\:\\:generateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/CampaignActionBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\CareTeamBlock\\:\\:generateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/CareTeamBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$livewire of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/CareTeamBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$livewire of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/CareTeamBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\CaseBlock\\:\\:generateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/CaseBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\EngagementBatchEmailBlock\\:\\:generateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/EngagementBatchEmailBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\EngagementBatchSmsBlock\\:\\:generateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/EngagementBatchSmsBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\EventBlock\\:\\:generateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/EventBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\InteractionBlock\\:\\:generateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/InteractionBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\ProactiveAlertBlock\\:\\:generateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/ProactiveAlertBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\SubscriptionBlock\\:\\:generateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/SubscriptionBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$livewire of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/TagsBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Blocks\\\\TaskBlock\\:\\:generateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Blocks/TaskBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Filament\\\\Resources\\\\CampaignResource\\\\Pages\\\\CreateCampaign\\:\\:getSteps\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Resources/CampaignResource/Pages/CreateCampaign.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Resources/CampaignResource/Pages/CreateCampaign.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Resources/CampaignResource/Pages/EditCampaign.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Filament/Resources/CampaignResource/RelationManagers/CampaignActionsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Models\\\\Campaign\\:\\:scopeHasNotBeenExecuted\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Models/Campaign.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function is_null\\(\\) with string will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Observers/CampaignObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Result of && is always false\\.$#',
    'identifier' => 'booleanAnd.alwaysFalse',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Observers/CampaignObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Campaign\\\\Providers\\\\CampaignServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/campaign/src/Providers/CampaignServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/src/Filament/Resources/ProspectCareTeamRoleResource/Pages/CreateProspectCareTeamRole.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/src/Filament/Resources/ProspectCareTeamRoleResource/Pages/EditProspectCareTeamRole.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/src/Filament/Resources/StudentCareTeamRoleResource/Pages/CreateStudentCareTeamRole.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/src/Filament/Resources/StudentCareTeamRoleResource/Pages/EditStudentCareTeamRole.php',
];
$ignoreErrors[] = [
    'message' => '#^Instanceof between App\\\\Models\\\\User and App\\\\Models\\\\User will always evaluate to true\\.$#',
    'identifier' => 'instanceof.alwaysTrue',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/care-team/src/Observers/CareTeamObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\>&literal\\-string$#',
    'identifier' => 'match.unhandled',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/care-team/src/Observers/CareTeamObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CareTeam\\\\Rules\\\\EducatableIdExistsRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/src/Rules/EducatableIdExistsRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\CareTeam\\\\Rules\\\\EducatableIdExistsRule\\:\\:\\$data has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/src/Rules/EducatableIdExistsRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CareTeam\\\\Rules\\\\UniqueCareTeamRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/src/Rules/UniqueCareTeamRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\CareTeam\\\\Rules\\\\UniqueCareTeamRule\\:\\:\\$data has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/src/Rules/UniqueCareTeamRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/tests/Tenant/ProspectCareTeamRole/CreateProspectCareTeamRoleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/tests/Tenant/ProspectCareTeamRole/CreateProspectCareTeamRoleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/tests/Tenant/ProspectCareTeamRole/EditProspectCareTeamRoleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/tests/Tenant/ProspectCareTeamRole/EditProspectCareTeamRoleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/tests/Tenant/StudentCareTeamRole/CreateStudentCareTeamRoleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/tests/Tenant/StudentCareTeamRole/CreateStudentCareTeamRoleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/tests/Tenant/StudentCareTeamRole/EditStudentCareTeamRoleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/tests/Tenant/StudentCareTeamRole/EditStudentCareTeamRoleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$educatable of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/care-team/tests/Tenant/Unit/CareTeamAutoSubscriptionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/database/factories/CaseModelFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Database\\\\Factories\\\\CasePriorityFactory\\:\\:high\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/database/factories/CasePriorityFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Database\\\\Factories\\\\CasePriorityFactory\\:\\:low\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/database/factories/CasePriorityFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Database\\\\Factories\\\\CasePriorityFactory\\:\\:medium\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/database/factories/CasePriorityFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Actions\\\\CreateCaseHistory\\:\\:__construct\\(\\) has parameter \\$changes with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Actions/CreateCaseHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Actions\\\\CreateCaseHistory\\:\\:__construct\\(\\) has parameter \\$original with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Actions/CreateCaseHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$type\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Actions/GenerateCaseFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Actions\\\\GenerateCaseFormKitSchema\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Actions/GenerateCaseFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$priority of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Actions/GenerateCaseFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Cases\\\\CaseNumber\\\\SqidPlusSixCaseNumberGenerator\\:\\:generateRandomString\\(\\) has parameter \\$length with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Cases/CaseNumber/SqidPlusSixCaseNumberGenerator.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Enums\\\\CaseAssignmentStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Enums/CaseAssignmentStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Enums\\\\CaseUpdateDirection\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Enums/CaseUpdateDirection.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Enums\\\\SlaComplianceStatus\\:\\:getColor\\(\\) never returns array\\{50\\: string, 100\\: string, 200\\: string, 300\\: string, 400\\: string, 500\\: string, 600\\: string, 700\\: string, \\.\\.\\.\\} so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Enums/SlaComplianceStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Enums\\\\SlaComplianceStatus\\:\\:getColor\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Enums/SlaComplianceStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Enums\\\\SlaComplianceStatus\\:\\:getIcon\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Enums/SlaComplianceStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Enums\\\\SlaComplianceStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Enums/SlaComplianceStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Enums\\\\SystemCaseClassification\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Enums/SystemCaseClassification.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\CaseManagement\\\\Exceptions\\\\CaseNumberExceededReRollsException\\:\\:\\$message has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Exceptions/CaseNumberExceededReRollsException.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\CaseManagement\\\\Exceptions\\\\CaseNumberUpdateAttemptException\\:\\:\\$message has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Exceptions/CaseNumberUpdateAttemptException.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Actions/BulkCreateCaseAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseFormResource\\\\Pages\\\\CreateCaseForm\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseFormResource/Pages/CreateCaseForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseFormResource\\\\Pages\\\\CreateCaseForm\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseFormResource/Pages/CreateCaseForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseFormResource\\\\Pages\\\\CreateCaseForm\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseFormResource/Pages/CreateCaseForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$caseForm of method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseFormResource\\\\Pages\\\\CreateCaseForm\\:\\:saveFieldsFromComponents\\(\\) expects AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseForm, AdvisingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseFormResource/Pages/CreateCaseForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseFormResource\\\\Pages\\\\EditCaseForm\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseFormResource/Pages/EditCaseForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseFormResource\\\\Pages\\\\EditCaseForm\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseFormResource/Pages/EditCaseForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseFormResource\\\\Pages\\\\EditCaseForm\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseFormResource/Pages/EditCaseForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$caseForm of method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseFormResource\\\\Pages\\\\EditCaseForm\\:\\:saveFieldsFromComponents\\(\\) expects AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseForm, AdvisingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseFormResource/Pages/EditCaseForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Filament\\\\Resources\\\\Pages\\\\Page\\:\\:\\$record\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseResource\\\\Pages\\\\CaseTimeline\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/CaseTimeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TGroupKey in call to method Illuminate\\\\Support\\\\Collection\\<int,AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseStatus\\>\\:\\:groupBy\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/CreateCase.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$priority\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/EditCase.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TGroupKey in call to method Illuminate\\\\Support\\\\Collection\\<int,AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseStatus\\>\\:\\:groupBy\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/EditCase.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseResource\\\\Pages\\\\ManageCaseAssignment\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/ManageCaseAssignment.php',
];
$ignoreErrors[] = [
    'message' => '#^Unsafe call to private method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseResource\\\\Pages\\\\ManageCaseAssignment\\:\\:managers\\(\\) through static\\:\\:\\.$#',
    'identifier' => 'staticClassAccess.privateMethod',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/ManageCaseAssignment.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseResource\\\\Pages\\\\ManageCaseFormSubmission\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/ManageCaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Unsafe call to private method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseResource\\\\Pages\\\\ManageCaseFormSubmission\\:\\:managers\\(\\) through static\\:\\:\\.$#',
    'identifier' => 'staticClassAccess.privateMethod',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/ManageCaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseResource\\\\Pages\\\\ManageCaseInteraction\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/ManageCaseInteraction.php',
];
$ignoreErrors[] = [
    'message' => '#^Unsafe call to private method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseResource\\\\Pages\\\\ManageCaseInteraction\\:\\:managers\\(\\) through static\\:\\:\\.$#',
    'identifier' => 'staticClassAccess.privateMethod',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/ManageCaseInteraction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseResource\\\\Pages\\\\ManageCaseUpdate\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/ManageCaseUpdate.php',
];
$ignoreErrors[] = [
    'message' => '#^Unsafe call to private method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseResource\\\\Pages\\\\ManageCaseUpdate\\:\\:managers\\(\\) through static\\:\\:\\.$#',
    'identifier' => 'staticClassAccess.privateMethod',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/ManageCaseUpdate.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/ViewCase.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining values\\: \\(class\\-string\\<AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\>&literal\\-string\\)\\|\\(class\\-string\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\>&literal\\-string\\)$#',
    'identifier' => 'match.unhandled',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/Pages/ViewCase.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/RelationManagers/AssignedToRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$is_authenticated\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseResource/RelationManagers/CaseFormSubmissionRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseStatusResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseStatusResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseTypeResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseTypeResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:priorities\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseTypeResource/Pages/CreateCaseType.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseType\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseTypeResource/Pages/ViewCaseType.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$join of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseUpdateResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseUpdateResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseUpdateResource\\\\Components\\\\CaseAssignmentViewAction\\:\\:caseAssignmentInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseUpdateResource/Components/CaseAssignmentViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseUpdateResource\\\\Components\\\\CaseHistoryViewAction\\:\\:caseHistoryInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseUpdateResource/Components/CaseHistoryViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Filament\\\\Resources\\\\CaseUpdateResource\\\\Components\\\\CaseUpdateViewAction\\:\\:caseUpdateInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseUpdateResource/Components/CaseUpdateViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$case\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseUpdateResource/Pages/EditCaseUpdate.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseUpdateResource/Pages/EditCaseUpdate.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseUpdateResource/Pages/EditCaseUpdate.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$case\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseUpdateResource/Pages/ViewCaseUpdate.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseUpdateResource/Pages/ViewCaseUpdate.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Filament/Resources/CaseUpdateResource/Pages/ViewCaseUpdate.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Http/Controllers/CaseFeedbackFormWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/case-management/src/Http/Controllers/CaseFeedbackFormWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:check\\(\\) expects string, int given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Http/Controllers/CaseFormWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int\\<100000, 999999\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Http/Controllers/CaseFormWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\CaseManagement\\\\Models\\\\IdeHelperCaseFormSubmission\\:\\:\\$submitted_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Http/Controllers/CaseFormWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\CaseManagement\\\\Livewire\\\\RenderCaseFeedbackForm\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Livewire/RenderCaseFeedbackForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\CaseManagement\\\\Livewire\\\\RenderCaseForm\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Livewire/RenderCaseForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:assignments\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseAssignment.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseAssignment\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseAssignment.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @property\\-read for property AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFeedback\\:\\:\\$assignee contains unknown class AdvisingApp\\\\CaseManagement\\\\Models\\\\Educatable\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFeedback.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:notSubmitted\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFormSubmission\\:\\:scopeCanceled\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFormSubmission\\:\\:scopeCanceled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFormSubmission\\:\\:scopeNotCanceled\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFormSubmission\\:\\:scopeNotCanceled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFormSubmission\\:\\:scopeNotSubmitted\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFormSubmission\\:\\:scopeNotSubmitted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFormSubmission\\:\\:scopeRequested\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFormSubmission\\:\\:scopeRequested\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFormSubmission\\:\\:scopeSubmitted\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFormSubmission\\:\\:scopeSubmitted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$submission of method AdvisingApp\\\\Form\\\\Enums\\\\FormSubmissionRequestDeliveryMethod\\:\\:deliver\\(\\) expects AdvisingApp\\\\Form\\\\Models\\\\FormSubmission, \\$this\\(AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseFormSubmission\\) given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:histories\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseHistory\\:\\:formatValues\\(\\) has parameter \\$value with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseHistory\\:\\:formatValues\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseHistory\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseHistory\\:\\:getUpdates\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseHistory\\:\\:newValuesFormatted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseHistory\\:\\:originalValuesFormatted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Generic type Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\> in PHPDoc tag @return does not specify all template types of class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'generics.lessTypes',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Instanceof between AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\|AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student and AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\Subscribable will always evaluate to true\\.$#',
    'identifier' => 'instanceof.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\:\\:getLatestResponseSeconds\\(\\) should return int but returns float\\.$#',
    'identifier' => 'return.type',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\:\\:getResolutionSeconds\\(\\) should return int but returns float\\.$#',
    'identifier' => 'return.type',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\:\\:respondent\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\> but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<Illuminate\\\\Database\\\\Eloquent\\\\Model, \\$this\\(AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\)\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\:\\:scopeOpen\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Type AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable in generic type Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\> in PHPDoc tag @return is not subtype of template type TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model of class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\.$#',
    'identifier' => 'generics.notSubtype',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseType\\:\\:cases\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasManyThrough does not specify its types\\: TRelatedModel, TIntermediateModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseType.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:caseUpdates\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseUpdate.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseUpdate\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Models/CaseUpdate.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\CaseManagement\\\\Models\\\\IdeHelperCaseModel\\:\\:\\$status_updated_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Observers/CaseObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Observers/CaseObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/case-management/src/Observers/CaseObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Policies\\\\CaseAssignmentPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Policies/CaseAssignmentPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Policies\\\\CaseHistoryPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Policies/CaseHistoryPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Policies\\\\CaseModelPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Policies/CaseModelPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Policies\\\\CasePriorityPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Policies/CasePriorityPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Policies\\\\CaseStatusPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Policies/CaseStatusPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Policies\\\\CaseTypePolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Policies/CaseTypePolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Policies\\\\CaseUpdatePolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Policies/CaseUpdatePolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\CaseManagement\\\\Policies\\\\SlaPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/src/Policies/SlaPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Mockery\\\\ExpectationInterface\\|Mockery\\\\HigherOrderMessage\\:\\:twice\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/Case/CaseNumberTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseStatus\\> will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseStatus/CreateCaseStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseStatus/CreateCaseStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseStatus/CreateCaseStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseStatus/EditCaseStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseStatus/EditCaseStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseType\\> will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseType/CreateCaseTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseType/CreateCaseTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseType/CreateCaseTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseType/EditCaseTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseType/EditCaseTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseUpdate\\> will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseUpdate/CreateCaseUpdateTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseUpdate/CreateCaseUpdateTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseUpdate/CreateCaseUpdateTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseUpdate/EditCaseUpdateTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseUpdate/EditCaseUpdateTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\|AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:\\$full\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/CaseUpdate/ListCaseUpdatesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
    'identifier' => 'nullCoalesce.expr',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/RequestFactories/CreateCasePriorityRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/RequestFactories/CreateCaseRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/case-management/tests/Tenant/RequestFactories/EditCaseRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:catchPhrase\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/consent/database/factories/ConsentAgreementFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Match arm comparison between \\$this\\(AdvisingApp\\\\Consent\\\\Enums\\\\ConsentAgreementType\\)&AdvisingApp\\\\Consent\\\\Enums\\\\ConsentAgreementType\\:\\:AzureOpenAI and AdvisingApp\\\\Consent\\\\Enums\\\\ConsentAgreementType\\:\\:AzureOpenAI is always true\\.$#',
    'identifier' => 'match.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/consent/src/Enums/ConsentAgreementType.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Consent\\\\Enums\\\\ConsentAgreementType\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/consent/src/Enums/ConsentAgreementType.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$title on Illuminate\\\\Database\\\\Eloquent\\\\Model\\|int\\|string\\.$#',
    'identifier' => 'property.nonObject',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/consent/src/Filament/Resources/ConsentAgreementResource/Pages/EditConsentAgreement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Consent\\\\Providers\\\\ConsentServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/consent/src/Providers/ConsentServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Division\\\\Database\\\\Factories\\\\DivisionFactory\\:\\:default\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/division/database/factories/DivisionFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/division/src/Filament/Resources/DivisionResource/Pages/CreateDivision.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/division/src/Filament/Resources/DivisionResource/Pages/EditDivision.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Division\\\\Models\\\\Division\\:\\:notificationSetting\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: mixed$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/database/factories/EngagementResponseFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$attributes of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/database/factories/EngagementResponseFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$attributes of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/engagement/database/factories/UnmatchedInboundCommunicationFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Schema\\\\ForeignKeyDefinition\\:\\:unique\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/database/migrations/2023_08_17_183840_create_engagement_deliverables_table.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$engagement of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/engagement/database/migrations/2024_06_07_234752_data_fix_engagement_bodies.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Schema\\\\ForeignKeyDefinition\\:\\:unique\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/database/migrations/2024_12_16_201141_drop_engagement_deliverables_table.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/engagement/database/migrations/2024_12_24_030206_seed_permissions_remove_engagement_deliverable_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/database/migrations/2024_12_24_030206_seed_permissions_remove_engagement_deliverable_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/engagement/database/migrations/2024_12_24_030206_seed_permissions_remove_engagement_deliverable_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/database/migrations/2024_12_24_030206_seed_permissions_remove_engagement_deliverable_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Engagement\\\\Models\\\\IdeHelperEngagement\\:\\:\\$scheduled_at \\(Illuminate\\\\Support\\\\Carbon\\|null\\) does not accept Carbon\\\\CarbonInterface\\|null\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Engagement\\\\Models\\\\IdeHelperEngagementBatch\\:\\:\\$scheduled_at \\(Illuminate\\\\Support\\\\Carbon\\|null\\) does not accept Carbon\\\\CarbonInterface\\|null\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagementBatch.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Actions\\\\GenerateEngagementBodyContent\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateEngagementBodyContent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Actions\\\\GenerateEngagementBodyContent\\:\\:__invoke\\(\\) has parameter \\$mergeData with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateEngagementBodyContent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:__invoke\\(\\) has parameter \\$mergeTags with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:mergeTag\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:text\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$node of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$event of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/engagement/src/Enums/EngagementDisplayStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Actions\\\\BulkDraftWithAiAction\\:\\:getMergeTags\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Actions\\\\BulkDraftWithAiAction\\:\\:mergeTags\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Engagement\\\\Filament\\\\Actions\\\\BulkDraftWithAiAction\\:\\:\\$mergeTags type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\:\\:\\$full_name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/MessageCenterDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Actions\\\\MessageCenterDraftWithAiAction\\:\\:getMergeTags\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/MessageCenterDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Actions\\\\MessageCenterDraftWithAiAction\\:\\:mergeTags\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/MessageCenterDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Engagement\\\\Filament\\\\Actions\\\\MessageCenterDraftWithAiAction\\:\\:\\$mergeTags type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/MessageCenterDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined static method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:displayNameKey\\(\\)\\.$#',
    'identifier' => 'staticMethod.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined static method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getLabel\\(\\)\\.$#',
    'identifier' => 'staticMethod.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Actions\\\\RelationManagerDraftWithAiAction\\:\\:getMergeTags\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Actions\\\\RelationManagerDraftWithAiAction\\:\\:mergeTags\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Engagement\\\\Filament\\\\Actions\\\\RelationManagerDraftWithAiAction\\:\\:\\$mergeTags type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerDraftWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\|Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$address\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\|Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$number\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$emailAddresses\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryEmailAddress\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Filament\\\\Forms\\\\Components\\\\Field\\:\\:getTemporaryImages\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:emailAddresses\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:phoneNumbers\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:primaryPhoneNumber\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining values\\: AdvisingApp\\\\Notification\\\\Enums\\\\NotificationChannel\\:\\:Database\\|null$#',
    'identifier' => 'match.unhandled',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Forms\\\\Components\\\\EngagementSmsBodyInput\\:\\:make\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Forms/Components/EngagementSmsBodyInput.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:timeline\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:getEducatableIds\\(\\) has parameter \\$engagementResponseScope with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:getEducatableIds\\(\\) has parameter \\$engagementScope with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:getEducatableIds\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:getLatestActivityForEducatables\\(\\) has parameter \\$ids with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:getProspectIds\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:getStudentIds\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:paginationView\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:selectChanged\\(\\) has parameter \\$value with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:updated\\(\\) has parameter \\$property with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:viewRecord\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:viewRecord\\(\\) has parameter \\$key with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:viewRecord\\(\\) has parameter \\$morphReference with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$entity of method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<AdvisingApp\\\\Timeline\\\\Models\\\\Timeline\\>\\:\\:forEntity\\(\\) expects Illuminate\\\\Database\\\\Eloquent\\\\Model, AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\|null given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$join of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$recordModel of method AdvisingApp\\\\Timeline\\\\Actions\\\\SyncTimelineData\\:\\:now\\(\\) expects Illuminate\\\\Database\\\\Eloquent\\\\Model, AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\|null given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$search of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:\\$recordModel \\(AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\|null\\) does not accept Illuminate\\\\Database\\\\Eloquent\\\\Model\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Engagement\\\\Filament\\\\Pages\\\\MessageCenter\\:\\:\\$timelineRecords with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Pages/MessageCenter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Resources\\\\Actions\\\\DraftTemplateWithAiAction\\:\\:getMergeTags\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/Actions/DraftTemplateWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Filament\\\\Resources\\\\Actions\\\\DraftTemplateWithAiAction\\:\\:mergeTags\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/Actions/DraftTemplateWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Engagement\\\\Filament\\\\Resources\\\\Actions\\\\DraftTemplateWithAiAction\\:\\:\\$mergeTags type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/Actions/DraftTemplateWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$id on Illuminate\\\\Database\\\\Eloquent\\\\Model\\|int\\|string\\.$#',
    'identifier' => 'property.nonObject',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementFileResource/Pages/ViewEngagementFile.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:\\$full\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementResponseResource/Pages/ViewEngagementResponse.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:\\$full\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementResponseResource/Pages/ViewEngagementResponse.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining values\\: \\(class\\-string\\<AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\>&literal\\-string\\)\\|\\(class\\-string\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\>&literal\\-string\\)$#',
    'identifier' => 'match.unhandled',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementResponseResource/Pages/ViewEngagementResponse.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Http\\\\Controllers\\\\EngagementFileDownloadController\\:\\:__invoke\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Http/Controllers/EngagementFileDownloadController.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Http\\\\Requests\\\\EngagementFileDownloadRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Http/Requests/EngagementFileDownloadRequest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$model of method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<Illuminate\\\\Database\\\\Eloquent\\\\Model,AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\>\\:\\:associate\\(\\) expects Illuminate\\\\Database\\\\Eloquent\\\\Model\\|null, AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Jobs/CreateBatchedEngagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Jobs\\\\DeliverEngagements\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Jobs/DeliverEngagements.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Engagement\\\\Jobs\\\\GatherAndDispatchSesS3InboundEmails\\:\\:\\$uniqueFor has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Jobs/GatherAndDispatchSesS3InboundEmails.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Engagement\\\\Jobs\\\\ProcessSesS3InboundEmail\\:\\:\\$uniqueFor has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Jobs/ProcessSesS3InboundEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$communications of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Jobs/UnmatchedInboundCommunicationsJob.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:orderedEngagements\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:batch\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:createdBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:getMergeData\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:getMergeTags\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:latestEmailMessage\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:latestSmsMessage\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeIsNotPartOfABatch\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeSentToProspect\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeSentToStudent\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method DOMNode\\:\\:getAttribute\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementBatch.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\EngagementFile\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFile.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:orderedEngagementResponses\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Notifications\\\\EngagementBatchFinishedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementBatchFinishedNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Notifications\\\\EngagementBatchStartedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementBatchStartedNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\:\\:\\$display_name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementFailedNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Engagement\\\\Notifications\\\\EngagementFailedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementFailedNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\:\\:\\$display_name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: AdvisingApp\\\\Notification\\\\Enums\\\\NotificationChannel\\:\\:Database$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function is_null\\(\\) with string will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Observers/EngagementBatchObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Result of && is always false\\.$#',
    'identifier' => 'booleanAnd.alwaysFalse',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Observers/EngagementBatchObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\$recipient of class AdvisingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData constructor expects AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified&Illuminate\\\\Database\\\\Eloquent\\\\Model\\>, Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/tests/Tenant/Feature/Actions/CreateEngagementBatchTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int,AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\>\\:\\:map\\(\\) expects callable\\(AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField, int\\)\\: array\\{type\\: \'tiptapBlock\', attrs\\: array\\{id\\: string, type\\: string, data\\: non\\-empty\\-array\\}\\}, Closure\\(AdvisingApp\\\\Form\\\\Models\\\\FormField\\)\\: array\\{type\\: \'tiptapBlock\', attrs\\: array\\{id\\: string, type\\: string, data\\: non\\-empty\\-array\\}\\} given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/database/factories/FormFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\DuplicateForm\\:\\:replaceIdsInContent\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DuplicateForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\DuplicateForm\\:\\:replaceIdsInContent\\(\\) has parameter \\$content with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DuplicateForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\DuplicateForm\\:\\:replaceIdsInContent\\(\\) has parameter \\$fieldMap with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DuplicateForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\DuplicateForm\\:\\:replicateFields\\(\\) has parameter \\$stepMap with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DuplicateForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\DuplicateForm\\:\\:replicateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DuplicateForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\DuplicateForm\\:\\:replicateSteps\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DuplicateForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\DuplicateForm\\:\\:updateStepContent\\(\\) has parameter \\$fieldMap with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DuplicateForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) has parameter \\$fields with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:generateContent\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) has parameter \\$component with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) has parameter \\$fields with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:text\\(\\) has parameter \\$component with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:text\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:wizardContent\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:wizardContent\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$recaptcha_enabled\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:fields\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:fields\\(\\) has parameter \\$fields with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:wizardRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:mapWithKeys\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: array\\<array\\>, Closure\\(AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\)\\: non\\-empty\\-array\\<array\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$label\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidator.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TMapWithKeysValue in call to method Illuminate\\\\Support\\\\Collection\\<int,\\(int\\|string\\)\\>\\:\\:mapWithKeys\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidator.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:\\$pivot\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\InjectSubmissionStateIntoTipTapContent\\:\\:__invoke\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\InjectSubmissionStateIntoTipTapContent\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\InjectSubmissionStateIntoTipTapContent\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\ProcessSubmissionField\\:\\:__invoke\\(\\) has parameter \\$fields with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/ProcessSubmissionField.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AdvisingApp\\\\Form\\\\Models\\\\Submissible\\>&literal\\-string$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveBlockRegistry.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Actions\\\\ResolveBlockRegistry\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveBlockRegistry.php',
];
$ignoreErrors[] = [
    'message' => '#^If condition is always true\\.$#',
    'identifier' => 'if.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveSubmissionAuthorFromEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
    'identifier' => 'deadCode.unreachable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveSubmissionAuthorFromEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Enums\\\\FormSubmissionRequestDeliveryMethod\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Enums/FormSubmissionRequestDeliveryMethod.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Enums\\\\FormSubmissionStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Enums/FormSubmissionStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Enums\\\\Rounding\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Enums/Rounding.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$submissible\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Class AdvisingApp\\\\Form\\\\Exports\\\\FormSubmissionExport implements generic interface Maatwebsite\\\\Excel\\\\Concerns\\\\WithMapping but does not specify its types\\: RowType$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:__construct\\(\\) has parameter \\$submissions with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:collection\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection does not specify its types\\: TKey, TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:headings\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:map\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:formSubmissions\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Actions/RequestFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\AgreementFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/AgreementFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\AgreementFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/AgreementFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\AgreementFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/AgreementFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\CheckboxesFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxesFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\CheckboxesFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxesFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\CheckboxesFormFieldBlock\\:\\:getSubmissionState\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxesFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\CheckboxesFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxesFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxesFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxesFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\DateFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/DateFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\DateFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/DateFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\DateFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/DateFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getFormSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getSubmissionState\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\EmailFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EmailFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\EmailFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EmailFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\EmailFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EmailFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getFormSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getSubmissionState\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable \\$block in PHPDoc tag @var does not exist\\.$#',
    'identifier' => 'varTag.variableNotFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlockRegistry.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\NumberFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/NumberFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\NumberFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/NumberFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\NumberFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/NumberFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\PhoneFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/PhoneFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\PhoneFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/PhoneFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\PhoneFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/PhoneFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\RadioFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\RadioFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\RadioFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\SelectFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\SelectFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\SelectFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\SignatureFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SignatureFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\SignatureFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SignatureFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextAreaFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextAreaFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextAreaFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextAreaFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextAreaFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextAreaFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextInputFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextInputFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextInputFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextInputFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextInputFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextInputFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\TimeFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TimeFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\TimeFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TimeFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\UrlFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UrlFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\UrlFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UrlFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Blocks\\\\UrlFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UrlFormFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Resources\\\\FormResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Resources\\\\FormResource\\\\Pages\\\\CreateForm\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/CreateForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Resources\\\\FormResource\\\\Pages\\\\CreateForm\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/CreateForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Resources\\\\FormResource\\\\Pages\\\\CreateForm\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/CreateForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$form of method AdvisingApp\\\\Form\\\\Filament\\\\Resources\\\\FormResource\\\\Pages\\\\CreateForm\\:\\:saveFieldsFromComponents\\(\\) expects AdvisingApp\\\\Form\\\\Models\\\\Form, AdvisingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/CreateForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Resources\\\\FormResource\\\\Pages\\\\EditForm\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/EditForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Resources\\\\FormResource\\\\Pages\\\\EditForm\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/EditForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Filament\\\\Resources\\\\FormResource\\\\Pages\\\\EditForm\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/EditForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$form of method AdvisingApp\\\\Form\\\\Filament\\\\Resources\\\\FormResource\\\\Pages\\\\EditForm\\:\\:saveFieldsFromComponents\\(\\) expects AdvisingApp\\\\Form\\\\Models\\\\Form, AdvisingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/EditForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/ListForms.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$is_authenticated\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/ManageFormSubmissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/ManageFormSubmissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$submissions\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/ManageFormSubmissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$records of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Resources/FormResource/Pages/ManageFormSubmissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:canceled\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Tables/Filters/FormSubmissionStatusFilter.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:requested\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Tables/Filters/FormSubmissionStatusFilter.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:submitted\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Tables/Filters/FormSubmissionStatusFilter.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:check\\(\\) expects string, int given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Http/Controllers/FormWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int\\<100000, 999999\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/form/src/Http/Controllers/FormWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Form\\\\Models\\\\IdeHelperFormSubmission\\:\\:\\$submitted_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Http/Controllers/FormWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\FormEmailAutoReply\\:\\:getMergeData\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormEmailAutoReply.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:notSubmitted\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\FormSubmission\\:\\:scopeCanceled\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\FormSubmission\\:\\:scopeCanceled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\FormSubmission\\:\\:scopeNotCanceled\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\FormSubmission\\:\\:scopeNotCanceled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\FormSubmission\\:\\:scopeNotSubmitted\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\FormSubmission\\:\\:scopeNotSubmitted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\FormSubmission\\:\\:scopeRequested\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\FormSubmission\\:\\:scopeRequested\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\FormSubmission\\:\\:scopeSubmitted\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\FormSubmission\\:\\:scopeSubmitted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/FormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Class AdvisingApp\\\\Form\\\\Models\\\\Submissible has PHPDoc tag @property for property \\$allowed_domains with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
    'message' => '#^Class AdvisingApp\\\\Form\\\\Models\\\\Submissible has PHPDoc tag @property for property \\$content with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
    'message' => '#^Class AdvisingApp\\\\Form\\\\Models\\\\Submissible uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:allowedDomains\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:content\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:embedEnabled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:isWizard\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:name\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:steps\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:submissions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleAuthentication\\:\\:author\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleAuthentication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleAuthentication\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleAuthentication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleAuthentication\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleAuthentication.php',
];
$ignoreErrors[] = [
    'message' => '#^Class AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField has PHPDoc tag @property for property \\$config with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:config\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:isRequired\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:label\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:step\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:\\$sort\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
    'message' => '#^Class AdvisingApp\\\\Form\\\\Models\\\\SubmissibleStep has PHPDoc tag @property for property \\$content with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:content\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:label\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submission\\:\\:author\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submission\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submission\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submission\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Models\\\\Submission\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Notifications\\\\AuthenticateFormNotification\\:\\:identifyRecipient\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Notifications/AuthenticateFormNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining values\\: \\(class\\-string\\<AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\>&literal\\-string\\)\\|\\(class\\-string\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\>&literal\\-string\\)\\|null$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Notifications/AuthorLinkedFormSubmissionCreatedNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Notifications\\\\AuthorLinkedFormSubmissionCreatedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Notifications/AuthorLinkedFormSubmissionCreatedNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Form\\\\Providers\\\\FormServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Providers/FormServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/in\\-app\\-communication/database/migrations/2025_03_20_125908_data_remove_realtime_chat_api_permission\\.php\\:41\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/database/migrations/2025_03_20_125908_data_remove_realtime_chat_api_permission.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/in\\-app\\-communication/database/migrations/2025_03_20_125908_data_remove_realtime_chat_api_permission\\.php\\:41\\:\\:\\$permissionsToDelete type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/database/migrations/2025_03_20_125908_data_remove_realtime_chat_api_permission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Actions\\\\CheckConversationMessageContentForMention\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Actions/CheckConversationMessageContentForMention.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Actions\\\\ConvertMessageJsonToText\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Actions/ConvertMessageJsonToText.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable \\$user in PHPDoc tag @var does not match assigned variable \\$creator\\.$#',
    'identifier' => 'varTag.differentVariable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Actions/CreateTwilioConversation.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Enums\\\\ConversationNotificationPreference\\:\\:getColor\\(\\) never returns array\\{50\\: string, 100\\: string, 200\\: string, 300\\: string, 400\\: string, 500\\: string, 600\\: string, 700\\: string, \\.\\.\\.\\} so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Enums/ConversationNotificationPreference.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Enums\\\\ConversationNotificationPreference\\:\\:getColor\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Enums/ConversationNotificationPreference.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Enums\\\\ConversationNotificationPreference\\:\\:getDescription\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Enums/ConversationNotificationPreference.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Enums\\\\ConversationNotificationPreference\\:\\:getIcon\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Enums/ConversationNotificationPreference.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Enums\\\\ConversationNotificationPreference\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Enums/ConversationNotificationPreference.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Events\\\\ConversationMessageSent\\:\\:__construct\\(\\) has parameter \\$messageContent with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Events/ConversationMessageSent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, \\*NEVER\\*\\>\\:\\:\\$participant\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$notification_preference on null\\.$#',
    'identifier' => 'property.nonObject',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$participant on null\\.$#',
    'identifier' => 'property.nonObject',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^If condition is always true\\.$#',
    'identifier' => 'if.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:conversations\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection does not specify its types\\: TKey, TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:joinChannelsAction\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:onMessageSent\\(\\) has parameter \\$messageContent with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^Negated boolean expression is always false\\.$#',
    'identifier' => 'booleanNot.alwaysFalse',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @property for property AdvisingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:\\$conversations contains generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$relations of method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<AdvisingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversation\\>\\:\\:with\\(\\) expects array\\<array\\|\\(Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation\\<\\*, \\*, \\*\\>\\)\\: mixed\\)\\|string\\>\\|string, array\\{participants\\: Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\)\\: Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\} given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:\\$conversationActiveUsers type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$components of method Filament\\\\Panel\\:\\:livewireComponents\\(\\) expects array\\<string, class\\-string\\<Livewire\\\\Component\\>\\>, array\\{\'AdvisingApp\\\\\\\\InAppCommunication\\\\\\\\Livewire\\\\\\\\ChatNotifications\'\\} given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/InAppCommunicationPlugin.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, \\*NEVER\\*\\>\\:\\:\\$participant\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Jobs/NotifyConversationParticipant.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, \\*NEVER\\*\\>\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Jobs/NotifyConversationParticipant.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\LazyCollection\\<int,\\*NEVER\\*\\>\\:\\:each\\(\\) contains unresolvable type\\.$#',
    'identifier' => 'argument.unresolvableType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Jobs/NotifyConversationParticipants.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type of call to method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<App\\\\Models\\\\User,AdvisingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversation,covariant AdvisingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversationUser,string\\>\\:\\:lazyById\\(\\) contains unresolvable type\\.$#',
    'identifier' => 'method.unresolvableReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Jobs/NotifyConversationParticipants.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Livewire\\\\ChatNotifications\\:\\:getNotifications\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection does not specify its types\\: TKey, TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Livewire/ChatNotifications.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\InAppCommunication\\\\Models\\\\IdeHelperTwilioConversationUser\\:\\:\\$last_read_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversationUser.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\InAppCommunication\\\\Providers\\\\InAppCommunicationServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Providers/InAppCommunicationServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesBounceData\\:\\:__construct\\(\\) has parameter \\$bouncedRecipients with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesBounceData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesClickData\\:\\:__construct\\(\\) has parameter \\$linkTags with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesClickData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesComplaintData\\:\\:__construct\\(\\) has parameter \\$complainedRecipients with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesComplaintData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesDeliveryData\\:\\:__construct\\(\\) has parameter \\$recipients with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesDeliveryData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesDeliveryDelayData\\:\\:__construct\\(\\) has parameter \\$delayedRecipients with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesDeliveryDelayData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesEventData\\:\\:fromRequest\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesEventData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$commonHeaders with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$destination with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$headers with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesTopicPreferencesData\\:\\:__construct\\(\\) has parameter \\$topicDefaultSubscriptionStatus with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesTopicPreferencesData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesTopicPreferencesData\\:\\:__construct\\(\\) has parameter \\$topicSubscriptionStatus with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesTopicPreferencesData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\Http\\\\Controllers\\\\AwsSesInboundWebhookController\\:\\:__invoke\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Http/Controllers/AwsSesInboundWebhookController.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\Providers\\\\IntegrationAwsSesEventHandlingServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Providers/IntegrationAwsSesEventHandlingServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationAwsSesEventHandling\\\\Settings\\\\SesSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Settings/SesSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to property \\$mail on an unknown class TenantConfig\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/tests/Tenant/Feature/Filament/Pages/ManageAmazonSesSettingsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$config contains unknown class TenantConfig\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/tests/Tenant/Feature/Filament/Pages/ManageAmazonSesSettingsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:expectExceptionMessage\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/tests/Tenant/Unit/SesConfigurationSetTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationGoogleAnalytics\\\\Providers\\\\IntegrationGoogleAnalyticsServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-google-analytics/src/Providers/IntegrationGoogleAnalyticsServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationGoogleRecaptcha\\\\Providers\\\\IntegrationGoogleRecaptchaServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-google-recaptcha/src/Providers/IntegrationGoogleRecaptchaServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationGoogleRecaptcha\\\\Settings\\\\GoogleRecaptchaSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-google-recaptcha/src/Settings/GoogleRecaptchaSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationMicrosoftClarity\\\\Providers\\\\IntegrationMicrosoftClarityServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-microsoft-clarity/src/Providers/IntegrationMicrosoftClarityServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\DataTransferObjects\\\\Assistants\\\\AssistantsDataTransferObject\\:\\:__construct\\(\\) has parameter \\$tools with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/DataTransferObjects/Assistants/AssistantsDataTransferObject.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\DataTransferObjects\\\\Assistants\\\\FileSearchDataTransferObject\\:\\:__construct\\(\\) has parameter \\$vectorStoreIds with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/DataTransferObjects/Assistants/FileSearchDataTransferObject.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\DataTransferObjects\\\\Threads\\\\ThreadsDataTransferObject\\:\\:__construct\\(\\) has parameter \\$vectorStoreIds with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/DataTransferObjects/Threads/ThreadsDataTransferObject.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\DataTransferObjects\\\\VectorStoreFiles\\\\VectorStoreFilesDataTransferObject\\:\\:__construct\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/DataTransferObjects/VectorStoreFiles/VectorStoreFilesDataTransferObject.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\DataTransferObjects\\\\VectorStores\\\\VectorStoresDataTransferObject\\:\\:__construct\\(\\) has parameter \\$fileCounts with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/DataTransferObjects/VectorStores/VectorStoresDataTransferObject.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\Exceptions\\\\FileUploadException\\:\\:__construct\\(\\) has parameter \\$message with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Exceptions/FileUploadException.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\Exceptions\\\\FileUploadsCannotBeDisabled\\:\\:__construct\\(\\) has parameter \\$message with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Exceptions/FileUploadsCannotBeDisabled.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\Exceptions\\\\FileUploadsCannotBeEnabled\\:\\:__construct\\(\\) has parameter \\$message with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Exceptions/FileUploadsCannotBeEnabled.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\Jobs\\\\UploadFilesToAssistant\\:\\:__construct\\(\\) has parameter \\$files with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Jobs/UploadFilesToAssistant.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\IntegrationOpenAi\\\\Jobs\\\\UploadFilesToAssistant\\:\\:\\$deleteWhenMissingModels has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Jobs/UploadFilesToAssistant.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\IntegrationOpenAi\\\\Jobs\\\\UploadFilesToAssistant\\:\\:\\$maxExceptions has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Jobs/UploadFilesToAssistant.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\Providers\\\\IntegrationOpenAiServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Providers/IntegrationOpenAiServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\Services\\\\BaseOpenAiService\\:\\:modifyThread\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/BaseOpenAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\Services\\\\BaseOpenAiService\\:\\:retryMessage\\(\\) has parameter \\$files with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/BaseOpenAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\Services\\\\BaseOpenAiService\\:\\:sendMessage\\(\\) has parameter \\$files with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/BaseOpenAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationOpenAi\\\\Services\\\\BaseOpenAiService\\:\\:updateAssistantTools\\(\\) has parameter \\$tools with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/BaseOpenAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$file of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/BaseOpenAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$tool of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/BaseOpenAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$vectorStoreId of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/BaseOpenAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>message" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/BaseOpenAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>vectorStoreIds" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/BaseOpenAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type OpenAI\\\\Responses\\\\Threads\\\\Runs\\\\ThreadRunResponse\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/BaseOpenAiService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$file of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGpt41MiniService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$vectorStoreId of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGpt41MiniService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$file of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGpt41NanoService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$vectorStoreId of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGpt41NanoService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$file of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGpt4oMiniService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$vectorStoreId of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGpt4oMiniService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$file of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGpt4oService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$vectorStoreId of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGpt4oService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$file of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGptO1MiniService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$vectorStoreId of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGptO1MiniService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$file of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGptO3MiniService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$vectorStoreId of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGptO3MiniService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$file of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGptO4MiniService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$vectorStoreId of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/src/Services/OpenAiGptO4MiniService.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$responseClass of class OpenAI\\\\Responses\\\\StreamResponse constructor expects class\\-string\\<\\>, string given\\.$#',
    'identifier' => 'argument.type',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/integration-open-ai/tests/Tenant/Feature/Services/OpenAiGptTestServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$responses of method OpenAI\\\\Testing\\\\ClientFake\\:\\:addResponses\\(\\) expects array\\<OpenAI\\\\Testing\\\\Response\\>, array\\<int, OpenAI\\\\Responses\\\\Assistants\\\\AssistantResponse\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/integration-open-ai/tests/Tenant/Feature/Services/OpenAiGptTestServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$responses of method OpenAI\\\\Testing\\\\ClientFake\\:\\:addResponses\\(\\) expects array\\<OpenAI\\\\Testing\\\\Response\\>, array\\<int, OpenAI\\\\Responses\\\\StreamResponse\\<\\>\\|OpenAI\\\\Responses\\\\Threads\\\\Messages\\\\ThreadMessageResponse\\|OpenAI\\\\Responses\\\\Threads\\\\Runs\\\\ThreadRunListResponse\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/integration-open-ai/tests/Tenant/Feature/Services/OpenAiGptTestServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$responses of method OpenAI\\\\Testing\\\\ClientFake\\:\\:addResponses\\(\\) expects array\\<OpenAI\\\\Testing\\\\Response\\>, array\\<int, OpenAI\\\\Responses\\\\Threads\\\\Messages\\\\ThreadMessageListResponse\\|OpenAI\\\\Responses\\\\Threads\\\\Runs\\\\ThreadRunListResponse\\|OpenAI\\\\Responses\\\\Threads\\\\Runs\\\\ThreadRunResponse\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/integration-open-ai/tests/Tenant/Feature/Services/OpenAiGptTestServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$responses of method OpenAI\\\\Testing\\\\ClientFake\\:\\:addResponses\\(\\) expects array\\<OpenAI\\\\Testing\\\\Response\\>, array\\<int, OpenAI\\\\Responses\\\\Threads\\\\Messages\\\\ThreadMessageResponse\\|OpenAI\\\\Responses\\\\Threads\\\\ThreadResponse\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/tests/Tenant/Feature/Services/OpenAiGptTestServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$responses of method OpenAI\\\\Testing\\\\ClientFake\\:\\:addResponses\\(\\) expects array\\<OpenAI\\\\Testing\\\\Response\\>, array\\<int, OpenAI\\\\Responses\\\\Threads\\\\ThreadDeleteResponse\\|OpenAI\\\\Responses\\\\Threads\\\\ThreadResponse\\|OpenAI\\\\Responses\\\\VectorStores\\\\VectorStoreDeleteResponse\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/tests/Tenant/Feature/Services/OpenAiGptTestServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$responses of method OpenAI\\\\Testing\\\\ClientFake\\:\\:addResponses\\(\\) expects array\\<OpenAI\\\\Testing\\\\Response\\>, array\\<int, OpenAI\\\\Responses\\\\Threads\\\\ThreadResponse\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/integration-open-ai/tests/Tenant/Feature/Services/OpenAiGptTestServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/integration-open-ai/tests/Tenant/Feature/Services/OpenAiGptTestServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: string$#',
    'identifier' => 'match.unhandled',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/integration-twilio/src/Actions/TwilioWebhookProcessor.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of job class AdvisingApp\\\\IntegrationTwilio\\\\Jobs\\\\MessageReceived constructor expects AdvisingApp\\\\IntegrationTwilio\\\\DataTransferObjects\\\\TwilioMessageReceivedData in AdvisingApp\\\\IntegrationTwilio\\\\Jobs\\\\MessageReceived\\:\\:dispatch\\(\\), AdvisingApp\\\\IntegrationTwilio\\\\DataTransferObjects\\\\TwilioWebhookData given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/src/Actions/TwilioWebhookProcessor.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of job class AdvisingApp\\\\IntegrationTwilio\\\\Jobs\\\\StatusCallback constructor expects AdvisingApp\\\\IntegrationTwilio\\\\DataTransferObjects\\\\TwilioStatusCallbackData in AdvisingApp\\\\IntegrationTwilio\\\\Jobs\\\\StatusCallback\\:\\:dispatch\\(\\), AdvisingApp\\\\IntegrationTwilio\\\\DataTransferObjects\\\\TwilioWebhookData given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/src/Actions/TwilioWebhookProcessor.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationTwilio\\\\DataTransferObjects\\\\TwilioMessageReceivedData\\:\\:fromRequest\\(\\) should return static\\(AdvisingApp\\\\IntegrationTwilio\\\\DataTransferObjects\\\\TwilioMessageReceivedData\\) but returns AdvisingApp\\\\IntegrationTwilio\\\\DataTransferObjects\\\\TwilioMessageReceivedData\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/src/DataTransferObjects/TwilioMessageReceivedData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationTwilio\\\\DataTransferObjects\\\\TwilioStatusCallbackData\\:\\:fromRequest\\(\\) should return static\\(AdvisingApp\\\\IntegrationTwilio\\\\DataTransferObjects\\\\TwilioStatusCallbackData\\) but returns AdvisingApp\\\\IntegrationTwilio\\\\DataTransferObjects\\\\TwilioStatusCallbackData\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/src/DataTransferObjects/TwilioStatusCallbackData.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function is_array\\(\\) with string will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/src/Http/Middleware/LogTwilioRequest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function is_null\\(\\) with AdvisingApp\\\\Notification\\\\Models\\\\SmsMessage will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/src/Jobs/StatusCallback.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining values\\: string\\|null$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/src/Jobs/StatusCallback.php',
];
$ignoreErrors[] = [
    'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
    'identifier' => 'nullCoalesce.expr',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/src/Settings/TwilioSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationTwilio\\\\Settings\\\\TwilioSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/src/Settings/TwilioSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\IntegrationTwilio\\\\Tests\\\\Fixtures\\\\ClientMock\\:\\:__construct\\(\\) has parameter \\$messageList with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/tests/Fixtures/ClientMock.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$job of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/tests/Tenant/Feature/Http/Controllers/TwilioInboundWebhookControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$event of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/tests/Tenant/Feature/Http/Controllers/TwilioInboundWebhookControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$payload of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/tests/Tenant/Feature/Http/Controllers/TwilioInboundWebhookControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$request of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-twilio/tests/Tenant/Feature/Http/Middleware/LogTwilioRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: mixed$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/database/factories/InteractionFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Interaction\\\\Database\\\\Seeders\\\\InteractionSeeder\\:\\:metadataSeeders\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/database/seeders/InteractionSeeder.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Filament/Actions/BulkCreateInteractionAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Filament\\\\Resources\\\\Pages\\\\Page\\:\\:getRecord\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/interaction/src/Filament/Actions/DraftInteractionWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Filament/Resources/InteractionDriverResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Filament/Resources/InteractionInitiativeResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Filament/Resources/InteractionOutcomeResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Filament/Resources/InteractionRelationResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function is_null\\(\\) with Illuminate\\\\Support\\\\Carbon will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Filament/Resources/InteractionResource/Components/InteractionViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Filament/Resources/InteractionStatusResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Filament/Resources/InteractionTypeResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: string$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Imports/InteractionsImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:orderedInteractions\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Models/Interaction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Interaction\\\\Models\\\\Interaction\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Models/Interaction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Interaction\\\\Models\\\\Interaction\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Models/Interaction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Interaction\\\\Models\\\\Interaction\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Models/Interaction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Interaction\\\\Models\\\\Interaction\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Models/Interaction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Interaction\\\\Models\\\\InteractionConfidentialTeam\\:\\:interaction\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Models/InteractionConfidentialTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Interaction\\\\Models\\\\InteractionConfidentialTeam\\:\\:team\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Models/InteractionConfidentialTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Interaction\\\\Models\\\\InteractionConfidentialUser\\:\\:interaction\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Models/InteractionConfidentialUser.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Interaction\\\\Models\\\\InteractionConfidentialUser\\:\\:user\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Models/InteractionConfidentialUser.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function is_null\\(\\) with Illuminate\\\\Support\\\\Carbon will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Observers/InteractionObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$student_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Policies/InteractionPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Interaction\\\\Rules\\\\InteractableIdExistsRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Rules/InteractableIdExistsRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Interaction\\\\Rules\\\\InteractableIdExistsRule\\:\\:\\$data has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/interaction/src/Rules/InteractableIdExistsRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:catchPhrase\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/database/factories/CalendarEventFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:catchPhrase\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/database/factories/EventFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Database\\\\Factories\\\\EventRegistrationFormFactory\\:\\:createFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/database/factories/EventRegistrationFormFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Database\\\\Factories\\\\EventRegistrationFormStepFactory\\:\\:createFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/database/factories/EventRegistrationFormStepFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<int,AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\>\\:\\:each\\(\\) expects callable\\(AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField, int\\)\\: mixed, Closure\\(AdvisingApp\\\\MeetingCenter\\\\Models\\\\EventRegistrationFormField\\)\\: void given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/database/factories/EventRegistrationFormSubmissionFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$weight of method Faker\\\\Generator\\:\\:optional\\(\\) expects float, bool given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/database/factories/EventRegistrationFormSubmissionFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\MeetingCenter\\\\Models\\\\EventRegistrationFormStep\\:\\:\\$form_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Actions/DuplicateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Actions\\\\DuplicateEvent\\:\\:replaceIdsInContent\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Actions/DuplicateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Actions\\\\DuplicateEvent\\:\\:replaceIdsInContent\\(\\) has parameter \\$content with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Actions/DuplicateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Actions\\\\DuplicateEvent\\:\\:replaceIdsInContent\\(\\) has parameter \\$fieldMap with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Actions/DuplicateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Actions\\\\DuplicateEvent\\:\\:replicateFields\\(\\) has parameter \\$stepMap with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Actions/DuplicateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Actions\\\\DuplicateEvent\\:\\:replicateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Actions/DuplicateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Actions\\\\DuplicateEvent\\:\\:replicateSteps\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Actions/DuplicateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Actions\\\\DuplicateEvent\\:\\:updateStepContent\\(\\) has parameter \\$fieldMap with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Actions/DuplicateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Actions\\\\GenerateEventRegistrationFormKitSchema\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Actions/GenerateEventRegistrationFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Enums\\\\CalendarProvider\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Enums/CalendarProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Enums\\\\CalendarProvider\\:\\:getLabel\\(\\) never returns string so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Enums/CalendarProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Enums\\\\EventAttendeeStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Enums/EventAttendeeStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Livewire\\\\Component\\:\\:getRecord\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Actions/InviteEventAttendeeAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Filament\\\\Resources\\\\EventResource\\\\Pages\\\\CreateEvent\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Resources/EventResource/Pages/CreateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Filament\\\\Resources\\\\EventResource\\\\Pages\\\\CreateEvent\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Resources/EventResource/Pages/CreateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Filament\\\\Resources\\\\EventResource\\\\Pages\\\\CreateEvent\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Resources/EventResource/Pages/CreateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$form of method AdvisingApp\\\\MeetingCenter\\\\Filament\\\\Resources\\\\EventResource\\\\Pages\\\\CreateEvent\\:\\:saveFieldsFromComponents\\(\\) expects AdvisingApp\\\\MeetingCenter\\\\Models\\\\EventRegistrationForm, AdvisingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Resources/EventResource/Pages/CreateEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Filament\\\\Resources\\\\EventResource\\\\Pages\\\\EditEvent\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Resources/EventResource/Pages/EditEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Filament\\\\Resources\\\\EventResource\\\\Pages\\\\EditEvent\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Resources/EventResource/Pages/EditEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Filament\\\\Resources\\\\EventResource\\\\Pages\\\\EditEvent\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Resources/EventResource/Pages/EditEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$form of method AdvisingApp\\\\MeetingCenter\\\\Filament\\\\Resources\\\\EventResource\\\\Pages\\\\EditEvent\\:\\:saveFieldsFromComponents\\(\\) expects AdvisingApp\\\\MeetingCenter\\\\Models\\\\EventRegistrationForm, AdvisingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Resources/EventResource/Pages/EditEvent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$title\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Resources/EventResource/Pages/ListEvents.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Filament\\\\Widgets\\\\CalendarEventWidget\\:\\:fetchEvents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Widgets/CalendarEventWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Filament\\\\Widgets\\\\CalendarWidget\\:\\:fetchEvents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Filament/Widgets/CalendarWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$event\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Http/Controllers/EventRegistrationWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\MeetingCenter\\\\Models\\\\EventRegistrationFormAuthentication\\:\\:\\$code\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Http/Controllers/EventRegistrationWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:check\\(\\) expects string, int given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Http/Controllers/EventRegistrationWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int\\<100000, 999999\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Http/Controllers/EventRegistrationWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\MeetingCenter\\\\Models\\\\IdeHelperEventRegistrationFormSubmission\\:\\:\\$submitted_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Http/Controllers/EventRegistrationWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Jobs\\\\CreateEventAttendee\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Jobs/CreateEventAttendee.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Jobs\\\\CreateEventAttendees\\:\\:__construct\\(\\) has parameter \\$emails with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Jobs/CreateEventAttendees.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Jobs\\\\SyncCalendars\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Jobs/SyncCalendars.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Livewire\\\\EventAttendeeSubmissionsManager\\:\\:render\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Livewire/EventAttendeeSubmissionsManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\MeetingCenter\\\\Livewire\\\\RenderEventRegistrationForm\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Livewire/RenderEventRegistrationForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Managers\\\\Contracts\\\\CalendarInterface\\:\\:getEvents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Managers/Contracts/CalendarInterface.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Google\\\\Service\\\\Calendar\\\\Event\\:\\:\\$end\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Managers/GoogleCalendarManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Google\\\\Service\\\\Calendar\\\\Event\\:\\:\\$start\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Managers/GoogleCalendarManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Managers\\\\GoogleCalendarManager\\:\\:getEvents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Managers/GoogleCalendarManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$email of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Managers/GoogleCalendarManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Managers/GoogleCalendarManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Managers/GoogleCalendarManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Managers\\\\OutlookCalendarManager\\:\\:getEvents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Managers/OutlookCalendarManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Managers/OutlookCalendarManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Managers/OutlookCalendarManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$event\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Notifications/AuthenticateEventRegistrationFormNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Notifications\\\\AuthenticateEventRegistrationFormNotification\\:\\:identifyRecipient\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Notifications/AuthenticateEventRegistrationFormNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Notifications\\\\CalendarRequiresReconnectNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Notifications/CalendarRequiresReconnectNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^If condition is always true\\.$#',
    'identifier' => 'if.alwaysTrue',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Observers/CalendarEventObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Services\\\\AzureGraph\\:\\:setAccessToken\\(\\) should return AdvisingApp\\\\MeetingCenter\\\\Services\\\\AzureGraph but returns Microsoft\\\\Graph\\\\Graph\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Services/AzureGraph.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Settings\\\\AzureCalendarSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Settings/AzureCalendarSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MeetingCenter\\\\Settings\\\\GoogleCalendarSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/meeting-center/src/Settings/GoogleCalendarSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/multifactor\\-authentication/database/migrations/2024_06_18_210439_seed_permissions_add_multifactor_settings_permission\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/database/migrations/2024_06_18_210439_seed_permissions_add_multifactor_settings_permission.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/multifactor\\-authentication/database/migrations/2024_06_18_210439_seed_permissions_add_multifactor_settings_permission\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/database/migrations/2024_06_18_210439_seed_permissions_add_multifactor_settings_permission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Filament\\\\Actions\\\\PasswordButtonAction\\:\\:isPasswordSessionValid\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Filament/Actions/PasswordButtonAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Livewire\\\\MultifactorAuthenticationManagement\\:\\:getMultifactorQrCode\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Livewire/MultifactorAuthenticationManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Livewire\\\\MultifactorAuthenticationManagement\\:\\:getRecoveryCodesProperty\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Livewire/MultifactorAuthenticationManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Livewire\\\\MultifactorAuthenticationManagement\\:\\:mount\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Livewire/MultifactorAuthenticationManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Livewire\\\\MultifactorAuthenticationManagement\\:\\:render\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Livewire/MultifactorAuthenticationManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @property\\-read for property AdvisingApp\\\\MultifactorAuthentication\\\\Livewire\\\\MultifactorAuthenticationManagement\\:\\:\\$recoveryCodes contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Livewire/MultifactorAuthenticationManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Livewire/MultifactorAuthenticationManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$action of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Livewire/MultifactorAuthenticationManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$livewire of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Livewire/MultifactorAuthenticationManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Providers\\\\MultifactorAuthenticationServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Providers/MultifactorAuthenticationServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Services\\\\MultifactorService\\:\\:generateSecretKey\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Services/MultifactorService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Services\\\\MultifactorService\\:\\:getEngine\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Services/MultifactorService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Services\\\\MultifactorService\\:\\:getMultifactorQrCodeSvg\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Services/MultifactorService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Services\\\\MultifactorService\\:\\:getQrCodeUrl\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Services/MultifactorService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Services\\\\MultifactorService\\:\\:getQrCodeUrl\\(\\) has parameter \\$companyEmail with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Services/MultifactorService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Services\\\\MultifactorService\\:\\:getQrCodeUrl\\(\\) has parameter \\$companyName with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Services/MultifactorService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Services\\\\MultifactorService\\:\\:getQrCodeUrl\\(\\) has parameter \\$secret with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Services/MultifactorService.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\MultifactorAuthentication\\\\Services\\\\MultifactorService\\:\\:verify\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/multifactor-authentication/src/Services/MultifactorService.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/notification/database/migrations/2025_02_10_233057_seed_permissions_remove_outbound_deliverable_permissions\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/database/migrations/2025_02_10_233057_seed_permissions_remove_outbound_deliverable_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/notification/database/migrations/2025_02_10_233057_seed_permissions_remove_outbound_deliverable_permissions\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/database/migrations/2025_02_10_233057_seed_permissions_remove_outbound_deliverable_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Actions\\\\SubscriptionToggle\\:\\:handle\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Actions/SubscriptionToggle.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\DataTransferObjects\\\\EmailChannelResultData\\:\\:__construct\\(\\) has parameter \\$recipients with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/DataTransferObjects/EmailChannelResultData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Enums\\\\EmailMessageEventType\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Enums/EmailMessageEventType.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Enums\\\\EmailMessageEventType\\:\\:getLabel\\(\\) never returns string so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Enums/EmailMessageEventType.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Enums\\\\NotificationChannel\\:\\:getEngagementOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Enums/NotificationChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Enums\\\\NotificationChannel\\:\\:getIcon\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Enums/NotificationChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Enums\\\\NotificationDeliveryStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Enums/NotificationDeliveryStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Enums\\\\SmsMessageEventType\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Enums/SmsMessageEventType.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Notification\\\\Exceptions\\\\NotificationQuotaExceeded\\:\\:\\$message has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Exceptions/NotificationQuotaExceeded.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Notification\\\\Exceptions\\\\SubscriptionAlreadyExistsException\\:\\:\\$message has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Exceptions/SubscriptionAlreadyExistsException.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Filament/Actions/SubscribeBulkAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:displayNameKey\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionCreated.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>&literal\\-string$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionCreated.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:displayNameKey\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionDeleted.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>&literal\\-string$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionDeleted.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifications\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notify\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notify\\(\\) has parameter \\$instance with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifyNow\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifyNow\\(\\) has parameter \\$channels with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifyNow\\(\\) has parameter \\$instance with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:readNotifications\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:routeNotificationFor\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:routeNotificationFor\\(\\) has parameter \\$driver with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:routeNotificationFor\\(\\) has parameter \\$notification with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:unreadNotifications\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\Message\\:\\:recipient\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/Message.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\Message\\:\\:related\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/Message.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Subscription\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Models\\\\Subscription\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Notifications\\\\Notification\\:\\:toDatabase\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/DatabaseChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: true$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/DatabaseChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(void\\) of method AdvisingApp\\\\Notification\\\\Notifications\\\\Channels\\\\DatabaseChannel\\:\\:send\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Model\\) of method Illuminate\\\\Notifications\\\\Channels\\\\DatabaseChannel\\:\\:send\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/DatabaseChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Notifications\\\\Notification\\:\\:toMail\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
    'identifier' => 'nullCoalesce.expr',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Notifications\\\\Messages\\\\SimpleMessage\\:\\:\\$mailer \\(string\\) on left side of \\?\\? is not nullable\\.$#',
    'identifier' => 'nullCoalesce.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(void\\) of method AdvisingApp\\\\Notification\\\\Notifications\\\\Channels\\\\MailChannel\\:\\:send\\(\\) should be compatible with return type \\(Illuminate\\\\Mail\\\\SentMessage\\|null\\) of method Illuminate\\\\Notifications\\\\Channels\\\\MailChannel\\:\\:send\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>isDemoModeEnabled" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type mixed\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Notifications\\\\Notification\\:\\:toSms\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/SmsChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Notifications\\\\Channels\\\\SmsChannel\\:\\:determineQuotaUsage\\(\\) should return int but returns string\\|null\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/SmsChannel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Notifications\\\\Contracts\\\\OnDemandNotification\\:\\:identifyRecipient\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Contracts/OnDemandNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Notifications\\\\Messages\\\\MailMessage\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Messages/MailMessage.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Notifications\\\\Messages\\\\SimpleMessage\\:\\:\\$actionUrl \\(string\\) on left side of \\?\\? is not nullable\\.$#',
    'identifier' => 'nullCoalesce.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Messages/MailMessage.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Notifications\\\\Messages\\\\TwilioMessage\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Notifications/Messages/TwilioMessage.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getLicenseType\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/notification/src/Policies/SubscriptionPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/notification/src/Policies/SubscriptionPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Rules\\\\SubscribableIdExistsRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Rules/SubscribableIdExistsRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Notification\\\\Rules\\\\SubscribableIdExistsRule\\:\\:\\$data has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Rules/SubscribableIdExistsRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Rules\\\\UniqueSubscriptionRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Rules/UniqueSubscriptionRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Notification\\\\Rules\\\\UniqueSubscriptionRule\\:\\:\\$data has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/Rules/UniqueSubscriptionRule.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Notification\\\\Tests\\\\Fixtures\\\\TestDatabaseNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/tests/Fixtures/TestDatabaseNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$job of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/tests/Tenant/Notifications/ChannelManagerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Method TestSystemNotification\\:\\:via\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/tests/Tenant/Notifications/Channels/MailChannelEmailMessageTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Match arm comparison between AdvisingApp\\\\Portal\\\\Enums\\\\PortalType\\:\\:ResourceHub and AdvisingApp\\\\Portal\\\\Enums\\\\PortalType\\:\\:ResourceHub is always true\\.$#',
    'identifier' => 'match.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Actions/GeneratePortalEmbedCode.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Portal\\\\DataTransferObjects\\\\ResourceHubSearchData\\:\\:\\$articles with generic class Spatie\\\\LaravelData\\\\DataCollection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/DataTransferObjects/ResourceHubSearchData.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Portal\\\\DataTransferObjects\\\\ResourceHubSearchData\\:\\:\\$categories with generic class Spatie\\\\LaravelData\\\\DataCollection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/DataTransferObjects/ResourceHubSearchData.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Portal\\\\Enums\\\\PortalType\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Enums/PortalType.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:check\\(\\) expects string, int given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/ResourceHub/ResourceHubPortalAuthenticateController.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\ResourceHub\\\\Models\\\\ResourceHubArticle\\:\\:\\$categoryId\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/ResourceHub/ResourceHubPortalCategoryController.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\ResourceHub\\\\Models\\\\ResourceHubArticle\\:\\:\\$name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/ResourceHub/ResourceHubPortalCategoryController.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Portal\\\\Http\\\\Controllers\\\\ResourceHub\\\\ResourceHubPortalLogoutController\\:\\:__invoke\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/ResourceHub/ResourceHubPortalLogoutController.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Support\\\\ValidatedInput\\:\\:\\$email\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/ResourceHub/ResourceHubPortalRequestAuthenticationController.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Support\\\\ValidatedInput\\:\\:\\$isSpa\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/ResourceHub/ResourceHubPortalRequestAuthenticationController.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int\\<100000, 999999\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/ResourceHub/ResourceHubPortalRequestAuthenticationController.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$category of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/ResourceHub/ResourceHubPortalSearchController.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$item of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/ResourceHub/ResourceHubPortalSearchController.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Portal\\\\Http\\\\Requests\\\\ResourceHubPortalAuthenticationRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Requests/ResourceHubPortalAuthenticationRequest.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Portal\\\\Models\\\\PortalAuthentication\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Models/PortalAuthentication.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Portal\\\\Notifications\\\\AuthenticatePortalNotification\\:\\:identifyRecipient\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Notifications/AuthenticatePortalNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Portal\\\\Providers\\\\PortalServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Providers/PortalServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Portal\\\\Settings\\\\PortalSettings\\:\\:\\$footer_copyright_statement type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Settings/PortalSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Portal\\\\Settings\\\\PortalSettings\\:\\:\\$gdpr_banner_text type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Settings/PortalSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:state\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/database/factories/ProspectAddressFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:secondaryAddress\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/database/factories/ProspectFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:stateAbbr\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/database/factories/ProspectFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
    'identifier' => 'nullCoalesce.expr',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/database/factories/ProspectFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Database\\\\Factories\\\\ProspectPhoneNumberFactory\\:\\:canNotReceiveSms\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/database/factories/ProspectPhoneNumberFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Database\\\\Factories\\\\ProspectPhoneNumberFactory\\:\\:canReceiveSms\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/database/factories/ProspectPhoneNumberFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Database\\\\Factories\\\\ProspectPhoneNumberFactory\\:\\:withExtension\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/database/factories/ProspectPhoneNumberFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/prospect/database/migrations/2024_10_08_174318_seed_permissions_for_prospect_conversion\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/database/migrations/2024_10_08_174318_seed_permissions_for_prospect_conversion.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/prospect/database/migrations/2024_10_08_174318_seed_permissions_for_prospect_conversion\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/database/migrations/2024_10_08_174318_seed_permissions_for_prospect_conversion.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Enums\\\\SystemProspectClassification\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Enums/SystemProspectClassification.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Actions/ProspectTagsBulkAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Pages\\\\ManageProspectConversionSettings\\:\\:getFormActions\\(\\) has invalid return type AdvisingApp\\\\Prospect\\\\Filament\\\\Pages\\\\Action\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Pages/ManageProspectConversionSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Pages\\\\ManageProspectConversionSettings\\:\\:getFormActions\\(\\) has invalid return type AdvisingApp\\\\Prospect\\\\Filament\\\\Pages\\\\ActionGroup\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Pages/ManageProspectConversionSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Pages\\\\ManageProspectConversionSettings\\:\\:getFormActions\\(\\) should return array\\<AdvisingApp\\\\Prospect\\\\Filament\\\\Pages\\\\Action\\|AdvisingApp\\\\Prospect\\\\Filament\\\\Pages\\\\ActionGroup\\> but returns array\\<Filament\\\\Actions\\\\Action\\|Filament\\\\Actions\\\\ActionGroup\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Pages/ManageProspectConversionSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$otherid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$preferred\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryEmailAddress\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryPhoneNumber\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\:\\:getGlobalSearchEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\:\\:modifyGlobalSearchQuery\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\:\\:scoreGlobalSearchResults\\(\\) has parameter \\$attributeScores with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\:\\:scoreGlobalSearchResults\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined static method Filament\\\\Pages\\\\Page\\:\\:getResourcePageName\\(\\)\\.$#',
    'identifier' => 'staticMethod.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Actions/ConvertToStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Negated boolean expression is always false\\.$#',
    'identifier' => 'booleanNot.alwaysFalse',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Actions/ConvertToStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Actions/ConvertToStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method save\\(\\) on an unknown class AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Actions\\\\Prospect\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Actions/DisassociateStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method status\\(\\) on an unknown class AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Actions\\\\Prospect\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Actions/DisassociateStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method student\\(\\) on an unknown class AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Actions\\\\Prospect\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Actions/DisassociateStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$record contains unknown class AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Actions\\\\Prospect\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Actions/DisassociateStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Actions/DisassociateStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/CreateProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:addresses\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/CreateProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:emailAddresses\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/CreateProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:phoneNumbers\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/CreateProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:primaryAddress\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/CreateProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:primaryEmailAddress\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/CreateProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:primaryPhoneNumber\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/CreateProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/EditProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:addresses\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/EditProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:emailAddresses\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/EditProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:phoneNumbers\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/EditProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:primaryAddress\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/EditProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:primaryEmailAddress\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/EditProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:primaryPhoneNumber\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/EditProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\EditProspect\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/EditProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ListProspects\\:\\:segmentFilter\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ListProspects.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ListProspects\\:\\:segmentFilter\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ListProspects.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\)\\: bool given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ListProspects.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ManageProspectAlerts\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ManageProspectAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$badge of method Filament\\\\Navigation\\\\NavigationItem\\:\\:badge\\(\\) expects Closure\\|string\\|null, int\\<1, max\\>\\|null given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ManageProspectAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ManageProspectCareTeam\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ManageProspectCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ManageProspectSubscriptions\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ManageProspectSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ManageProspectTasks\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ManageProspectTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ViewProspect\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ViewProspect.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:timeline\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ViewProspectActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ViewProspectActivityFeed\\:\\:viewRecord\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ViewProspectActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ViewProspectActivityFeed\\:\\:viewRecord\\(\\) has parameter \\$key with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ViewProspectActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ViewProspectActivityFeed\\:\\:viewRecord\\(\\) has parameter \\$morphReference with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ViewProspectActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ViewProspectActivityFeed\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ViewProspectActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectResource\\\\Pages\\\\ViewProspectActivityFeed\\:\\:\\$timelineRecords with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectResource/Pages/ViewProspectActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Dead catch \\- Illuminate\\\\Database\\\\QueryException is never thrown in the try block\\.$#',
    'identifier' => 'catch.neverThrown',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectStatusResource/Pages/ListProspectStatuses.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$is_system_protected\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectStatusResource/Pages/ViewProspectStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectStatusResource/Pages/ViewProspectStatus.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method hasLicense\\(\\) on an unknown class AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectTagResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\ProspectTagResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectTagResource.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AdvisingApp\\\\Prospect\\\\Filament\\\\Resources\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Filament/Resources/ProspectTagResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:additionalAddresses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:additionalEmailAddresses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:additionalPhoneNumbers\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:alertHistories\\(\\) return type with generic class Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:careTeam\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<App\\\\Models\\\\User, \\$this\\(AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\), Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphPivot, \'pivot\'\\> but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<App\\\\Models\\\\User, \\$this\\(AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\), AdvisingApp\\\\CareTeam\\\\Models\\\\CareTeam, \'pivot\'\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:displayName\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:fullAddress\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:orderedEngagementResponses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:orderedEngagements\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:subscribedUsers\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<App\\\\Models\\\\User, \\$this\\(AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\), Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphPivot, \'pivot\'\\> but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<App\\\\Models\\\\User, \\$this\\(AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\), AdvisingApp\\\\Notification\\\\Models\\\\Subscription, \'pivot\'\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:tags\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<App\\\\Models\\\\Tag, \\$this\\(AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\), Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphPivot, \'pivot\'\\> but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<App\\\\Models\\\\Tag, \\$this\\(AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\), App\\\\Models\\\\Taggable, \'pivot\'\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:taskHistories\\(\\) return type with generic class Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\:\\:timeline\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @return with type array\\<string, string\\>\\|string\\|null is not subtype of native type string\\|null\\.$#',
    'identifier' => 'return.phpDocType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/Prospect.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Prospect\\\\Models\\\\ProspectAddress\\:\\:full\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Models/ProspectAddress.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Prospect\\\\Models\\\\IdeHelperProspectAddress\\:\\:\\$order \\(int\\) does not accept Illuminate\\\\Contracts\\\\Database\\\\Query\\\\Expression\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Observers/ProspectAddressObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Prospect\\\\Models\\\\IdeHelperProspectEmailAddress\\:\\:\\$order \\(int\\) does not accept Illuminate\\\\Contracts\\\\Database\\\\Query\\\\Expression\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Observers/ProspectEmailAddressObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Prospect\\\\Models\\\\IdeHelperProspectPhoneNumber\\:\\:\\$order \\(int\\) does not accept Illuminate\\\\Contracts\\\\Database\\\\Query\\\\Expression\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Observers/ProspectPhoneNumberObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/src/Policies/ProspectPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to method static method Illuminate\\\\Support\\\\Arr\\:\\:first\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/Prospect/CreateProspectTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:state\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/Prospect/RequestFactories/CreateProspectRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/Prospect/RequestFactories/EditProspectRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AdvisingApp\\\\Prospect\\\\Models\\\\ProspectSource\\> will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/ProspectSource/CreateProspectSourceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/ProspectSource/CreateProspectSourceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/ProspectSource/CreateProspectSourceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/ProspectSource/EditProspectSourceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/ProspectSource/EditProspectSourceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AdvisingApp\\\\Prospect\\\\Models\\\\ProspectStatus\\> will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/ProspectStatus/CreateProspectStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/ProspectStatus/CreateProspectStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/ProspectStatus/CreateProspectStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/ProspectStatus/EditProspectStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/ProspectStatus/EditProspectStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$money of static method Cknow\\\\Money\\\\Money\\:\\:parseByDecimal\\(\\) expects string, float given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/prospect/tests/Tenant/Unit/ProspectConversionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type App\\\\Models\\\\User\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/database/factories/ReportFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/report/database/migrations/2024_06_05_110340_seed_permissions_add_report_library\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/database/migrations/2024_06_05_110340_seed_permissions_add_report_library.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/report/database/migrations/2024_06_05_110340_seed_permissions_add_report_library\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/database/migrations/2024_06_05_110340_seed_permissions_add_report_library.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method can\\(\\) on an unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/AiReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method hasLicense\\(\\) on an unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/AiReport.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/AiReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method can\\(\\) on an unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/EngagementReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method hasLicense\\(\\) on an unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/EngagementReport.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/EngagementReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method can\\(\\) on an unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/ProspectReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method hasLicense\\(\\) on an unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/ProspectReport.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/ProspectReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method can\\(\\) on an unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/StudentReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method hasLicense\\(\\) on an unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/StudentReport.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/StudentReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method can\\(\\) on an unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/UserReport.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AdvisingApp\\\\Report\\\\Abstract\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Abstract/UserReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Enums\\\\ReportModel\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Enums/ReportModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Enums\\\\ReportModel\\:\\:query\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Enums/ReportModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Enums\\\\TrackedEventType\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Enums/TrackedEventType.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Alert\\\\Models\\\\AlertStatus\\:\\:\\$name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Exports/StudentExporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Report\\\\Filament\\\\Pages\\\\ArtificialIntelligence\\:\\:\\$cacheTag has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/ArtificialIntelligence.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Report\\\\Filament\\\\Pages\\\\ProspectEnagagementReport\\:\\:\\$cacheTag has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/ProspectEnagagementReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Report\\\\Filament\\\\Pages\\\\ProspectReport\\:\\:\\$cacheTag has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/ProspectReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Report\\\\Filament\\\\Pages\\\\StudentDeliverabilityReport\\:\\:\\$cacheTag has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/StudentDeliverabilityReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Report\\\\Filament\\\\Pages\\\\StudentEngagementReport\\:\\:\\$cacheTag has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/StudentEngagementReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Report\\\\Filament\\\\Pages\\\\Students\\:\\:\\$cacheTag has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/Students.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Report\\\\Filament\\\\Pages\\\\TaskManagement\\:\\:\\$cacheTag has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/TaskManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Report\\\\Filament\\\\Pages\\\\UserLoginActivity\\:\\:\\$cacheTag has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/UserLoginActivity.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Resources\\\\ReportResource\\\\Pages\\\\CreateReport\\:\\:getReportModels\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Resources/ReportResource/Pages/CreateReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Resources\\\\ReportResource\\\\Pages\\\\CreateReport\\:\\:getSteps\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Resources/ReportResource/Pages/CreateReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Resources/ReportResource/Pages/CreateReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Resources/ReportResource/Pages/CreateReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$filters\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Resources/ReportResource/Pages/EditReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$model\\.$#',
    'identifier' => 'property.notFound',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Resources/ReportResource/Pages/EditReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$user\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Resources/ReportResource/Pages/EditReport.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function never returns int so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/AiStats.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\ChartReportWidget\\:\\:mount\\(\\) has parameter \\$cacheTag with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ChartReportWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\ChartReportWidget\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ChartReportWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\ExchangesByMonthLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ExchangesByMonthLineChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\LineChartReportWidget\\:\\:mount\\(\\) has parameter \\$cacheTag with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/LineChartReportWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\LineChartReportWidget\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/LineChartReportWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostEngagedProspectsTable\\:\\:mount\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostEngagedProspectsTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostEngagedProspectsTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostEngagedProspectsTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostEngagedStudentsTable\\:\\:mount\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostEngagedStudentsTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostEngagedStudentsTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostEngagedStudentsTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostRecentStudentsTable\\:\\:mount\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentStudentsTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostRecentStudentsTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentStudentsTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentStudentsTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function is_null\\(\\) with AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\|AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining values\\: \\(class\\-string\\<AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\>&literal\\-string\\)\\|\\(class\\-string\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\>&literal\\-string\\)$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostRecentTasksTable\\:\\:mount\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostRecentTasksTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Ternary operator condition is always true\\.$#',
    'identifier' => 'ternary.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>\\(Expression\\)" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\PieChartReportWidget\\:\\:mount\\(\\) has parameter \\$cacheTag with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/PieChartReportWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\PieChartReportWidget\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/PieChartReportWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\PromptsByCategoryDoughnutChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/PromptsByCategoryDoughnutChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\PromptsCreatedLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/PromptsCreatedLineChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\ProspectEngagementLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ProspectEngagementLineChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ProspectInteractionStatusPolarAreaChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ProspectInteractionTypeDoughnutChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$interaction of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ProspectInteractionUsersTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ProspectInteractionUsersTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 6,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ProspectInteractionUsersTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$teamQuery of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ProspectInteractionUsersTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\ProspectReportLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ProspectReportLineChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\ProspectReportTableChart\\:\\:mount\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ProspectReportTableChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\ProspectReportTableChart\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ProspectReportTableChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ProspectReportTableChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\RefreshWidget\\:\\:removeWidgetCache\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/RefreshWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\SpecialActionsDoughnutChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/SpecialActionsDoughnutChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\SpecialActionsDoughnutChart\\:\\:getRgbString\\(\\) has parameter \\$color with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/SpecialActionsDoughnutChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\StatsOverviewReportWidget\\:\\:mount\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StatsOverviewReportWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\StatsOverviewReportWidget\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StatsOverviewReportWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\StudentCumulativeCountLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentCumulativeCountLineChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\StudentDeliverableTable\\:\\:mount\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentDeliverableTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\StudentDeliverableTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentDeliverableTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\StudentEmailOptInOptOutPieChart\\:\\:getRgbString\\(\\) has parameter \\$color with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentEmailOptInOptOutPieChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\StudentEngagementLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentEngagementLineChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentInteractionStats.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentInteractionStatusPolarAreaChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentInteractionTypeDoughnutChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$interaction of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentInteractionUsersTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentInteractionUsersTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 6,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentInteractionUsersTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$teamQuery of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentInteractionUsersTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\StudentSmsOptInOptOutPieChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentSmsOptInOptOutPieChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\StudentSmsOptInOptOutPieChart\\:\\:getRgbString\\(\\) has parameter \\$color with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StudentSmsOptInOptOutPieChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\TaskCumulativeCountLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/TaskCumulativeCountLineChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\UserUniqueLoginCountLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/UserUniqueLoginCountLineChart.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: string$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/UsersLoginCountTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\UsersLoginCountTable\\:\\:mount\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/UsersLoginCountTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Filament\\\\Widgets\\\\UsersLoginCountTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/UsersLoginCountTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/UsersLoginCountTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Report\\\\Providers\\\\ReportServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Providers/ReportServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$interaction of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Filament/Widgets/ProspectInteractionUsersTableTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$interaction of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Filament/Widgets/StudentInteractionUsersTableTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$user of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Unit/UniqueLogin/UserStatsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\|Pest\\\\Support\\\\HigherOrderTapProxy\\:\\:travelTo\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Unit/UniqueLogin/UserUniqueLoginCountLineChartTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Spatie\\\\Invade\\\\Invader\\<object\\>\\|Spatie\\\\Invade\\\\StaticInvader\\:\\:getData\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Unit/UniqueLogin/UserUniqueLoginCountLineChartTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type T in call to function invade$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Unit/UniqueLogin/UserUniqueLoginCountLineChartTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Unit/UniqueLogin/UserUniqueLoginCountLineChartTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Carbon\\\\Carbon\\:\\:subMonth\\(\\) invoked with 1 parameter, 0 required\\.$#',
    'identifier' => 'arguments.count',
    'count' => 6,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Unit/UniqueLogin/UsersLoginCountTableTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/database/factories/ResourceHubArticleFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Filament\\\\Resources\\\\Pages\\\\Page\\:\\:\\$data\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Actions/DraftResourceHubArticleWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type Filament\\\\Resources\\\\Pages\\\\Page\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Actions/DraftResourceHubArticleWithAiAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$category\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$division\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$quality\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$status\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\ResourceHub\\\\Filament\\\\Resources\\\\ResourceHubArticleResource\\:\\:getGlobalSearchEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$title\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/EditResourceHubArticle.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/EditResourceHubArticle.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\ResourceHub\\\\Filament\\\\Resources\\\\ResourceHubArticleResource\\\\Pages\\\\EditResourceHubArticleMetadata\\:\\:form\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/EditResourceHubArticleMetadata.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\ResourceHub\\\\Models\\\\ResourceHubArticle\\:\\:\\$my_upvotes_count\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/ListResourceHubArticles.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$notes\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/ListResourceHubArticles.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$public\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/ListResourceHubArticles.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$title\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/ListResourceHubArticles.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:isUpvoted\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/ViewResourceHubArticle.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:toggleUpvote\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/ViewResourceHubArticle.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:upvotes\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/ViewResourceHubArticle.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:views\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/ViewResourceHubArticle.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot access property \\$title on Illuminate\\\\Database\\\\Eloquent\\\\Model\\|int\\|string\\.$#',
    'identifier' => 'property.nonObject',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Filament/Resources/ResourceHubArticleResource/Pages/ViewResourceHubArticle.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\ResourceHub\\\\Jobs\\\\ResourceHubArticleDownloadExternalMedia\\:\\:processContentItem\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Jobs/ResourceHubArticleDownloadExternalMedia.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\ResourceHub\\\\Jobs\\\\ResourceHubArticleDownloadExternalMedia\\:\\:processContentItem\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Jobs/ResourceHubArticleDownloadExternalMedia.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$item of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Jobs/ResourceHubArticleDownloadExternalMedia.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\ResourceHub\\\\Models\\\\ResourceHubArticle\\:\\:scopePublic\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Models/ResourceHubArticle.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\ResourceHub\\\\Models\\\\ResourceHubArticle\\:\\:scopePublic\\(\\) has parameter \\$query with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/resource-hub/src/Models/ResourceHubArticle.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/resource-hub/tests/Tenant/ResourceHubArticle/RequestFactories/CreateResourceHubArticleRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type App\\\\Models\\\\User\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/database/factories/SegmentFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Segment\\\\Enums\\\\SegmentModel\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Enums/SegmentModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Segment\\\\Enums\\\\SegmentModel\\:\\:getSubjectImporter\\(\\) has invalid return type AdvisingApp\\\\Segment\\\\Enums\\\\Importer\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Enums/SegmentModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Segment\\\\Enums\\\\SegmentModel\\:\\:getSubjectImporter\\(\\) should return class\\-string\\<AdvisingApp\\\\Segment\\\\Enums\\\\Importer\\> but returns string\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Enums/SegmentModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Segment\\\\Enums\\\\SegmentModel\\:\\:query\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Enums/SegmentModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Segment\\\\Enums\\\\SegmentType\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Enums/SegmentType.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property App\\\\Models\\\\Import\\:\\:\\$failed_at\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/CreateSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to protected property Spatie\\\\Invade\\\\Invader\\<Illuminate\\\\Filesystem\\\\AwsS3V3Adapter\\>\\:\\:\\$client\\.$#',
    'identifier' => 'property.protected',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/CreateSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined static method AdvisingApp\\\\Segment\\\\Enums\\\\Importer\\:\\:getCompletedNotificationBody\\(\\)\\.$#',
    'identifier' => 'staticMethod.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/CreateSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Segment\\\\Filament\\\\Resources\\\\SegmentResource\\\\Pages\\\\CreateSegment\\:\\:getSteps\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/CreateSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var with type array\\<array\\<array\\<string, string\\>\\>\\> is not subtype of native type Generator\\.$#',
    'identifier' => 'varTag.nativeType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/CreateSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Filament\\\\Actions\\\\Imports\\\\Models\\\\Import\\:\\:\\$importer \\(class\\-string\\<Filament\\\\Actions\\\\Imports\\\\Importer\\>\\) does not accept class\\-string\\<AdvisingApp\\\\Segment\\\\Enums\\\\Importer\\>\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/CreateSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to method static method Illuminate\\\\Support\\\\Arr\\:\\:first\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/CreateSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$filters\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/EditSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$model\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/EditSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$type\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/EditSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$user\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/EditSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:subjects\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/EditSegment.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$filters\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/GetSegmentQuery.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$model\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/GetSegmentQuery.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$type\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/GetSegmentQuery.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$user\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/GetSegmentQuery.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:subjects\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResource/Pages/GetSegmentQuery.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Segment\\\\Filament\\\\Resources\\\\SegmentResourceForProcesses\\:\\:canAccess\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Filament/Resources/SegmentResourceForProcesses.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Segment\\\\Models\\\\Segment\\:\\:scopeModel\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Models/Segment.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type AdvisingApp\\\\Segment\\\\Enums\\\\SegmentModel\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/segment/src/Policies/SegmentPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Segment\\\\Providers\\\\SegmentServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/segment/src/Providers/SegmentServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:state\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/factories/StudentAddressFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Database\\\\Factories\\\\StudentPhoneNumberFactory\\:\\:canNotReceiveSms\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/factories/StudentPhoneNumberFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Database\\\\Factories\\\\StudentPhoneNumberFactory\\:\\:canReceiveSms\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/factories/StudentPhoneNumberFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Database\\\\Factories\\\\StudentPhoneNumberFactory\\:\\:withExtension\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/factories/StudentPhoneNumberFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2024_08_19_204749_seed_permissions_add_manage_sis_settings\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2024_08_19_204749_seed_permissions_add_manage_sis_settings.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2024_08_19_204749_seed_permissions_add_manage_sis_settings\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2024_08_19_204749_seed_permissions_add_manage_sis_settings.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2024_10_01_133436_seed_permissions_for_student_record_manager\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2024_10_01_133436_seed_permissions_for_student_record_manager.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2024_10_01_133436_seed_permissions_for_student_record_manager\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2024_10_01_133436_seed_permissions_for_student_record_manager.php',
];
$ignoreErrors[] = [
    'message' => '#^Method Illuminate\\\\Database\\\\Schema\\\\Blueprint\\:\\:float\\(\\) invoked with 3 parameters, 1\\-2 required\\.$#',
    'identifier' => 'arguments.count',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2024_11_25_150149_update_programs_table_columns.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2025_01_03_155805_seed_permissions_add_remaining_student_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2025_01_03_155805_seed_permissions_add_remaining_student_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2025_01_03_155805_seed_permissions_add_remaining_student_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2025_01_03_155805_seed_permissions_add_remaining_student_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2025_01_06_190706_seed_permissions_add_remaining_enrollment_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2025_01_06_190706_seed_permissions_add_remaining_enrollment_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2025_01_06_190706_seed_permissions_add_remaining_enrollment_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2025_01_06_190706_seed_permissions_add_remaining_enrollment_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2025_01_06_190750_seed_permissions_add_remaining_program_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2025_01_06_190750_seed_permissions_add_remaining_program_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2025_01_06_190750_seed_permissions_add_remaining_program_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2025_01_06_190750_seed_permissions_add_remaining_program_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2025_03_19_185332_data_seed_record_sync_permissions\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2025_03_19_185332_data_seed_record_sync_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/student\\-data\\-model/database/migrations/2025_03_19_185332_data_seed_record_sync_permissions\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/database/migrations/2025_03_19_185332_data_seed_record_sync_permissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Instanceof between Illuminate\\\\Contracts\\\\Auth\\\\Authenticatable and Illuminate\\\\Contracts\\\\Auth\\\\Authenticatable will always evaluate to true\\.$#',
    'identifier' => 'instanceof.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Actions/CompleteStudentDataImport.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\StudentDataModel\\\\Models\\\\IdeHelperStudentDataImport\\:\\:\\$canceled_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Actions/CompleteStudentDataImport.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\StudentDataModel\\\\Models\\\\IdeHelperStudentDataImport\\:\\:\\$completed_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Actions/CompleteStudentDataImport.php',
];
$ignoreErrors[] = [
    'message' => '#^If condition is always true\\.$#',
    'identifier' => 'if.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Actions/ResolveEducatableFromEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
    'identifier' => 'deadCode.unreachable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Actions/ResolveEducatableFromEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Match arm comparison between \\$this\\(AdvisingApp\\\\StudentDataModel\\\\Enums\\\\SisSystem\\)&AdvisingApp\\\\StudentDataModel\\\\Enums\\\\SisSystem\\:\\:ThesisElements and AdvisingApp\\\\StudentDataModel\\\\Enums\\\\SisSystem\\:\\:ThesisElements is always true\\.$#',
    'identifier' => 'match.alwaysTrue',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Enums/SisSystem.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Foundation\\\\Bus\\\\PendingChain\\:\\:finally\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Actions/ImportStudentDataAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$callback of method Illuminate\\\\Bus\\\\PendingBatch\\:\\:when\\(\\) expects \\(callable\\(Illuminate\\\\Bus\\\\PendingBatch, bool\\)\\: Illuminate\\\\Foundation\\\\Bus\\\\PendingChain\\)\\|null, Closure\\(Illuminate\\\\Foundation\\\\Bus\\\\PendingChain\\)\\: Illuminate\\\\Foundation\\\\Bus\\\\PendingChain given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Actions/ImportStudentDataAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Actions/StudentTagsBulkAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Imports\\\\EnrollmentImporter\\:\\:getEnrollmentColumns\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Imports/EnrollmentImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Imports\\\\EnrollmentImporter\\:\\:getProgramColumns\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Imports/EnrollmentImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Imports\\\\ProgramImporter\\:\\:getEnrollmentColumns\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Imports/ProgramImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Imports\\\\ProgramImporter\\:\\:getProgramColumns\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Imports/ProgramImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Imports/StudentAddressImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Imports/StudentEmailAddressImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Imports\\\\StudentEnrollmentImporter\\:\\:getEnrollmentColumns\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Imports/StudentEnrollmentImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Imports\\\\StudentEnrollmentImporter\\:\\:getProgramColumns\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Imports/StudentEnrollmentImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Imports/StudentPhoneNumberImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Imports\\\\StudentProgramImporter\\:\\:getEnrollmentColumns\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Imports/StudentProgramImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Imports\\\\StudentProgramImporter\\:\\:getProgramColumns\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Imports/StudentProgramImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Pages\\\\ManageStudentConfiguration\\:\\:getFormActions\\(\\) has invalid return type AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Pages\\\\Action\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Pages/ManageStudentConfiguration.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Pages\\\\ManageStudentConfiguration\\:\\:getFormActions\\(\\) has invalid return type AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Pages\\\\ActionGroup\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Pages/ManageStudentConfiguration.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Pages\\\\ManageStudentConfiguration\\:\\:getFormActions\\(\\) should return array\\<AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Pages\\\\Action\\|AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Pages\\\\ActionGroup\\> but returns array\\<Filament\\\\Actions\\\\Action\\|Filament\\\\Actions\\\\ActionGroup\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Pages/ManageStudentConfiguration.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$event\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sender\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$subject\\.$#',
    'identifier' => 'property.notFound',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$user\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getBody\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getDeliveryMethod\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:timeline\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: string$#',
    'identifier' => 'match.unhandled',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\EducatableResource\\\\Widgets\\\\EducatableActivityFeedWidget\\:\\:viewRecord\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\EducatableResource\\\\Widgets\\\\EducatableActivityFeedWidget\\:\\:viewRecord\\(\\) has parameter \\$key with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\EducatableResource\\\\Widgets\\\\EducatableActivityFeedWidget\\:\\:viewRecord\\(\\) has parameter \\$morphReference with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\EducatableResource\\\\Widgets\\\\EducatableActivityFeedWidget\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\EducatableResource\\\\Widgets\\\\EducatableActivityFeedWidget\\:\\:\\$timelineRecords with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableActivityFeedWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\EducatableResource\\\\Widgets\\\\EducatableCareTeamWidget\\:\\:getCareTeam\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableCareTeamWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable&Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:subscribedUsers\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableSubscriptionsWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\EducatableResource\\\\Widgets\\\\EducatableSubscriptionsWidget\\:\\:getSubscribedUsers\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableSubscriptionsWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\EducatableResource\\\\Widgets\\\\EducatableTasksWidget\\:\\:getStatusCounts\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/EducatableResource/Widgets/EducatableTasksWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$otherid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$preferred\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryEmailAddress\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryPhoneNumber\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\:\\:getGlobalSearchEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\:\\:modifyGlobalSearchQuery\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\:\\:scoreGlobalSearchResults\\(\\) has parameter \\$attributeScores with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\:\\:scoreGlobalSearchResults\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/CreateStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dfw\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dual\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$first\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$firstgen\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$full_name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$last\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$preferred\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryEmailAddress\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryPhoneNumber\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sap\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function never returns null so it can be removed from the return type\\.$#',
    'identifier' => 'return.unusedType',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/EditStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Comparison operation "\\>" between int\\<1, max\\> and 0 is always true\\.$#',
    'identifier' => 'greater.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ListStudents.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\ListStudents\\:\\:segmentFilter\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ListStudents.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\ListStudents\\:\\:segmentFilter\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ListStudents.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dfw\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dual\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$first\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$firstgen\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$full_name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$last\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$preferred\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryEmailAddress\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryPhoneNumber\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sap\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentAlerts.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dfw\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dual\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$first\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$firstgen\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$full_name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$last\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$preferred\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryEmailAddress\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryPhoneNumber\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sap\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentCareTeam.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dfw\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dual\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$first\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$firstgen\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$full_name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$last\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$preferred\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryEmailAddress\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryPhoneNumber\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sap\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentSubscriptions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dfw\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dual\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$first\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$firstgen\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$full_name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$last\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$preferred\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryEmailAddress\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryPhoneNumber\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sap\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getLicenseType\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(AdvisingApp\\\\Task\\\\Models\\\\Task\\)\\: bool given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$model of method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable,Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:associate\\(\\) expects AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\|null, Illuminate\\\\Database\\\\Eloquent\\\\Model given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Ternary operator condition is always true\\.$#',
    'identifier' => 'ternary.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ManageStudentTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined static method class\\-string\\<Filament\\\\Resources\\\\RelationManagers\\\\RelationManager\\>\\|Filament\\\\Resources\\\\RelationManagers\\\\RelationManagerConfiguration\\:\\:canViewForRecord\\(\\)\\.$#',
    'identifier' => 'staticMethod.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/StudentCaseManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\StudentCaseManagement\\:\\:filterRelationManagers\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/StudentCaseManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\StudentCaseManagement\\:\\:filterRelationManagers\\(\\) has parameter \\$record with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/StudentCaseManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\StudentCaseManagement\\:\\:filterRelationManagers\\(\\) has parameter \\$relationManager with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/StudentCaseManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\StudentCaseManagement\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/StudentCaseManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Unsafe call to private method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\StudentCaseManagement\\:\\:managers\\(\\) through static\\:\\:\\.$#',
    'identifier' => 'staticClassAccess.privateMethod',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/StudentCaseManagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dfw\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dual\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$first\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$firstgen\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$full_name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$last\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$preferred\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryEmailAddress\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryPhoneNumber\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sap\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudent.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dfw\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$dual\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$first\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$firstgen\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$full_name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$last\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$preferred\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryEmailAddress\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$primaryPhoneNumber\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sap\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sisid\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:timeline\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\ViewStudentActivityFeed\\:\\:viewRecord\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\ViewStudentActivityFeed\\:\\:viewRecord\\(\\) has parameter \\$key with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\ViewStudentActivityFeed\\:\\:viewRecord\\(\\) has parameter \\$morphReference with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\ViewStudentActivityFeed\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\Pages\\\\ViewStudentActivityFeed\\:\\:\\$timelineRecords with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/Pages/ViewStudentActivityFeed.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sent_at\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$subject\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getBody\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>&literal\\-string$#',
    'identifier' => 'match.unhandled',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: string$#',
    'identifier' => 'match.unhandled',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$morphQuery of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type AdvisingApp\\\\Notification\\\\Enums\\\\EmailMessageEventType\\|AdvisingApp\\\\Notification\\\\Enums\\\\SmsMessageEventType\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method delete\\(\\) on an unknown class AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\RelationManagers\\\\Program\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/EnrollmentsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Comparison operation "\\>" between int\\<1, max\\> and 0 is always true\\.$#',
    'identifier' => 'greater.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/EnrollmentsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$record contains unknown class AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\RelationManagers\\\\Program\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/EnrollmentsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentResource\\\\RelationManagers\\\\EventsRelationManager\\:\\:getHeaderActions\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/EventsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function is_array\\(\\) with array\\<mixed\\> will always evaluate to true\\.$#',
    'identifier' => 'function.alreadyNarrowedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/ProgramsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Comparison operation "\\>" between int\\<1, max\\> and 0 is always true\\.$#',
    'identifier' => 'greater.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentResource/RelationManagers/ProgramsRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method hasLicense\\(\\) on an unknown class AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentTagResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\StudentTagResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentTagResource.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AdvisingApp\\\\StudentDataModel\\\\Filament\\\\Resources\\\\User\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Filament/Resources/StudentTagResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Http\\\\Controllers\\\\UpdateStudentInformationSystemSettingsController\\:\\:__invoke\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Http/Controllers/UpdateStudentInformationSystemSettingsController.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to protected property Spatie\\\\Invade\\\\Invader\\<Illuminate\\\\Filesystem\\\\AwsS3V3Adapter\\>\\:\\:\\$client\\.$#',
    'identifier' => 'property.protected',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Jobs/PrepareStudentDataCsvImport.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var with type array\\<array\\<array\\<string, string\\>\\>\\> is not subtype of native type Generator\\.$#',
    'identifier' => 'varTag.nativeType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Jobs/PrepareStudentDataCsvImport.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\StudentDataModel\\\\Jobs\\\\PrepareStudentDataCsvImport\\:\\:\\$timeout has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Jobs/PrepareStudentDataCsvImport.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\StudentDataModel\\\\Jobs\\\\PrepareStudentDataCsvImport\\:\\:\\$tries has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Jobs/PrepareStudentDataCsvImport.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\:\\:careTeam\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Contracts/Educatable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Scopes\\\\EducatableSearch\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Scopes/EducatableSearch.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Scopes\\\\EducatableSort\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Scopes/EducatableSort.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$join of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Scopes/EducatableSort.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Scopes\\\\LicensedToEducatable\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Scopes/LicensedToEducatable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:additionalAddresses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:additionalEmailAddresses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:additionalPhoneNumbers\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:alertHistories\\(\\) return type with generic class Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:displayName\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:fullAddress\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:orderedEngagementResponses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:orderedEngagements\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:primaryAddress\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:taskHistories\\(\\) return type with generic class Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\:\\:timeline\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @return with type array\\<string, string\\>\\|string\\|null is not subtype of native type string\\|null\\.$#',
    'identifier' => 'return.phpDocType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/Student.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\StudentDataModel\\\\Models\\\\StudentAddress\\:\\:full\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Models/StudentAddress.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\StudentDataModel\\\\Models\\\\IdeHelperStudentAddress\\:\\:\\$order \\(int\\) does not accept Illuminate\\\\Contracts\\\\Database\\\\Query\\\\Expression\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Observers/StudentAddressObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\StudentDataModel\\\\Models\\\\IdeHelperStudentEmailAddress\\:\\:\\$order \\(int\\) does not accept Illuminate\\\\Contracts\\\\Database\\\\Query\\\\Expression\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Observers/StudentEmailAddressObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\StudentDataModel\\\\Models\\\\IdeHelperStudentPhoneNumber\\:\\:\\$order \\(int\\) does not accept Illuminate\\\\Contracts\\\\Database\\\\Query\\\\Expression\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/student-data-model/src/Observers/StudentPhoneNumberObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int,AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField\\>\\:\\:map\\(\\) expects callable\\(AdvisingApp\\\\Form\\\\Models\\\\SubmissibleField, int\\)\\: array\\{type\\: \'tiptapBlock\', attrs\\: array\\{id\\: string, type\\: string, data\\: non\\-empty\\-array\\}\\}, Closure\\(AdvisingApp\\\\Survey\\\\Models\\\\SurveyField\\)\\: array\\{type\\: \'tiptapBlock\', attrs\\: array\\{id\\: string, type\\: string, data\\: non\\-empty\\-array\\}\\} given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/database/factories/SurveyFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Actions\\\\DuplicateSurvey\\:\\:replaceIdsInContent\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Actions/DuplicateSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Actions\\\\DuplicateSurvey\\:\\:replaceIdsInContent\\(\\) has parameter \\$content with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Actions/DuplicateSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Actions\\\\DuplicateSurvey\\:\\:replaceIdsInContent\\(\\) has parameter \\$fieldMap with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Actions/DuplicateSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Actions\\\\DuplicateSurvey\\:\\:replicateFields\\(\\) has parameter \\$stepMap with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Actions/DuplicateSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Actions\\\\DuplicateSurvey\\:\\:replicateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Actions/DuplicateSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Actions\\\\DuplicateSurvey\\:\\:replicateSteps\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Actions/DuplicateSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Actions\\\\DuplicateSurvey\\:\\:updateStepContent\\(\\) has parameter \\$fieldMap with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Actions/DuplicateSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Blocks\\\\LikertScaleSurveyBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Blocks/LikertScaleSurveyBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Blocks\\\\LikertScaleSurveyBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Blocks/LikertScaleSurveyBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Blocks\\\\LikertScaleSurveyBlock\\:\\:options\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Blocks/LikertScaleSurveyBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Blocks\\\\RatingScaleSurveyFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Blocks/RatingScaleSurveyFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Blocks\\\\RatingScaleSurveyFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Blocks/RatingScaleSurveyFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Blocks\\\\SliderSurveyFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Blocks/SliderSurveyFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Blocks\\\\SliderSurveyFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Blocks/SliderSurveyFieldBlock.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable \\$block in PHPDoc tag @var does not exist\\.$#',
    'identifier' => 'varTag.variableNotFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Blocks/SurveyFieldBlockRegistry.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Resources\\\\SurveyResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Resources\\\\SurveyResource\\\\Pages\\\\CreateSurvey\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/CreateSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Resources\\\\SurveyResource\\\\Pages\\\\CreateSurvey\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/CreateSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Resources\\\\SurveyResource\\\\Pages\\\\CreateSurvey\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/CreateSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$survey of method AdvisingApp\\\\Survey\\\\Filament\\\\Resources\\\\SurveyResource\\\\Pages\\\\CreateSurvey\\:\\:saveFieldsFromComponents\\(\\) expects AdvisingApp\\\\Survey\\\\Models\\\\Survey, AdvisingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/CreateSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Resources\\\\SurveyResource\\\\Pages\\\\EditSurvey\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/EditSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Resources\\\\SurveyResource\\\\Pages\\\\EditSurvey\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/EditSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Filament\\\\Resources\\\\SurveyResource\\\\Pages\\\\EditSurvey\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/EditSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$survey of method AdvisingApp\\\\Survey\\\\Filament\\\\Resources\\\\SurveyResource\\\\Pages\\\\EditSurvey\\:\\:saveFieldsFromComponents\\(\\) expects AdvisingApp\\\\Survey\\\\Models\\\\Survey, AdvisingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/EditSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/ListSurveys.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$is_authenticated\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/ManageSurveySubmissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/ManageSurveySubmissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$submissions\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/ManageSurveySubmissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$records of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Filament/Resources/SurveyResource/Pages/ManageSurveySubmissions.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:check\\(\\) expects string, int given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Http/Controllers/SurveyWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int\\<100000, 999999\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Http/Controllers/SurveyWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Survey\\\\Models\\\\IdeHelperSurveySubmission\\:\\:\\$submitted_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Http/Controllers/SurveyWidgetController.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Survey\\\\Livewire\\\\RenderSurvey\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Livewire/RenderSurvey.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:notSubmitted\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Models\\\\SurveySubmission\\:\\:scopeCanceled\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Models\\\\SurveySubmission\\:\\:scopeCanceled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Models\\\\SurveySubmission\\:\\:scopeNotCanceled\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Models\\\\SurveySubmission\\:\\:scopeNotCanceled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Models\\\\SurveySubmission\\:\\:scopeNotSubmitted\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Models\\\\SurveySubmission\\:\\:scopeNotSubmitted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Models\\\\SurveySubmission\\:\\:scopeRequested\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Models\\\\SurveySubmission\\:\\:scopeRequested\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Models\\\\SurveySubmission\\:\\:scopeSubmitted\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Models\\\\SurveySubmission\\:\\:scopeSubmitted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$submission of method AdvisingApp\\\\Form\\\\Enums\\\\FormSubmissionRequestDeliveryMethod\\:\\:deliver\\(\\) expects AdvisingApp\\\\Form\\\\Models\\\\FormSubmission, \\$this\\(AdvisingApp\\\\Survey\\\\Models\\\\SurveySubmission\\) given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Models/SurveySubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Survey\\\\Providers\\\\SurveyServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/survey/src/Providers/SurveyServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/task/database/factories/TaskFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Task\\\\Histories\\\\TaskHistory\\:\\:\\$formatted\\.$#',
    'identifier' => 'property.notFound',
    'count' => 14,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Actions/TaskHistoryCreatedViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Task\\\\Histories\\\\TaskHistory\\:\\:\\$formatted\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Actions/TaskHistoryUpdatedViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Filament\\\\Pages\\\\Components\\\\TaskKanbanViewAction\\:\\:taskInfoList\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Ternary operator condition is always true\\.$#',
    'identifier' => 'ternary.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Auth\\\\AuthManager\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\|AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getLicenseType\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/task/src/Filament/RelationManagers/BaseTaskRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(AdvisingApp\\\\Task\\\\Models\\\\Task\\)\\: bool given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/RelationManagers/BaseTaskRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$model of method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable,Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:associate\\(\\) expects AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\|null, Illuminate\\\\Database\\\\Eloquent\\\\Model given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/RelationManagers/BaseTaskRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Ternary operator condition is always true\\.$#',
    'identifier' => 'ternary.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/RelationManagers/BaseTaskRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Filament\\\\Resources\\\\TaskResource\\\\Components\\\\TaskViewAction\\:\\:taskInfoList\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Ternary operator condition is always true\\.$#',
    'identifier' => 'ternary.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Auth\\\\AuthManager\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\|AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: true$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/CreateTask.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: true$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/EditTask.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Filament\\\\Resources\\\\TaskResource\\\\Pages\\\\EditTask\\:\\:editFormFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/EditTask.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$join of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/ListTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Ternary operator condition is always true\\.$#',
    'identifier' => 'ternary.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/ListTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access on non\\-nullable type AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\|AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/ListTasks.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:taskHistories\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Histories/TaskHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Histories\\\\TaskHistory\\:\\:getFormattedValueForKey\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Histories/TaskHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Histories\\\\TaskHistory\\:\\:getFormattedValues\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Histories/TaskHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Histories\\\\TaskHistory\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Histories/TaskHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: string$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Imports/TaskImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe property access "\\?\\-\\>email" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Imports/TaskImporter.php',
];
$ignoreErrors[] = [
    'message' => '#^Generic type Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\> in PHPDoc tag @return does not specify all template types of class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'generics.lessTypes',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Instanceof between AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\|AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student and AdvisingApp\\\\Notification\\\\Models\\\\Contracts\\\\Subscribable will always evaluate to true\\.$#',
    'identifier' => 'instanceof.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:concern\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\> but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<Illuminate\\\\Database\\\\Eloquent\\\\Model, \\$this\\(AdvisingApp\\\\Task\\\\Models\\\\Task\\)\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:getStateMachineFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:processCustomHistories\\(\\) has parameter \\$new with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:processCustomHistories\\(\\) has parameter \\$old with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:processCustomHistories\\(\\) has parameter \\$pending with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:processHistory\\(\\) has parameter \\$new with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:processHistory\\(\\) has parameter \\$old with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:recordHistory\\(\\) has parameter \\$new with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:recordHistory\\(\\) has parameter \\$old with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:recordHistory\\(\\) has parameter \\$pending with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:scopeByNextDue\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:scopeOpen\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Task\\\\Models\\\\Task\\:\\:\\$ignoredAttributes type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Type AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable in generic type Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AdvisingApp\\\\StudentDataModel\\\\Models\\\\Contracts\\\\Educatable\\> in PHPDoc tag @return is not subtype of template type TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model of class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\.$#',
    'identifier' => 'generics.notSubtype',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
    'message' => '#^Instanceof between AdvisingApp\\\\Prospect\\\\Models\\\\Prospect and AdvisingApp\\\\Prospect\\\\Models\\\\Prospect will always evaluate to true\\.$#',
    'identifier' => 'instanceof.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Notifications/TaskAssignedToUserNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Task\\\\Notifications\\\\TaskAssignedToUserNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Notifications/TaskAssignedToUserNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$concern\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/task/src/Observers/TaskHistoryObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\|AdvisingApp\\\\StudentDataModel\\\\Models\\\\Student\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/task/src/Policies/TaskPolicy.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/task/tests/Tenant/TaskAssignmentTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<int\\<0, max\\>\\|null\\>\\:\\:and\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/task/tests/Tenant/TaskAssignmentTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:catchPhrase\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/team/database/factories/TeamFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$value of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/team/src/Filament/Resources/TeamResource/RelationManagers/UsersRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Team\\\\Providers\\\\TeamServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/team/src/Providers/TeamServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Theme\\\\DataTransferObjects\\\\ThemeConfig\\:\\:__construct\\(\\) has parameter \\$colorOverrides with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/theme/src/DataTransferObjects/ThemeConfig.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to property \\$applicationName on an unknown class AdvisingApp\\\\Theme\\\\Filament\\\\Pages\\\\TenantConfig\\.$#',
    'identifier' => 'class.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/theme/src/Filament/Pages/ManageBrandConfigurationSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var for variable \\$config contains unknown class AdvisingApp\\\\Theme\\\\Filament\\\\Pages\\\\TenantConfig\\.$#',
    'identifier' => 'class.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/theme/src/Filament/Pages/ManageBrandConfigurationSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$length of method Filament\\\\Forms\\\\Components\\\\TextInput\\:\\:maxLength\\(\\) expects Closure\\|int\\|null, \'255\' given\\.$#',
    'identifier' => 'argument.type',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/theme/src/Filament/Pages/ManageBrandConfigurationSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Theme\\\\Filament\\\\Pages\\\\ManageCollegeBrandingSettings\\:\\:getFormActions\\(\\) has invalid return type AdvisingApp\\\\Theme\\\\Filament\\\\Pages\\\\Action\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/theme/src/Filament/Pages/ManageCollegeBrandingSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Theme\\\\Filament\\\\Pages\\\\ManageCollegeBrandingSettings\\:\\:getFormActions\\(\\) has invalid return type AdvisingApp\\\\Theme\\\\Filament\\\\Pages\\\\ActionGroup\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/theme/src/Filament/Pages/ManageCollegeBrandingSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Theme\\\\Filament\\\\Pages\\\\ManageCollegeBrandingSettings\\:\\:getFormActions\\(\\) should return array\\<AdvisingApp\\\\Theme\\\\Filament\\\\Pages\\\\Action\\|AdvisingApp\\\\Theme\\\\Filament\\\\Pages\\\\ActionGroup\\> but returns array\\<Filament\\\\Actions\\\\Action\\|Filament\\\\Actions\\\\ActionGroup\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/theme/src/Filament/Pages/ManageCollegeBrandingSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Theme\\\\Settings\\\\ThemeSettings\\:\\:\\$tenant_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/theme/src/Http/Controllers/BrandedWebsiteLinksController.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Theme\\\\Http\\\\Requests\\\\BrandedWebsiteLinksRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/theme/src/Http/Requests/BrandedWebsiteLinksRequest.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Theme\\\\Http\\\\Requests\\\\UpdateThemeRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/theme/src/Http/Requests/UpdateThemeRequest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Spatie\\\\Image\\\\Drivers\\\\ImageDriver\\:\\:keepOriginalImageFormat\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/theme/src/Settings/SettingsProperties/ThemeSettingsProperty.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Theme\\\\Settings\\\\ThemeSettings\\:\\:\\$color_overrides type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/theme/src/Settings/ThemeSettings.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/theme/tests/Tenant/Feature/BrandedWebsiteLinksTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Actions\\\\AggregatesTimelineRecordsForModel\\:\\:handle\\(\\) has parameter \\$modelsToTimeline with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Actions\\\\AggregatesTimelineRecordsForModel\\:\\:handle\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Timeline\\\\Actions\\\\AggregatesTimelineRecordsForModel\\:\\:\\$aggregateRecords with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Actions\\\\SyncTimelineData\\:\\:now\\(\\) has parameter \\$modelsToTimeline with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Actions/SyncTimelineData.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/timeline/src/Actions/SyncTimelineData.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:timeline\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:mount\\(\\) has parameter \\$record with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:viewRecord\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:viewRecord\\(\\) has parameter \\$key with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:viewRecord\\(\\) has parameter \\$morphReference with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AdvisingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:\\$timelineRecords with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:timeline\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Listeners/AddRecordToTimeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Models\\\\Contracts\\\\HasHistory\\:\\:histories\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Models/Contracts/HasHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Models\\\\Contracts\\\\ProvidesATimeline\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Models/Contracts/ProvidesATimeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Class AdvisingApp\\\\Timeline\\\\Models\\\\History has PHPDoc tag @property for property \\$new with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Models/History.php',
];
$ignoreErrors[] = [
    'message' => '#^Class AdvisingApp\\\\Timeline\\\\Models\\\\History has PHPDoc tag @property for property \\$old with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Models/History.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Models\\\\History\\:\\:formatted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Models/History.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Models\\\\History\\:\\:getFormattedValueForKey\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Models/History.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Models\\\\History\\:\\:getFormattedValues\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Models/History.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Models\\\\Timeline\\:\\:scopeForEntity\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Models/Timeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Models\\\\Timeline\\:\\:scopeForEntity\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Models/Timeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method AdvisingApp\\\\Timeline\\\\Models\\\\Contracts\\\\HasHistory\\:\\:getAttributes\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Observers/HistorySubjectObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method AdvisingApp\\\\Timeline\\\\Models\\\\Contracts\\\\HasHistory\\:\\:getChanges\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/timeline/src/Observers/HistorySubjectObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method AdvisingApp\\\\Timeline\\\\Models\\\\Contracts\\\\HasHistory\\:\\:getOriginal\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Observers/HistorySubjectObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method AdvisingApp\\\\Timeline\\\\Models\\\\Contracts\\\\HasHistory\\:\\:processHistory\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/timeline/src/Observers/HistorySubjectObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/timeline/src/Observers/HistorySubjectObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/timeline/src/Observers/HistorySubjectObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Timeline\\\\Providers\\\\TimelineServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Providers/TimelineServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Alert\\\\Histories\\\\AlertHistory\\:\\:\\$created_at\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Timelines/AlertHistoryTimeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Alert\\\\Histories\\\\AlertHistory\\:\\:\\$event\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/timeline/src/Timelines/AlertHistoryTimeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: mixed$#',
    'identifier' => 'match.unhandled',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/timeline/src/Timelines/AlertHistoryTimeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Task\\\\Histories\\\\TaskHistory\\:\\:\\$created_at\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Timelines/TaskHistoryTimeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\Task\\\\Histories\\\\TaskHistory\\:\\:\\$event\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/timeline/src/Timelines/TaskHistoryTimeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: mixed$#',
    'identifier' => 'match.unhandled',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/timeline/src/Timelines/TaskHistoryTimeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:engagementResponses\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/tests/Tenant/Listeners/AddRecordToTimelineTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:engagementResponses\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/tests/Tenant/Listeners/RemoveRecordFromTimelineTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AdvisingApp\\\\Webhook\\\\Actions\\\\StoreInboundWebhook\\:\\:handle\\(\\) has parameter \\$payload with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/webhook/src/Actions/StoreInboundWebhook.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function is_array\\(\\) with string will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/webhook/src/Http/Middleware/HandleAwsSnsRequest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$service of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/ChangeAppKey.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$app of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/ChangeAppKey.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Container\\\\Container\\:\\:getNamespace\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/Finders/ApplicationModels.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Actions\\\\Finders\\\\ApplicationModels\\:\\:all\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/Finders/ApplicationModels.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$class of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/Finders/ApplicationModels.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$item of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/Finders/ApplicationModels.php',
];
$ignoreErrors[] = [
    'message' => '#^Class App\\\\Casts\\\\Encrypted implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Casts/Encrypted.php',
];
$ignoreErrors[] = [
    'message' => '#^Class App\\\\Casts\\\\LandlordEncrypted implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Casts/LandlordEncrypted.php',
];
$ignoreErrors[] = [
    'message' => '#^Class App\\\\Casts\\\\TenantEncrypted implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Casts/TenantEncrypted.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\DataCast\\:\\:get\\(\\) has parameter \\$payload with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/DataTransferObjects/Casts/DataCast.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\DataCast\\:\\:set\\(\\) has parameter \\$payload with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/DataTransferObjects/Casts/DataCast.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\DataCast\\:\\:set\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/DataTransferObjects/Casts/DataCast.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\MoneySettingCast\\:\\:get\\(\\) has parameter \\$payload with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/DataTransferObjects/Casts/MoneySettingCast.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\MoneySettingCast\\:\\:set\\(\\) has parameter \\$payload with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/DataTransferObjects/Casts/MoneySettingCast.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\MoneySettingCast\\:\\:set\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/DataTransferObjects/Casts/MoneySettingCast.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Exceptions\\\\IntegrationException\\:\\:make\\(\\) should return static\\(App\\\\Exceptions\\\\IntegrationException\\) but returns App\\\\Exceptions\\\\IntegrationNotConfigured\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app/Exceptions/IntegrationException.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Exceptions\\\\IntegrationException\\:\\:make\\(\\) should return static\\(App\\\\Exceptions\\\\IntegrationException\\) but returns App\\\\Exceptions\\\\IntegrationNotEnabled\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app/Exceptions/IntegrationException.php',
];
$ignoreErrors[] = [
    'message' => '#^Property App\\\\Exceptions\\\\IntegrationException\\:\\:\\$message has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app/Exceptions/IntegrationException.php',
];
$ignoreErrors[] = [
    'message' => '#^Property App\\\\Exceptions\\\\IntegrationNotConfigured\\:\\:\\$message has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app/Exceptions/IntegrationNotConfigured.php',
];
$ignoreErrors[] = [
    'message' => '#^Property App\\\\Exceptions\\\\IntegrationNotEnabled\\:\\:\\$message has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app/Exceptions/IntegrationNotEnabled.php',
];
$ignoreErrors[] = [
    'message' => '#^Property App\\\\Exceptions\\\\SoftDeleteContraintViolationException\\:\\:\\$message has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app/Exceptions/SoftDeleteContraintViolationException.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeFill\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeFill\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeSave\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeSave\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
    'message' => '#^Property App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Filament\\\\Pages\\\\ProductHealth\\:\\:getNavigationBadge\\(\\) should return string\\|null but returns int\\<1, max\\>\\|null\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Pages/ProductHealth.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$targetDay of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Pages/ProfilePage.php',
];
$ignoreErrors[] = [
    'message' => '#^Negated boolean expression is always true\\.$#',
    'identifier' => 'booleanNot.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignLicensesBulkAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignLicensesBulkAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$value of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignLicensesBulkAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignRolesBulkAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$record of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignTeamBulkAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Resources/UserResource/Pages/CreateUser.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Resources/UserResource/Pages/EditUser.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 6,
    'path' => __DIR__ . '/app/Filament/Resources/UserResource/Pages/ListUsers.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AdvisingApp\\\\CaseManagement\\\\Models\\\\CaseModel\\:\\:\\$case_model_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app/Filament/Widgets/MyCases.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\|Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation\\:\\:unread\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getNotifications\\(\\) return type with generic interface Illuminate\\\\Contracts\\\\Pagination\\\\Paginator does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation does not specify its types\\: TRelatedModel, TDeclaringModel, TResult$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getUnreadNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getUnreadNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation does not specify its types\\: TRelatedModel, TDeclaringModel, TResult$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Http\\\\Controllers\\\\ViewPublicUserProfileController\\:\\:formatHours\\(\\) has parameter \\$hours with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Controllers/ViewPublicUserProfileController.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Http\\\\Controllers\\\\ViewPublicUserProfileController\\:\\:formatHours\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Http/Controllers/ViewPublicUserProfileController.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Livewire\\\\ProspectPipelineKanban\\:\\:getPipelineSubjects\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/ProspectPipelineKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Livewire\\\\ProspectPipelineKanban\\:\\:getStages\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/ProspectPipelineKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Livewire\\\\ProspectPipelineKanban\\:\\:moveProspect\\(\\) has parameter \\$fromStage with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/ProspectPipelineKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Livewire\\\\ProspectPipelineKanban\\:\\:moveProspect\\(\\) has parameter \\$toStage with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/ProspectPipelineKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Livewire\\\\ProspectPipelineKanban\\:\\:render\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/ProspectPipelineKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$groupBy of method Illuminate\\\\Support\\\\Collection\\<int,Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:groupBy\\(\\) expects \\(callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\)\\: \\(int\\|string\\)\\)\\|list\\<Closure\\|string\\>\\|string, Closure\\(AdvisingApp\\\\Prospect\\\\Models\\\\Prospect\\)\\: \\(string\\|null\\) given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/ProspectPipelineKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$relations of method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:with\\(\\) expects array\\<array\\|\\(Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation\\<\\*, \\*, \\*\\>\\)\\: mixed\\)\\|string\\>\\|string, array\\{educatablePipelineStages\\: Closure\\(AdvisingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\)\\: AdvisingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\} given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/ProspectPipelineKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TGroupKey in call to method Illuminate\\\\Support\\\\Collection\\<int,Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:groupBy\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/ProspectPipelineKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Property App\\\\Livewire\\\\RenderForm\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/RenderForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getStateMachine\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Match expression does not handle remaining value\\: true$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:editFormFields\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:getTasks\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\Authenticatable\\:\\:canOrElse\\(\\) has parameter \\$abilities with no value type specified in iterable type iterable\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Authenticatable.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\Authenticatable\\:\\:roles\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Authenticatable.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$q of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Authenticatable.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TRelatedModel in call to method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:morphToMany\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Authenticatable.php',
];
$ignoreErrors[] = [
    'message' => '#^Class App\\\\Models\\\\BaseModel uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/BaseModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\Scopes\\\\ExcludeConvertedProspects\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Scopes/ExcludeConvertedProspects.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\Scopes\\\\HasLicense\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Scopes/HasLicense.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\Scopes\\\\HasLicense\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation but does not specify its types\\: TRelatedModel, TDeclaringModel, TResult$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Scopes/HasLicense.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\Scopes\\\\SearchBy\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Scopes/SearchBy.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\Scopes\\\\SetupIsComplete\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Scopes/SetupIsComplete.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:role\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Scopes/WithoutSuperAdmin.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\Scopes\\\\WithoutSuperAdmin\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Scopes/WithoutSuperAdmin.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:allowedOperators\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:confirmMultifactorAuthentication\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:disableMultifactorAuthentication\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:enableMultifactorAuthentication\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:generateRecoveryCodes\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:getMultifactorQrCodeUrl\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:multifactorRecoveryCodes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:orderableColumns\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:processGlobalSearch\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:processGlobalSearch\\(\\) has parameter \\$data with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:processQuery\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:processQuery\\(\\) has parameter \\$data with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:processQuery\\(\\) has parameter \\$query with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:reGenerateRecoveryCodes\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:scopeAdvancedFilter\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:scopeAdvancedFilter\\(\\) has parameter \\$data with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:scopeAdvancedFilter\\(\\) has parameter \\$query with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Models\\\\User\\:\\:whiteListColumns\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @return contains generic type AdvisingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\<AdvisingApp\\\\Consent\\\\Models\\\\ConsentAgreement, \\$this\\(App\\\\Models\\\\User\\)\\> but class AdvisingApp\\\\Audit\\\\Overrides\\\\BelongsToMany is not generic\\.$#',
    'identifier' => 'generics.notGeneric',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$column of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Strict comparison using \\!\\=\\= between \\*NEVER\\* and string will always evaluate to true\\.$#',
    'identifier' => 'notIdentical.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Ternary operator condition is always false\\.$#',
    'identifier' => 'ternary.alwaysFalse',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
    'message' => '#^Property App\\\\Multitenancy\\\\Exceptions\\\\TenantAppKeyIsNull\\:\\:\\$message has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app/Multitenancy/Exceptions/TenantAppKeyIsNull.php',
];
$ignoreErrors[] = [
    'message' => '#^Property App\\\\Multitenancy\\\\Exceptions\\\\UnableToResolveTenantForEncryptionKey\\:\\:\\$message has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app/Multitenancy/Exceptions/UnableToResolveTenantForEncryptionKey.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Overrides\\\\LaravelSqsExtended\\\\SqsDiskQueue\\:\\:pop\\(\\) should return Illuminate\\\\Contracts\\\\Queue\\\\Job\\|null but return statement is missing\\.$#',
    'identifier' => 'return.missing',
    'count' => 1,
    'path' => __DIR__ . '/app/Overrides/LaravelSqsExtended/SqsDiskQueue.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Overrides\\\\LaravelSqsExtended\\\\SqsDiskQueue\\:\\:pushRaw\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app/Overrides/LaravelSqsExtended/SqsDiskQueue.php',
];
$ignoreErrors[] = [
    'message' => '#^Negated boolean expression is always false\\.$#',
    'identifier' => 'booleanNot.alwaysFalse',
    'count' => 1,
    'path' => __DIR__ . '/app/Overrides/LaravelSqsExtended/SqsDiskQueue.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$app of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 4,
    'path' => __DIR__ . '/app/Providers/AppServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var with type Filament\\\\Forms\\\\Components\\\\Checkbox is not subtype of native type \\$this\\(App\\\\Providers\\\\FilamentServiceProvider\\)\\.$#',
    'identifier' => 'varTag.nativeType',
    'count' => 1,
    'path' => __DIR__ . '/app/Providers/FilamentServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var with type Filament\\\\Forms\\\\Components\\\\Toggle is not subtype of native type \\$this\\(App\\\\Providers\\\\FilamentServiceProvider\\)\\.$#',
    'identifier' => 'varTag.nativeType',
    'count' => 1,
    'path' => __DIR__ . '/app/Providers/FilamentServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$state of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 3,
    'path' => __DIR__ . '/app/Providers/FilamentServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$modelClass of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Providers/MorphServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$connection of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Providers/MultiConnectionParallelTestingServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$database of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 3,
    'path' => __DIR__ . '/app/Providers/MultiConnectionParallelTestingServiceProvider.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:apply\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:apply\\(\\) has parameter \\$data with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:apply\\(\\) has parameter \\$query with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:contains\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:contains\\(\\) has parameter \\$filter with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:contains\\(\\) has parameter \\$query with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:isNestedColumn\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:isNestedColumn\\(\\) has parameter \\$column with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeFilter\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeFilter\\(\\) has parameter \\$filter with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeFilter\\(\\) has parameter \\$query with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeOrder\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeOrder\\(\\) has parameter \\$data with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeOrder\\(\\) has parameter \\$query with no type specified\\.$#',
    'identifier' => 'missingType.parameter',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$q of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Property App\\\\Support\\\\FilterQueryBuilder\\:\\:\\$model has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Property App\\\\Support\\\\FilterQueryBuilder\\:\\:\\$table has no type specified\\.$#',
    'identifier' => 'missingType.property',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$email of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/database/seeders/LocalDevelopmentSeeder.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$user of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/routes/channels.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$id of anonymous function has no typehint\\.$#',
    'identifier' => 'closure.parameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/routes/channels.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\|Pest\\\\Support\\\\HigherOrderTapProxy\\:\\:actingAs\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/tests/Helpers.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
