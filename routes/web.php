<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttsController;
use App\Http\Controllers\DutyController;
use App\Http\Controllers\EresController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\ViewController;
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
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SoldierExportController;



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

Route::middleware('auth')->group(function () {
    // Route::prefix('/dashboard', function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    // })->middleware(['auth', 'verified'])->name('dashboard');

    Route::prefix('leave')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('leave.index');
        Route::post('submit', [LeaveController::class, 'leaveApplicationSubmit'])->name('leave.leaveApplicationSubmit');
        Route::post('changeStatusSubmit', [LeaveController::class, 'changeStatus'])->name('leave.changeStatusSubmit');
        Route::put('update/{id}', [LeaveController::class, 'update'])->name('leave.update');
        Route::delete('{id}', [LeaveController::class, 'destroy'])->name('leave.destroy');


        Route::get('approval/', [LeaveController::class, 'approvalList'])->name('leave.approveList');
        Route::post('approval/{id}', [LeaveController::class, 'approvalAction'])->name('leave.approveAction');
    });

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


    // routes/web.php

    Route::prefix('duty')->group(function () {

        Route::get('create', [DutyController::class, 'create'])->name('duty.create');
        Route::get('', [DutyController::class, 'index'])->name('duty.index');
        Route::get('{duty}/edit', [DutyController::class, 'edit'])->name('duty.edit');

        // This route handles the form submission to update the record in the database.
        Route::put('{duty}', [DutyController::class, 'update'])->name('duty.update');
        // Route to handle the form submission and store the new record
        Route::post('store', [DutyController::class, 'store'])->name('duty.store');
        Route::delete('{duty}', [DutyController::class, 'destroy'])->name('duty.destroy');

        Route::get('assign', [DutyController::class, 'assignDuties'])
            ->name('duty.assign');

        // Store/save the assignments

        Route::get('assignlist', [DutyController::class, 'assignList'])->name('duty.assign');
        Route::post('assign', [DutyController::class, 'storeAssignments'])->name('duty.storeAssignment');
        Route::get('assign', [DutyController::class, 'createAssignments'])->name('duty.createAssignment');



        Route::get('assign/{id}/edit', [DutyController::class, 'editAssignment'])->name('duty.editAssignment');
        Route::put('assign/{id}', [DutyController::class, 'updateAssignment'])->name('duty.updateAssignment');
        Route::delete('assign/{id}', [DutyController::class, 'deleteAssignment'])->name('duty.deleteAssignment');
    });
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


    Route::get('sports', [ViewController::class, 'sportsIndex'])->name('sports.index');
    Route::get('sports/create', [ViewController::class, 'sportsCreate'])->name('sports.create');


    Route::get('otherQual/index', [ViewController::class, 'otherQualIndex'])->name('otherQual.index');
    Route::get('otherQual/create', [ViewController::class, 'otherQualCreate'])->name('otherQual.create');


    Route::get('absent/index', [ViewController::class, 'absentIndex'])->name('absent.index');
    Route::get('absent/create', [ViewController::class, 'absentCreate'])->name('absent.create');


    // Route::get('leaveType/index', [ViewController::class, 'leaveTypeIndex'])->name('leaveType.index');
    // Route::get('leaveType/create', [ViewController::class, 'leaveTypeCreate'])->name('leaveType.create');


    // Route::get('duty/index', [ViewController::class, 'dutyIndex'])->name('duty.index');
    // Route::get('duty/create', [ViewController::class, 'dutyCreate'])->name('duty.create');


    Route::get('assignDuty/index', [ViewController::class, 'assignDutyIndex'])->name('assignDuty.index');
    Route::get('assignDuty/create', [ViewController::class, 'assignDutyCreate'])->name('assignDuty.create');


    Route::get('approval/duty', [ViewController::class, 'approveDuty'])->name('approveDuty.duty');
    Route::get('approval/leave', [ViewController::class, 'approveLeave'])->name('approveDuty.leave');


    Route::get('assignLeave/index', [ViewController::class, 'assignLeaveIndex'])->name('assignLeave.index');
    Route::get('assignLeave/create', [ViewController::class, 'assignLeaveCreate'])->name('assignLeave.create');



    // Route::get('filter', [ViewController::class, 'filter'])->name('filter');
    // Route::get('filters', [ViewController::class, 'filters'])->name('filters');





    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';



Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {});


Route::prefix('settings')->middleware(['auth', 'role:admin'])->group(function () {
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
});
