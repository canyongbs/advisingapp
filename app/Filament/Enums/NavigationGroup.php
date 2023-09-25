<?php

namespace App\Filament\Enums;

use App\Filament\Pages\Team;
use App\Filament\Pages\MessageCenter;
use App\Filament\Pages\ProductHealth;
use App\Filament\Pages\ProactiveAlerts;
use App\Filament\Resources\UserResource;
use Filament\Support\Contracts\HasLabel;
use App\Filament\Pages\FilesAndDocuments;
use Assist\Task\Filament\Resources\TaskResource;
use Assist\Audit\Filament\Resources\AuditResource;
use Assist\Audit\Filament\Pages\ManageAuditSettings;
use Assist\Assistant\Filament\Pages\PersonalAssistant;
use Assist\CaseloadManagement\Filament\Pages\Campaign;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Authorization\Filament\Resources\RoleResource;
use Assist\Webhook\Filament\Resources\InboundWebhookResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Authorization\Filament\Resources\RoleGroupResource;
use Assist\Interaction\Filament\Resources\InteractionResource;
use Assist\Prospect\Filament\Resources\ProspectSourceResource;
use Assist\Prospect\Filament\Resources\ProspectStatusResource;
use Assist\Authorization\Filament\Resources\PermissionResource;
use Assist\Theme\Filament\Pages\ManageBrandConfigurationSettings;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;
use Assist\Interaction\Filament\Resources\InteractionTypeResource;
use Filament\Navigation\NavigationGroup as FilamentNavigationGroup;
use Assist\Interaction\Filament\Resources\InteractionDriverResource;
use Assist\Interaction\Filament\Resources\InteractionStatusResource;
use Assist\Interaction\Filament\Resources\InteractionOutcomeResource;
use Assist\Interaction\Filament\Resources\InteractionCampaignResource;
use Assist\Interaction\Filament\Resources\InteractionRelationResource;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource;
use Assist\Interaction\Filament\Resources\InteractionInstitutionResource;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource;
use Assist\IntegrationGoogleAnalytics\Filament\Pages\ManageGoogleAnalyticsSettings;
use Assist\IntegrationMicrosoftClarity\Filament\Pages\ManageMicrosoftClaritySettings;

enum NavigationGroup implements HasLabel
{
    case RecordManagement;
    case ProductivityTools;
    case MassEngagement;
    case UsersAndPermissions;
    case ProductAdministration;
    case UsageAnalyticsIntegrations;
    case ProductSettings;

    public function getLabel(): ?string
    {
        return match ($this) {
            NavigationGroup::UsersAndPermissions => 'Users and Permissions',
            default => str($this->name)->headline()->toString(),
        };
    }

    public static function getNavigationGroup(string $class): ?NavigationGroup
    {
        return match ($class) {
            StudentResource::class, ProspectResource::class => NavigationGroup::RecordManagement,
            PersonalAssistant::class, MessageCenter::class, ServiceRequestResource::class, ProactiveAlerts::class, InteractionResource::class, FilesAndDocuments::class, KnowledgeBaseItemResource::class, TaskResource::class => NavigationGroup::ProductivityTools,
            CaseloadResource::class, Campaign::class => NavigationGroup::MassEngagement,
            UserResource::class, RoleGroupResource::class, RoleResource::class, PermissionResource::class, Team::class => NavigationGroup::UsersAndPermissions,
            ManageAuditSettings::class, ManageBrandConfigurationSettings::class, AuditResource::class, InboundWebhookResource::class, ProductHealth::class => NavigationGroup::ProductAdministration,
            ManageGoogleAnalyticsSettings::class, ManageMicrosoftClaritySettings::class => NavigationGroup::UsageAnalyticsIntegrations,
            ProspectStatusResource::class, ProspectSourceResource::class, ServiceRequestPriorityResource::class, ServiceRequestStatusResource::class, ServiceRequestTypeResource::class, KnowledgeBaseCategoryResource::class, KnowledgeBaseQualityResource::class, KnowledgeBaseStatusResource::class, InteractionCampaignResource::class, InteractionDriverResource::class, InteractionInstitutionResource::class, InteractionOutcomeResource::class, InteractionRelationResource::class, InteractionStatusResource::class, InteractionTypeResource::class => NavigationGroup::ProductSettings,
        };
    }

    public static function getNavigationSort(string $class): ?int
    {
        return match (NavigationGroup::getNavigationGroup($class)) {
            NavigationGroup::RecordManagement => array_search($class, [
                StudentResource::class,
                ProspectResource::class,
            ]),
            NavigationGroup::ProductivityTools => array_search($class, [
                PersonalAssistant::class,
                MessageCenter::class,
                ServiceRequestResource::class,
                ProactiveAlerts::class,
                InteractionResource::class,
                FilesAndDocuments::class,
                KnowledgeBaseItemResource::class,
                TaskResource::class,
            ]),
            NavigationGroup::MassEngagement => array_search($class, [
                CaseloadResource::class,
                Campaign::class,
            ]),
            NavigationGroup::UsersAndPermissions => array_search($class, [
                UserResource::class,
                RoleGroupResource::class,
                RoleResource::class,
                PermissionResource::class,
                Team::class,
            ]),
            NavigationGroup::ProductAdministration => array_search($class, [
                ManageAuditSettings::class,
                ManageBrandConfigurationSettings::class,
                AuditResource::class,
                InboundWebhookResource::class,
                ProductHealth::class,
            ]),
            NavigationGroup::UsageAnalyticsIntegrations => array_search($class, [
                ManageGoogleAnalyticsSettings::class,
                ManageMicrosoftClaritySettings::class,
            ]),
            NavigationGroup::ProductSettings => array_search($class, [
                ProspectStatusResource::class,
                ProspectSourceResource::class,
                ServiceRequestPriorityResource::class,
                ServiceRequestStatusResource::class,
                ServiceRequestTypeResource::class,
                KnowledgeBaseCategoryResource::class,
                KnowledgeBaseQualityResource::class,
                KnowledgeBaseStatusResource::class,
                InteractionCampaignResource::class,
                InteractionDriverResource::class,
                InteractionInstitutionResource::class,
                InteractionOutcomeResource::class,
                InteractionRelationResource::class,
                InteractionStatusResource::class,
                InteractionTypeResource::class,
            ]),
        };
    }

    public static function groups(): array
    {
        return [
            FilamentNavigationGroup::make()
                ->label(NavigationGroup::RecordManagement->getLabel()),
            FilamentNavigationGroup::make()
                ->label(NavigationGroup::ProductivityTools->getLabel()),
            FilamentNavigationGroup::make()
                ->label(NavigationGroup::MassEngagement->getLabel()),
            FilamentNavigationGroup::make()
                ->label(NavigationGroup::UsersAndPermissions->getLabel()),
            FilamentNavigationGroup::make()
                ->label(NavigationGroup::ProductAdministration->getLabel()),
            FilamentNavigationGroup::make()
                ->label(NavigationGroup::UsageAnalyticsIntegrations->getLabel()),
            FilamentNavigationGroup::make()
                ->label(NavigationGroup::ProductSettings->getLabel())
                ->collapsed(),
        ];
    }
}
