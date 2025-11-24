<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttsController;
use App\Http\Controllers\DutyController;
use App\Http\Controllers\EresController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\AbsentController;
use App\Http\Controllers\CadreController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\SoldierController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\FilterController;
use App\Http\Controllers\SkillCategoryController;
use App\Http\Controllers\DutyAssignmentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentManagerController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CommissionsController;
use App\Http\Controllers\CompanyRankManpowerController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SoldierExportController;
use App\Http\Controllers\CourseCadreManagerController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\CmdController;
use App\Http\Controllers\ExAreaController;
use App\Http\Controllers\AbsentTypeController;
use App\Http\Controllers\MedicalCategoryController;
use App\Http\Controllers\PermanentSicknessController;
use App\Http\Controllers\InstructionRecomendationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::middleware('auth', 'check.leaves')->group(function () {
    // Route::prefix('/dashboard', function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    // })->middleware(['auth', 'verified'])->name('dashboard');

    Route::prefix('leave')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('leave.index');
        Route::post('submit', [LeaveController::class, 'leaveApplicationSubmit'])->name('leave.leaveApplicationSubmit');
        Route::post('changeStatusSubmit', [LeaveController::class, 'changeStatus'])->name('leave.changeStatusSubmit');
        Route::put('update/{id}', [LeaveController::class, 'update'])->name('leave.update');
        Route::delete('{id}', [LeaveController::class, 'destroy'])->name('leave.destroy');

        Route::post('/leave/filter', [LeaveController::class, 'filter'])->name('leave.filter');
        Route::get('approval/', [LeaveController::class, 'approvalList'])->name('leave.approveList');
        Route::post('approval/{id}', [LeaveController::class, 'approvalAction'])->name('leave.approveAction');
        Route::post('/leave/bulk-status-update', [LeaveController::class, 'bulkStatusUpdate'])->name('leave.bulkStatusUpdate');
        Route::delete('/leave/bulk-delete', [LeaveController::class, 'bulkDelete'])->name('leave.bulkDelete');
    });

    // Absent Routes
    Route::get('absent', [AbsentController::class, 'index'])->name('absent.index');
    Route::post('absent/filter', [AbsentController::class, 'filter'])->name('absent.filter');
    Route::post('absent/submit', [AbsentController::class, 'absentApplicationSubmit'])->name('absent.absentApplicationSubmit');
    Route::post('absent/change-status', [AbsentController::class, 'changeStatus'])->name('absent.changeStatusSubmit');
    Route::put('absent/{id}', [AbsentController::class, 'update'])->name('absent.update');
    Route::delete('absent/{id}', [AbsentController::class, 'destroy'])->name('absent.destroy');

    Route::prefix('army')->group(function () {
        // Profile list
        Route::get('/', [SoldierController::class, 'index'])->name('soldier.index');

        // Filter options endpoint for AJAX calls
        Route::get('/filter-options', [SoldierController::class, 'getFilterOptions'])
            ->name('soldier.filterOptions');

        // Personal step (optional {profile} for first-time creation)
        Route::get('personal/{profile?}', [SoldierController::class, 'personalForm'])->name('soldier.personalForm');
        Route::post('personal', [SoldierController::class, 'savePersonal'])->name('soldier.savePersonal');
        Route::put('profile/{soldier}/personal', [SoldierController::class, 'updatePersonal'])
            ->name('soldier.updatePersonal');

        // Service step
        Route::get('{id}/service', [SoldierController::class, 'serviceForm'])->name('soldier.serviceForm');
        Route::post('{id}/service', [SoldierController::class, 'saveService'])->name('soldier.saveService');

        // Qualifications
        Route::get('{id}/qualifications', [SoldierController::class, 'qualificationsForm'])->name('soldier.qualificationsForm');
        Route::post('{id}/qualifications', [SoldierController::class, 'saveQualifications'])->name('soldier.saveQualifications');

        // Medical
        Route::get('{id}/medical', [SoldierController::class, 'medicalForm'])->name('soldier.medicalForm');
        Route::post('{id}/medical', [SoldierController::class, 'saveMedical'])->name('soldier.saveMedical');

        Route::get('{id}/details', [SoldierController::class, 'details'])->name('soldier.details');

        // Complete
        Route::get('complete', function () {
            return view('soldier.complete');
        })->name('soldier.complete');

        // Profile management endpoints
        Route::delete('{soldier}', [SoldierController::class, 'destroy'])->name('soldier.destroy');
        // Route::get('export', [SoldierController::class, 'export'])->name('soldier.export');
        Route::get('export', [SoldierExportController::class, 'export'])
            ->name('soldier.export');
        Route::post('bulk-update-status', [SoldierController::class, 'bulkUpdateStatus'])->name('soldier.bulkUpdateStatus');
        Route::post('bulk-delete', [SoldierController::class, 'bulkDelete'])->name('soldier.bulkDelete');
        Route::get('{soldier}/profile', [SoldierController::class, 'getProfileData'])
            ->name('soldier.profile');
    });

    Route::prefix('appointmanager')->name('appointmanager.')->group(function () {
        Route::get('/', [AppointmentManagerController::class, 'index'])->name('index');
        Route::get('/create', [AppointmentManagerController::class, 'create'])->name('create');
        Route::post('/', [AppointmentManagerController::class, 'store'])->name('store');
        Route::get('/{id}', [AppointmentManagerController::class, 'show'])->name('show');
        Route::put('/{id}', [AppointmentManagerController::class, 'update'])->name('update');
        Route::put('/{id}/release', [AppointmentManagerController::class, 'release'])->name('release');
        Route::delete('/{id}', [AppointmentManagerController::class, 'destroy'])->name('destroy');
    });
    // routes/web.php
    Route::get('/coursecadremanager', [CourseCadreManagerController::class, 'index'])->name('coursecadremanager.index');
    Route::get('/coursecadremanager/create', [CourseCadreManagerController::class, 'create'])->name('coursecadremanager.create');
    Route::post('/coursecadremanager', [CourseCadreManagerController::class, 'store'])->name('coursecadremanager.store');
    Route::put('/coursecadremanager/course/{id}/complete', [CourseCadreManagerController::class, 'completeCourse'])->name('coursecadremanager.course.complete');
    Route::put('/coursecadremanager/cadre/{id}/complete', [CourseCadreManagerController::class, 'completeCadre'])->name('coursecadremanager.cadre.complete');
    Route::delete('/coursecadremanager/{type}/{id}', [CourseCadreManagerController::class, 'destroy'])->name('coursecadremanager.destroy');
    Route::put('/coursecadremanager/courses/bulk-complete', [CourseCadreManagerController::class, 'bulkCompleteCourses'])->name('coursecadremanager.courses.bulk-complete');
    Route::put('/coursecadremanager/cadres/bulk-complete', [CourseCadreManagerController::class, 'bulkCompleteCadres'])->name('coursecadremanager.cadres.bulk-complete');
    Route::put('/coursecadremanager/ex-areas/bulk-complete', [CourseCadreManagerController::class, 'bulkCompleteExAreas'])->name('coursecadremanager.ex-areas.bulk-complete');

    Route::get('/coursecadremanager/{type}/{id}/edit-data', [CourseCadreManagerController::class, 'getEditData'])->name('coursecadremanager.edit-data');
    Route::put('/coursecadremanager/{type}/{id}', [CourseCadreManagerController::class, 'update'])->name('coursecadremanager.update');
    Route::put('/coursecadremanager/ex_area/{id}/complete', [CourseCadreManagerController::class, 'completeExArea'])->name('coursecadremanager.exarea.complete');

    Route::get('/soldiers/by-rank/{rank}', [ProfileController::class, 'getByRank'])->name('soldiers.byRank');
    Route::get('ranks-data', [RankController::class, 'getRanks'])->name('ranks.data');

    // Route to show the create form
    Route::prefix('assignments')->group(function () {
        Route::get('generate', [DutyAssignmentController::class, 'showForm'])
            ->name('assignments.generateForm');

        Route::post('today', [DutyAssignmentController::class, 'generateToday'])
            ->name('assignments.generateToday');

        Route::post('date', [DutyAssignmentController::class, 'generateForDate'])
            ->name('assignments.generateForDate');
    });

    // routes/api.php or routes/web.php



    Route::prefix('duty-assignments')->middleware(['auth'])->group(function () {
        Route::get('/', [DutyAssignmentController::class, 'index'])->name('duty-assignments.index');
        // API endpoints for AJAX calls
        Route::post('/assign', [DutyAssignmentController::class, 'assignForDate'])->name('duty-assignments.assign');
        Route::post('/assign-range', [DutyAssignmentController::class, 'assignForDateRange'])->name('duty-assignments.assign-range');
        Route::get('/statistics', [DutyAssignmentController::class, 'statistics'])->name('duty-assignments.statistics');
        Route::get('/details', [DutyAssignmentController::class, 'details'])->name('duty-assignments.details');
        Route::get('/unfulfilled', [DutyAssignmentController::class, 'unfulfilled'])->name('duty-assignments.unfulfilled');
        Route::post('/check-eligibility', [DutyAssignmentController::class, 'checkEligibility'])->name('duty-assignments.check-eligibility');
        // reassign means changing the soldier for an existing assignment
        Route::post('/reassign', [DutyAssignmentController::class, 'reassign'])->name('duty-assignments.reassign');
        Route::post('/assign-soldier', [DutyAssignmentController::class, 'assignSoldier'])->name('duty-assignments.assign-soldier');

        Route::post('/cancel', [DutyAssignmentController::class, 'cancel'])->name('duty-assignments.cancel');
        Route::get('/export', [DutyAssignmentController::class, 'export'])->name('duty-assignments.export');

        Route::get('/available-soldiers', [DutyAssignmentController::class, 'availableSoldiers']);
        Route::get('/available-duties', [DutyAssignmentController::class, 'availableDuties']);

        // routes/web.php
        Route::get('/duty-details/{dutyId}', [DutyAssignmentController::class, 'dutyDetails'])
            ->name('duty-assignments.duty-details');
    });


    // Route::get('sports', [ViewController::class, 'sportsIndex'])->name('sports.index');
    // Route::get('sports/create', [ViewController::class, 'sportsCreate'])->name('sports.create');


    // Route::get('otherQual/index', [ViewController::class, 'otherQualIndex'])->name('otherQual.index');
    // Route::get('otherQual/create', [ViewController::class, 'otherQualCreate'])->name('otherQual.create');


    // Route::get('absent/index', [ViewController::class, 'absentIndex'])->name('absent.index');
    // Route::get('absent/create', [ViewController::class, 'absentCreate'])->name('absent.create');


    // Route::get('leaveType/index', [ViewController::class, 'leaveTypeIndex'])->name('leaveType.index');
    // Route::get('leaveType/create', [ViewController::class, 'leaveTypeCreate'])->name('leaveType.create');


    // Route::get('duty/index', [ViewController::class, 'dutyIndex'])->name('duty.index');
    // Route::get('duty/create', [ViewController::class, 'dutyCreate'])->name('duty.create');


    // Route::get('assignDuty/index', [ViewController::class, 'assignDutyIndex'])->name('assignDuty.index');
    // Route::get('assignDuty/create', [ViewController::class, 'assignDutyCreate'])->name('assignDuty.create');


    // Route::get('approval/duty', [ViewController::class, 'approveDuty'])->name('approveDuty.duty');
    // Route::get('approval/leave', [ViewController::class, 'approveLeave'])->name('approveDuty.leave');


    // Route::get('assignLeave/index', [ViewController::class, 'assignLeaveIndex'])->name('assignLeave.index');
    // Route::get('assignLeave/create', [ViewController::class, 'assignLeaveCreate'])->name('assignLeave.create');



    // Route::get('filter', [ViewController::class, 'filter'])->name('filter');
    // Route::get('filters', [ViewController::class, 'filters'])->name('filters');





    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';



Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {});

