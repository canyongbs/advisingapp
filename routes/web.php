<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KbItemController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CaseItemController;
use App\Http\Controllers\Admin\UserAlertController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Auth\UserProfileController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\Admin\JourneyItemController;
use App\Http\Controllers\Admin\SupportItemController;
use App\Http\Controllers\Admin\SupportPageController;
use App\Http\Controllers\Admin\CaseItemTypeController;
use App\Http\Controllers\Admin\KbItemStatusController;
use App\Http\Controllers\Admin\ProspectItemController;
use App\Http\Controllers\Admin\KbItemQualityController;
use App\Http\Controllers\Admin\ReportStudentController;
use App\Http\Controllers\Admin\CaseItemStatusController;
use App\Http\Controllers\Admin\CaseUpdateItemController;
use App\Http\Controllers\Admin\KbItemCategoryController;
use App\Http\Controllers\Admin\ProspectSourceController;
use App\Http\Controllers\Admin\ProspectStatusController;
use App\Http\Controllers\Admin\ReportProspectController;
use App\Http\Controllers\Admin\JourneyTextItemController;
use App\Http\Controllers\Admin\CaseItemPriorityController;
use App\Http\Controllers\Admin\JourneyEmailItemController;
use App\Http\Controllers\Admin\JourneyTargetListController;
use App\Http\Controllers\Admin\RecordProgramItemController;
use App\Http\Controllers\Admin\RecordStudentItemController;
use App\Http\Controllers\Admin\EngagementTextItemController;
use App\Http\Controllers\Admin\EngagementEmailItemController;
use App\Http\Controllers\Admin\SupportFeedbackItemController;
use App\Http\Controllers\Admin\SupportTrainingItemController;
use App\Http\Controllers\Admin\RecordEnrollmentItemController;
use App\Http\Controllers\Admin\EngagementStudentFileController;
use App\Http\Controllers\Admin\EngagementInteractionItemController;
use App\Http\Controllers\Admin\EngagementInteractionTypeController;
use App\Http\Controllers\Admin\EngagementInteractionDriverController;
use App\Http\Controllers\Admin\EngagementInteractionOutcomeController;
use App\Http\Controllers\Admin\EngagementInteractionRelationController;

