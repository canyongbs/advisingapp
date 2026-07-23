<?php

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiAssistantConfidentialDepartment;
use AdvisingApp\Ai\Models\AiAssistantConfidentialUser;
use AdvisingApp\Ai\Models\AiAssistantFile;
use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Models\AiAssistantUpvote;
use AdvisingApp\Ai\Models\AiAssistantUse;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiThreadFolder;
use AdvisingApp\Ai\Models\ConfidentialPromptUser;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorCategory;
use AdvisingApp\Ai\Models\CustomerAdvisorFile;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use AdvisingApp\Ai\Models\CustomerAdvisorMessage;
use AdvisingApp\Ai\Models\CustomerAdvisorQuestion;
use AdvisingApp\Ai\Models\CustomerAdvisorThread;
use AdvisingApp\Ai\Models\DataAdvisor;
use AdvisingApp\Ai\Models\DepartmentConfidentialPrompt;
use AdvisingApp\Ai\Models\LegacyAiMessageLog;
use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Ai\Models\PromptType;
use AdvisingApp\Ai\Models\PromptUpvote;
use AdvisingApp\Ai\Models\PromptUse;
use AdvisingApp\Alert\Models\AlertConfiguration;
use AdvisingApp\Alert\Models\StudentAlert;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationAuthentication;
use AdvisingApp\Application\Models\ApplicationField;
use AdvisingApp\Application\Models\ApplicationFieldSubmission;
use AdvisingApp\Application\Models\ApplicationStep;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionsChecklistItem;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Audit\Models\Audit;
use AdvisingApp\Authorization\Models\License;
use AdvisingApp\Authorization\Models\OtpLoginCode;
use AdvisingApp\Authorization\Models\Permission;
use AdvisingApp\Authorization\Models\PermissionGroup;
use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\BasicNeeds\Models\BasicNeedsCategory;
use AdvisingApp\BasicNeeds\Models\BasicNeedsProgram;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Campaign\Models\CampaignActionEducatableRelated;
use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Concern\Models\Concern;
use AdvisingApp\Concern\Models\ConcernStatus;
use AdvisingApp\Consent\Models\ConsentAgreement;
use AdvisingApp\Consent\Models\UserConsentAgreement;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Engagement\Models\EngagementFile;
use AdvisingApp\Engagement\Models\EngagementFileEntities;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Models\HolisticEngagement;
use AdvisingApp\Engagement\Models\SmsTemplate;
use AdvisingApp\Engagement\Models\UnmatchedInboundCommunication;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormAuthentication;
use AdvisingApp\Form\Models\FormEmailAutoReply;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Form\Models\FormFieldSubmission;
use AdvisingApp\Form\Models\FormStep;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Form\Models\Submissible;
use AdvisingApp\Form\Models\SubmissibleAuthentication;
use AdvisingApp\Form\Models\SubmissibleField;
use AdvisingApp\Form\Models\SubmissibleStep;
use AdvisingApp\Form\Models\Submission;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Group\Models\GroupSubject;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiResearchRequestVectorStore;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use AdvisingApp\Interaction\Models\DepartmentConfidentialInteraction;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionConfidentialUser;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroupAppointment;
use AdvisingApp\MeetingCenter\Models\BookingGroupDepartment;
use AdvisingApp\MeetingCenter\Models\BookingGroupUser;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormAuthentication;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormFieldSubmission;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;
use AdvisingApp\MeetingCenter\Models\PersonalBookingPage;
use AdvisingApp\Notification\Models\DatabaseMessage;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Models\EmailMessageEvent;
use AdvisingApp\Notification\Models\SmsMessage;
use AdvisingApp\Notification\Models\SmsMessageEvent;
use AdvisingApp\Notification\Models\StoredAnonymousNotifiable;
use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\Portal\Models\PortalAuthentication;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectAddress;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use AdvisingApp\Report\Models\TrackedEvent;
use AdvisingApp\Report\Models\TrackedEventCount;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestFolder;
use AdvisingApp\Research\Models\ResearchRequestParsedFile;
use AdvisingApp\Research\Models\ResearchRequestParsedLink;
use AdvisingApp\Research\Models\ResearchRequestParsedSearchResults;
use AdvisingApp\Research\Models\ResearchRequestQuestion;
use AdvisingApp\ResourceHub\Models\ManagerResourceHubArticle;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\ResourceHub\Models\ResourceHubArticleConcern;
use AdvisingApp\ResourceHub\Models\ResourceHubArticleUpvote;
use AdvisingApp\ResourceHub\Models\ResourceHubArticleView;
use AdvisingApp\ResourceHub\Models\ResourceHubCategory;
use AdvisingApp\ResourceHub\Models\ResourceHubQuality;
use AdvisingApp\ResourceHub\Models\ResourceHubStatus;
use AdvisingApp\StudentDataModel\Models\BouncedEmailAddress;
use AdvisingApp\StudentDataModel\Models\BouncedPhoneNumber;
use AdvisingApp\StudentDataModel\Models\EmailAddressOptInOptOut;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\EnrollmentSemester;
use AdvisingApp\StudentDataModel\Models\Hold;
use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentAddress;
use AdvisingApp\StudentDataModel\Models\StudentDataImport;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use AdvisingApp\Survey\Models\Survey;
use AdvisingApp\Survey\Models\SurveyAuthentication;
use AdvisingApp\Survey\Models\SurveyField;
use AdvisingApp\Survey\Models\SurveyFieldSubmission;
use AdvisingApp\Survey\Models\SurveyStep;
use AdvisingApp\Survey\Models\SurveySubmission;
use AdvisingApp\Task\Models\ConfidentialTasksUsers;
use AdvisingApp\Task\Models\DepartmentsConfidentialTasks;
use AdvisingApp\Task\Models\Task;
use AdvisingApp\Team\Models\Department;
use AdvisingApp\Timeline\Models\History;
use AdvisingApp\Timeline\Models\Timeline;
use AdvisingApp\Webhook\Models\InboundWebhook;
use AdvisingApp\Webhook\Models\LandlordInboundWebhook;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowCareTeamDetails;
use AdvisingApp\Workflow\Models\WorkflowDetails;
use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use AdvisingApp\Workflow\Models\WorkflowEngagementSmsDetails;
use AdvisingApp\Workflow\Models\WorkflowEventDetails;
use AdvisingApp\Workflow\Models\WorkflowInteractionDetails;
use AdvisingApp\Workflow\Models\WorkflowProactiveConcernDetails;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use AdvisingApp\Workflow\Models\WorkflowStep;
use AdvisingApp\Workflow\Models\WorkflowSubscriptionDetails;
use AdvisingApp\Workflow\Models\WorkflowTagsDetails;
use AdvisingApp\Workflow\Models\WorkflowTaskDetails;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\Authenticatable;
use App\Models\BaseModel;
use App\Models\Export;
use App\Models\FailedImportRow;
use App\Models\HealthCheckResultHistoryItem;
use App\Models\Import;
use App\Models\LandlordSettingsProperty;
use App\Models\MonitoredScheduledTask;
use App\Models\MonitoredScheduledTaskLogItem;
use App\Models\NotificationSetting;
use App\Models\NotificationSettingPivot;
use App\Models\Pronouns;
use App\Models\SettingsProperty;
use App\Models\SettingsPropertyWithMedia;
use App\Models\SystemUser;
use App\Models\Tag;
use App\Models\Taggable;
use App\Models\Tenant;
use App\Models\User;

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