Route::prefix('settings')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('medical-categories', MedicalCategoryController::class);
    Route::resource('permanent-sickness', PermanentSicknessController::class);

    Route::get('/site-settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/site-settings', [SiteSettingController::class, 'update'])->name('settings.update');

    Route::get('/primary-manpower', [CompanyRankManpowerController::class, 'index'])->name('company_rank_manpower.index');
    Route::post('/primary-manpower', [CompanyRankManpowerController::class, 'store'])->name('company_rank_manpower.store');


    Route::get('/', [SettingsController::class, 'index'])
        ->name('settings');

    Route::resource('permissions', PermissionController::class);
    // AJAX: Fetch permissions for a specific role
    Route::get('permissions/role/{role}', [PermissionController::class, 'getRolePermissions'])
        ->name('permissions.role.get');
    Route::put('permissions/role/{role}', [PermissionController::class, 'updateRolePermissions'])
        ->name('permissions.role.update');

    // users //
    Route::resource('users', UserController::class);
    // Manage roles for a user
    Route::get('users/{user}/roles', [UserController::class, 'editRoles'])->name('users.roles.edit');
    Route::put('users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');
    // role //
    Route::resource('roles', RoleController::class);

    Route::resource('atts', AttsController::class);
    // Additional route to toggle course status
    Route::patch('atts/{att}/toggle-status', [AttsController::class, 'toggleStatus'])
        ->name('atts.toggle-status');
    Route::resource('eres', EresController::class);

    Route::resource('instruction-recomendations', InstructionRecomendationController::class);
    Route::patch('instruction-recomendations/{instruction_recomendation}/toggle-status', [InstructionRecomendationController::class, 'toggleStatus'])->name('instruction-recomendations.toggle-status');

    Route::resource('cmds', CmdController::class);
    Route::patch('cmds/{cmd}/toggle-status', [CmdController::class, 'toggleStatus'])->name('cmds.toggle-status');
    Route::get('api/cmds', [CmdController::class, 'getCmds'])->name('cmds.api');

    Route::resource('ex-areas', ExAreaController::class);
    Route::patch('ex-areas/{ex_area}/toggle-status', [ExAreaController::class, 'toggleStatus'])->name('ex-areas.toggle-status');
    Route::get('api/ex-areas', [ExAreaController::class, 'getExAreas'])->name('ex-areas.api');

    Route::resource('absent-types', AbsentTypeController::class);
    Route::patch('absent-types/{absent_type}/toggle-status', [AbsentTypeController::class, 'toggleStatus'])->name('absent-types.toggle-status');
    Route::get('api/absent-types', [AbsentTypeController::class, 'getAbsentTypes'])->name('absent-types.api');
    // Additional route to toggle course status
    Route::patch('eres/{ere}/toggle-status', [EresController::class, 'toggleStatus'])
        ->name('eres.toggle-status');

    Route::resource('leave-types', LeaveTypeController::class);

    Route::patch('leave-types/{leave_type}/toggle-status', [LeaveTypeController::class, 'toggleStatus'])
        ->name('leave-types.toggle-status');
    // Company CRUD Routes
    Route::resource('companies', CompanyController::class);
    Route::patch('companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');


    // Resource routes for Course CRUD
    Route::resource('courses', CourseController::class);

    // Additional route to toggle course status
    Route::patch('courses/{course}/toggle-status', [CourseController::class, 'toggleStatus'])
        ->name('courses.toggle-status');



    Route::resource('cadres', CadreController::class);
    // Additional route to toggle course status
    Route::patch('cadres/{cadre}/toggle-status', [CadreController::class, 'toggleStatus'])
        ->name('cadres.toggle-status');

    Route::resource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/toggle-status', [AppointmentController::class, 'toggleStatus'])
        ->name('appointments.toggle-status');

    Route::resource('education', EducationController::class);
    // Additional route to toggle course status
    Route::patch('education/{education}/toggle-status', [EducationController::class, 'toggleStatus'])
        ->name('education.toggle-status');

    Route::resource('skillcategory', SkillCategoryController::class);
    // Additional route to toggle course status
    Route::patch('skillcategory/{skillcategory}/toggle-status', [SkillCategoryController::class, 'toggleStatus'])
        ->name('skillcategory.toggle-status');


    Route::resource('skill', SkillController::class);
    // Additional route to toggle course status
    Route::patch('skill/{skill}/toggle-status', [SkillController::class, 'toggleStatus'])
        ->name('skill.toggle-status');

    // Rank CRUD Routes
    Route::resource('ranks', RankController::class);
    Route::patch('ranks/{rank}/toggle-status', [RankController::class, 'toggleStatus'])->name('ranks.toggle-status');

    Route::resource('filters', FilterController::class);

    Route::prefix('duty')->group(function () {

        Route::get('create', [DutyController::class, 'create'])->name('duty.create');
        Route::get('', [DutyController::class, 'index'])->name('duty.index');
        Route::get('{duty}/edit', [DutyController::class, 'edit'])->name('duty.edit');

        // This route handles the form submission to update the record in the database.
        Route::put('{duty}', [DutyController::class, 'update'])->name('duty.update');
        // Route to handle the form submission and store the new record
        Route::post('store', [DutyController::class, 'store'])->name('duty.store');
        Route::delete('{duty}', [DutyController::class, 'destroy'])->name('duty.destroy');
    });
    Route::get('/duties/{duty}', [DutyController::class, 'show'])->name('duty.show');
    Route::get('/duties/statistics', [DutyController::class, 'getStatistics'])->name('duties.statistics');
    Route::get('/duties/available-soldiers', [DutyController::class, 'getAvailableSoldiers'])->name('duties.available-soldiers');
    Route::get('/soldiers/{soldier}/details', [DutyController::class, 'getSoldierDetails'])->name('soldiers.details');
    Route::post('/duties/{duty}/assign-soldier', [DutyController::class, 'assignSoldier'])->name('duties.assign-soldier');
    Route::post('/duties/{duty}/remove-soldier', [DutyController::class, 'removeSoldier'])->name('duties.remove-soldier');
    Route::post('/duties/check-availability', [DutyController::class, 'checkSoldierAvailability'])->name('duties.check-availability');
    Route::get('/duties/{duty}/assignments', [DutyController::class, 'getDutyAssignments'])->name('duties.assignments');
    Route::post('/duties/bulk-update-status', [DutyController::class, 'bulkUpdateStatus'])->name('duties.bulk-update-status');
    Route::post('/duties/export', [DutyController::class, 'export'])->name('duties.export');
    Route::post('/duties/{duty}/duplicate', [DutyController::class, 'duplicate'])->name('duty.duplicate');
});


