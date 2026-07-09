<?php

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

namespace AdvisingApp\Authorization\Enums;

use Filament\Support\Contracts\HasLabel;

enum PermissionDescription: string implements HasLabel
{
    case Application = 'This permission group enables the display and management of the Admissions feature in the primary navigation group CRM. Permission options include create, view, update, and delete applications.';
    case Assistant = 'This permission group enables the display and management of the Institutional Advisor feature in the primary navigation group Enterprise AI. Permission options include view institutional advisors.';
    case AssistantChatMessageLog = 'This permission group enables the display and management of the Assistant Utilization feature in the secondary navigation group Usage Auditing. Permission options include view assistant utilization report.';
    case AssistantCustom = 'This permission group enables the display and management of the Employee Advisor feature in the primary navigation group Chatbots. Permission options include create, view, update, and delete employee advisors.';
    case Audit = 'This permission group enables the display and management of the System Administration feature in the secondary navigation group Usage Auditing. Permission options include view system administration report.';
    case CalendarEvent = 'This permission group enables the display and management of the My Schedule feature in the primary navigation group Scheduling. Permission options include create, view, update, and delete appointments.';
    case Campaign = 'This permission group enables the display and management of the Campaign feature in the primary navigation group CRM. Permission options include create, view, update, and delete campaigns.';
    case CareTeam = 'This permission group enables the display and management of the Care Team feature in the tertiary navigation group View Student or View Prospect. Permission options include create, view, update, and delete care team members.';
    case Case = 'This permission group enables the display and management of the Case feature in the tertiary navigation group View Student or View Prospect. Permission options include create, view, update, and delete cases.';
    case CaseAssignment = 'This permission group enables the display and management of the Case Assignment feature in the quaternary navigation group View Case under a Student or Prospect. Permission options include create, view, update, and delete case assignments.';
    case CaseUpdate = 'This permission group enables the display and management of the Case Update feature in the quaternary navigation group View Case under a Student or Prospect. Permission options include create, view, update, and delete case updates.';
    case Concern = 'This permission group enables the display and management of the Concern feature in the tertiary navigation group View Student or View Prospect. Permission options include create, view, update, and delete concerns.';
    case CustomerAdvisor = 'This permission group enables the display and management of the Customer Advisor feature in the primary navigation group Chatbots. Permission options include create, view, update, and delete customer advisors.';
    case CustomerAdvisorEmbed = 'This permission group enables the display and management of the Customer Advisor Embed feature in the tertiary navigation group View Customer Advisor. Permission options include view customer advisor embeds.';
    case Division = 'This permission group enables the display and management of the Division feature in the primary navigation group User Management. Permission options include create, view, update, and delete divisions.';
    case Engagement = 'This permission group enables the display and management of the Sent Items feature in the secondary navigation group Unified Inbox. Permission options include create, view, update, and delete engagements.';
    case EngagementResponse = 'This permission group enables the display and management of the Inbox feature in the secondary navigation group Unified Inbox. Permission options include create, view, update, and delete engagement responses.';
    case Enrollment = 'This permission group enables the display and management of the Enrollment feature in the tertiary navigation group View Student. Permission options include create, view, update, and delete enrollments.';
    case Event = 'This permission group enables the display and management of the Event feature in the primary navigation group Scheduling. Permission options include create, view, update, and delete events.';
    case EventAttendee = 'This permission group enables the display and management of the Event Attendee feature in the tertiary navigation group View Event. Permission options include create and view event attendees.';
    case ExportHub = 'This permission group enables the display and management of the Export Hub feature in the primary navigation group Data and Analytics. Permission options include view exports.';
    case Form = 'This permission group enables the display and management of the Online Forms feature in the primary navigation group CRM. Permission options include create and view forms.';
    case Group = 'This permission group enables the display and management of the Student/Prospect Groups feature in the primary navigation group CRM. Permission options include create and view groups.';
    case GroupAppointment = 'This permission group enables the display and management of the Group Appointments feature in the primary navigation group Scheduling. Permission options include create and view group appointments.';
    case Interaction = 'This permission group enables the display and management of the Interaction feature in the tertiary navigation group View Student or View Prospect. Permission options include create, view, update, and delete interactions.';
    case JourneyStep = 'This permission group enables the display and management of the Journey Step feature in the tertiary navigation group View Campaign. Permission options include create, view, update, and delete journey steps.';
    case License = 'This permission group enables the display and management of the License feature in the tertiary navigation group View User. Permission options include granting and revoking licenses.';
    case Program = 'This permission group enables the display and management of the Program feature in the tertiary navigation group View Student. Permission options include create, view, update, and delete programs.';
    case Project = 'This permission group enables the display and management of the Project feature in the primary navigation group CRM. Permission options include create, view, update, and delete projects.';
    case Prompt = 'This permission group enables the display and management of the Prompt Library feature in the primary navigation group Enterprise AI. Permission options include create, view, update, and delete prompts.';
    case Prospect = 'This permission group enables the display and management of the Prospect feature in the primary navigation group CRM. Permission options include create, view, update, delete, and import prospects.';
    case RecordSync = 'This permission group enables the display and management of the Sync History feature in the primary navigation group Data and Analytics. Permission options include view record syncs.';
    case Report = 'This permission group enables the display and management of the Custom Reports feature in the primary navigation group Data and Analytics. Permission options include create, view, update, and delete custom reports.';
    case Reporting = 'This permission group enables the display and management of the Reporting feature in the primary navigation group User Management. Permission options include managing which users and departments have access to each report in the Report Library.';
    case ResearchAdvisor = 'This permission group enables the display and management of the Research Advisor feature in the primary navigation group Enterprise AI. Permission options include view research advisors.';
    case ResourceHubArticle = 'This permission group enables the display and management of the Resource Hub Article feature in the primary navigation group CRM. Permission options include create, view, update, and delete resource hub articles.';
    case Role = 'This permission group enables the display and management of the Role feature in the primary navigation group User Management. Permission options include create, view, update, and delete roles.';
    case Settings = 'This permission group enables access of the primary navigation group Settings.';
    case SISDataPipeline = 'This permission group enables the display and management of the SIS Data Pipeline feature in the primary navigation group Data and Analytics. Permission options include view SIS data pipelines.';
    case Student = 'This permission group enables the display and management of the Student feature in the primary navigation group CRM. Permission options include create, view, update, delete, and import students.';
    case Subscription = 'This permission group enables the display and management of the Subscription feature in the tertiary navigation group View Prospect. Permission options include create, view, update, and delete subscriptions.';
    case SupportProgram = 'This permission group enables the display and management of the Support Program feature in the primary navigation group CRM. Permission options include create, view, update, and delete support programs.';
    case Survey = 'This permission group enables the display and management of the Survey feature in the primary navigation group Premium Features. Permission options include create, view, update, and delete surveys.';
    case SystemUser = 'This permission group enables the display and management of the Programmatic Users feature in the primary navigation group User Management. Permission options include create, view, update, and delete programmatic users.';
    case Task = 'This permission group enables the display and management of the Task feature in the tertiary navigation group View Student or View Prospect. Permission options include create, view, update, delete, and import tasks.';
    case Team = 'This permission group enables the display and management of the Team feature in the primary navigation group User Management. Permission options include create, view, update, and delete teams.';
    case User = 'This permission group enables the display and management of the User feature in the primary navigation group User Management. Permission options include create, view, update, delete, and import users.';

    public function getLabel(): string
    {
        return $this->name;
    }
}