Route::redirect('/', '/login');

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Permissions
    Route::resource('permissions', PermissionController::class, ['except' => ['store', 'update', 'destroy']]);

    // Roles
    Route::resource('roles', RoleController::class, ['except' => ['store', 'update', 'destroy']]);

    // Users
    Route::resource('users', UserController::class, ['except' => ['store', 'update', 'destroy']]);

    // Audit Logs
    Route::resource('audit-logs', AuditLogController::class, ['except' => ['store', 'update', 'destroy', 'create', 'edit']]);

    // User Alert
    Route::get('user-alerts/seen', [UserAlertController::class, 'seen'])->name('user-alerts.seen');
    Route::resource('user-alerts', UserAlertController::class, ['except' => ['store', 'update', 'destroy']]);

    // Institution
    Route::post('institutions/csv', [InstitutionController::class, 'csvStore'])->name('institutions.csv.store');
    Route::put('institutions/csv', [InstitutionController::class, 'csvUpdate'])->name('institutions.csv.update');
    Route::resource('institutions', InstitutionController::class, ['except' => ['store', 'update', 'destroy']]);

    // Case Item
    Route::post('case-items/csv', [CaseItemController::class, 'csvStore'])->name('case-items.csv.store');
    Route::put('case-items/csv', [CaseItemController::class, 'csvUpdate'])->name('case-items.csv.update');
    Route::resource('case-items', CaseItemController::class, ['except' => ['store', 'update', 'destroy']]);

    // Case Item Status
    Route::resource('case-item-statuses', CaseItemStatusController::class, ['except' => ['store', 'update', 'destroy']]);

    // Case Item Type
    Route::resource('case-item-types', CaseItemTypeController::class, ['except' => ['store', 'update', 'destroy']]);

    // Case Item Priority
    Route::resource('case-item-priorities', CaseItemPriorityController::class, ['except' => ['store', 'update', 'destroy']]);

    // Kb Item
    Route::resource('kb-items', KbItemController::class, ['except' => ['store', 'update', 'destroy']]);

    // Kb Item Quality
    Route::resource('kb-item-qualities', KbItemQualityController::class, ['except' => ['store', 'update', 'destroy']]);

    // Kb Item Status
    Route::resource('kb-item-statuses', KbItemStatusController::class, ['except' => ['store', 'update', 'destroy']]);

    // Kb Item Category
    Route::resource('kb-item-categories', KbItemCategoryController::class, ['except' => ['store', 'update', 'destroy']]);

    // Engagement Interaction Item
    Route::resource('engagement-interaction-items', EngagementInteractionItemController::class, ['except' => ['store', 'update', 'destroy']]);

    // Engagement Interaction Type
    Route::resource('engagement-interaction-types', EngagementInteractionTypeController::class, ['except' => ['store', 'update', 'destroy']]);

    // Engagement Interaction Relation
    Route::resource('engagement-interaction-relations', EngagementInteractionRelationController::class, ['except' => ['store', 'update', 'destroy']]);

    // Engagement Interaction Driver
    Route::post('engagement-interaction-drivers/csv', [EngagementInteractionDriverController::class, 'csvStore'])->name('engagement-interaction-drivers.csv.store');
    Route::put('engagement-interaction-drivers/csv', [EngagementInteractionDriverController::class, 'csvUpdate'])->name('engagement-interaction-drivers.csv.update');
    Route::resource('engagement-interaction-drivers', EngagementInteractionDriverController::class, ['except' => ['store', 'update', 'destroy']]);

    // Engagement Interaction Outcome
    Route::post('engagement-interaction-outcomes/csv', [EngagementInteractionOutcomeController::class, 'csvStore'])->name('engagement-interaction-outcomes.csv.store');
    Route::put('engagement-interaction-outcomes/csv', [EngagementInteractionOutcomeController::class, 'csvUpdate'])->name('engagement-interaction-outcomes.csv.update');
    Route::resource('engagement-interaction-outcomes', EngagementInteractionOutcomeController::class, ['except' => ['store', 'update', 'destroy']]);

    // Support Item
    Route::resource('support-items', SupportItemController::class, ['except' => ['store', 'update', 'destroy']]);

    // Support Training Item
    Route::resource('support-training-items', SupportTrainingItemController::class, ['except' => ['store', 'update', 'destroy']]);

    // Support Feedback Item
    Route::resource('support-feedback-items', SupportFeedbackItemController::class, ['except' => ['store', 'update', 'destroy']]);

    // Support Pages
    Route::resource('support-pages', SupportPageController::class, ['except' => ['store', 'update', 'destroy']]);

    // Record Enrollment Item
    Route::resource('record-enrollment-items', RecordEnrollmentItemController::class, ['except' => ['store', 'update', 'destroy', 'create', 'edit']]);

    // Record Program Item
    Route::resource('record-program-items', RecordProgramItemController::class, ['except' => ['store', 'update', 'destroy', 'create', 'edit']]);

    // Record Student Item
    Route::resource('record-student-items', RecordStudentItemController::class, ['except' => ['store', 'update', 'destroy', 'create', 'edit']]);

    // Engagement Email Item
    Route::resource('engagement-email-items', EngagementEmailItemController::class, ['except' => ['store', 'update', 'destroy', 'edit']]);

    // Engagement Text Item
    Route::resource('engagement-text-items', EngagementTextItemController::class, ['except' => ['store', 'update', 'destroy', 'edit']]);

    // Engagement Student Files
    Route::post('engagement-student-files/media', [EngagementStudentFileController::class, 'storeMedia'])->name('engagement-student-files.storeMedia');
    Route::resource('engagement-student-files', EngagementStudentFileController::class, ['except' => ['store', 'update', 'destroy']]);

    // Prospect Item
    Route::post('prospect-items/csv', [ProspectItemController::class, 'csvStore'])->name('prospect-items.csv.store');
    Route::put('prospect-items/csv', [ProspectItemController::class, 'csvUpdate'])->name('prospect-items.csv.update');
    Route::resource('prospect-items', ProspectItemController::class, ['except' => ['store', 'update', 'destroy']]);

    // Case Update Item
    Route::resource('case-update-items', CaseUpdateItemController::class, ['except' => ['store', 'update', 'destroy', 'edit']]);

    // Report Student
    Route::resource('report-students', ReportStudentController::class, ['except' => ['store', 'update', 'destroy']]);

    // Report Prospect
    Route::resource('report-prospects', ReportProspectController::class, ['except' => ['store', 'update', 'destroy']]);

    // Journey Item
    Route::resource('journey-items', JourneyItemController::class, ['except' => ['store', 'update', 'destroy']]);

    // Journey Email Item
    Route::resource('journey-email-items', JourneyEmailItemController::class, ['except' => ['store', 'update', 'destroy']]);

    // Journey Text Item
    Route::resource('journey-text-items', JourneyTextItemController::class, ['except' => ['store', 'update', 'destroy']]);

    // Journey Target List
    Route::resource('journey-target-lists', JourneyTargetListController::class, ['except' => ['store', 'update', 'destroy']]);

    // Prospect Status
    Route::resource('prospect-statuses', ProspectStatusController::class, ['except' => ['store', 'update', 'destroy']]);

    // Prospect Source
    Route::resource('prospect-sources', ProspectSourceController::class, ['except' => ['store', 'update', 'destroy']]);
});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['auth']], function () {
    if (file_exists(app_path('Http/Controllers/Auth/UserProfileController.php'))) {
        Route::get('/', [UserProfileController::class, 'show'])->name('show');
    }
});