// This is a frozen snapshot of all legacy model classes that use HasVersion4Uuids (ordered UUIDv4).
// New models should use HasUuids (UUIDv7) instead.
// Do NOT add new models to this list. Do NOT remove models from this list unless they have been
// intentionally migrated from HasVersion4Uuids to HasUuids (UUIDv7).

return [
    AiAssistant::class,
    AiAssistantConfidentialDepartment::class,
    AiAssistantConfidentialUser::class,
    AiAssistantFile::class,
    AiAssistantLink::class,
    AiAssistantUpvote::class,
    AiAssistantUse::class,
    AiMessage::class,
    AiMessageFile::class,
    AiThread::class,
    AiThreadFolder::class,
    DepartmentConfidentialPrompt::class,
    ConfidentialPromptUser::class,
    DataAdvisor::class,
    LegacyAiMessageLog::class,
    Prompt::class,
    PromptType::class,
    PromptUpvote::class,
    PromptUse::class,
    CustomerAdvisor::class,
    CustomerAdvisorCategory::class,
    CustomerAdvisorFile::class,
    CustomerAdvisorLink::class,
    CustomerAdvisorMessage::class,
    CustomerAdvisorQuestion::class,
    CustomerAdvisorThread::class,
    AlertConfiguration::class,
    StudentAlert::class,
    Application::class,
    ApplicationAuthentication::class,
    ApplicationField::class,
    ApplicationFieldSubmission::class,
    ApplicationStep::class,
    ApplicationSubmission::class,
    ApplicationSubmissionState::class,
    ApplicationSubmissionsChecklistItem::class,
    Audit::class,
    License::class,
    OtpLoginCode::class,
    Permission::class,
    PermissionGroup::class,
    Role::class,
    BasicNeedsCategory::class,
    BasicNeedsProgram::class,
    Campaign::class,
    CampaignAction::class,
    CampaignActionEducatable::class,
    CampaignActionEducatableRelated::class,
    CareTeam::class,
    CareTeamRole::class,
    Concern::class,
    ConcernStatus::class,
    ConsentAgreement::class,
    UserConsentAgreement::class,
    Division::class,
    EmailTemplate::class,
    Engagement::class,
    EngagementBatch::class,
    EngagementFile::class,
    EngagementFileEntities::class,
    EngagementResponse::class,
    HolisticEngagement::class,
    SmsTemplate::class,
    UnmatchedInboundCommunication::class,
    Form::class,
    FormAuthentication::class,
    FormEmailAutoReply::class,
    FormField::class,
    FormFieldSubmission::class,
    FormStep::class,
    FormSubmission::class,
    Submissible::class,
    SubmissibleAuthentication::class,
    SubmissibleField::class,
    SubmissibleStep::class,
    Submission::class,
    Group::class,
    GroupSubject::class,
    OpenAiResearchRequestVectorStore::class,
    OpenAiVectorStore::class,
    Interaction::class,
    DepartmentConfidentialInteraction::class,
    InteractionConfidentialUser::class,
    InteractionDriver::class,
    InteractionInitiative::class,
    InteractionOutcome::class,
    InteractionRelation::class,
    InteractionStatus::class,
    InteractionType::class,
    BookingGroup::class,
    BookingGroupAppointment::class,
    BookingGroupDepartment::class,
    BookingGroupUser::class,
    Calendar::class,
    CalendarEvent::class,
    Event::class,
    EventAttendee::class,
    EventRegistrationForm::class,
    EventRegistrationFormAuthentication::class,
    EventRegistrationFormField::class,
    EventRegistrationFormFieldSubmission::class,
    EventRegistrationFormStep::class,
    EventRegistrationFormSubmission::class,
    PersonalBookingPage::class,
    DatabaseMessage::class,
    EmailMessage::class,
    EmailMessageEvent::class,
    SmsMessage::class,
    SmsMessageEvent::class,
    StoredAnonymousNotifiable::class,
    Subscription::class,
    PortalAuthentication::class,
    Prospect::class,
    ProspectAddress::class,
    ProspectEmailAddress::class,
    ProspectPhoneNumber::class,
    ProspectSource::class,
    ProspectStatus::class,
    TrackedEvent::class,
    TrackedEventCount::class,
    ResearchRequest::class,
    ResearchRequestFolder::class,
    ResearchRequestParsedFile::class,
    ResearchRequestParsedLink::class,
    ResearchRequestParsedSearchResults::class,
    ResearchRequestQuestion::class,
    ManagerResourceHubArticle::class,
    ResourceHubArticle::class,
    ResourceHubArticleConcern::class,
    ResourceHubArticleUpvote::class,
    ResourceHubArticleView::class,
    ResourceHubCategory::class,
    ResourceHubQuality::class,
    ResourceHubStatus::class,
    BouncedEmailAddress::class,
    BouncedPhoneNumber::class,
    EmailAddressOptInOptOut::class,
    Enrollment::class,
    EnrollmentSemester::class,
    Hold::class,
    Program::class,
    SmsOptOutPhoneNumber::class,
    Student::class,
    StudentAddress::class,
    StudentDataImport::class,
    StudentEmailAddress::class,
    StudentPhoneNumber::class,
    Survey::class,
    SurveyAuthentication::class,
    SurveyField::class,
    SurveyFieldSubmission::class,
    SurveyStep::class,
    SurveySubmission::class,
    DepartmentsConfidentialTasks::class,
    ConfidentialTasksUsers::class,
    Task::class,
    Department::class,
    History::class,
    Timeline::class,
    InboundWebhook::class,
    LandlordInboundWebhook::class,
    Workflow::class,
    WorkflowCareTeamDetails::class,
    WorkflowDetails::class,
    WorkflowEngagementEmailDetails::class,
    WorkflowEngagementSmsDetails::class,
    WorkflowEventDetails::class,
    WorkflowInteractionDetails::class,
    WorkflowProactiveConcernDetails::class,
    WorkflowRun::class,
    WorkflowRunStep::class,
    WorkflowRunStepRelated::class,
    WorkflowStep::class,
    WorkflowSubscriptionDetails::class,
    WorkflowTagsDetails::class,
    WorkflowTaskDetails::class,
    WorkflowTrigger::class,
    Authenticatable::class,
    BaseModel::class,
    Export::class,
    FailedImportRow::class,
    HealthCheckResultHistoryItem::class,
    Import::class,
    LandlordSettingsProperty::class,
    MonitoredScheduledTask::class,
    MonitoredScheduledTaskLogItem::class,
    NotificationSetting::class,
    NotificationSettingPivot::class,
    Pronouns::class,
    SettingsProperty::class,
    SettingsPropertyWithMedia::class,
    SystemUser::class,
    Tag::class,
    Taggable::class,
    Tenant::class,
    User::class,
];