Route::group(['middleware' => ['auth']], function () {
    Route::get('/audit-trail', [BackupController::class, 'index'])->name('audit-trail.index');
    Route::get('/audit-trail/{id}', [BackupController::class, 'show'])->name('audit-trail.view');
    Route::get('/users/{userId}', [BackupController::class, 'showUserProfile'])->name('users.show');
    Route::get('/download-database', [BackupController::class, 'downloadDatabase'])->name('database.download');

    route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/duties/{date}', [ReportController::class, 'getDutiesByDate'])->name('duties.stats');

    Route::prefix('export')->group(function () {
        Route::get('/duties/{type?}', [ExportController::class, 'exportDuties'])
            ->name('export.duties');
    });
    Route::get('/export/parade/{type}', [ExportController::class, 'exportParade'])->name('export.parade');
    Route::get('/export/manpower/{type}', [ExportController::class, 'exportManpower'])->name('export.manpower');
    // Route::get('/report/parade/{date}', [ReportController::class, 'paradeReport'])->name('report.parade');
    // Route::get('/export/game-attendance/{type}', [ExportController::class, 'exportGameAttendance'])->name('export.game');
    // In routes/web.php or routes/api.php
    // Dynamic attendance report export route
    Route::get('/export/{reportType}/attendance/{exportType}', [AttendanceReportController::class, 'exportAttendanceReport'])
        ->where('reportType', 'game|pt|roll-call|parade')
        ->where('exportType', 'xl|xlsx|pdf|excel')
        ->name('export.attendance');
});

use App\Http\Controllers\NotificationController;

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
});
Route::get('/test', [CommissionsController::class, 'testCommission'])->name('test');
