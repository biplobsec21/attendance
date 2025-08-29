<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CadreController;
use App\Http\Controllers\EducationController;

use App\Http\Controllers\RankController;
use App\Http\Controllers\SkillCategoryController;
use App\Http\Controllers\SkillController;

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

Route::get('/', function () {
    return view('welcome');
});


Route::get('profile', [ViewController::class, 'profileIndex'])->name('profile.index');
Route::get('profile/create', [ViewController::class, 'profileCreate'])->name('profile.create');
Route::get('profile/view', [ViewController::class, 'profileView'])->name('profile.view');


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


Route::get('sports', [ViewController::class, 'sportsIndex'])->name('sports.index');
Route::get('sports/create', [ViewController::class, 'sportsCreate'])->name('sports.create');


Route::get('otherQual/index', [ViewController::class, 'otherQualIndex'])->name('otherQual.index');
Route::get('otherQual/create', [ViewController::class, 'otherQualCreate'])->name('otherQual.create');


Route::get('absent/index', [ViewController::class, 'absentIndex'])->name('absent.index');
Route::get('absent/create', [ViewController::class, 'absentCreate'])->name('absent.create');


Route::get('duty/index', [ViewController::class, 'dutyIndex'])->name('duty.index');
Route::get('duty/create', [ViewController::class, 'dutyCreate'])->name('duty.create');


Route::get('assignDuty/index', [ViewController::class, 'assignDutyIndex'])->name('assignDuty.index');
Route::get('assignDuty/create', [ViewController::class, 'assignDutyCreate'])->name('assignDuty.create');


Route::get('approval/duty', [ViewController::class, 'approveDuty'])->name('approveDuty.duty');
Route::get('approval/leave', [ViewController::class, 'approveLeave'])->name('approveDuty.leave');


Route::get('leave/index', [ViewController::class, 'leaveIndex'])->name('leave.index');
Route::get('leave/create', [ViewController::class, 'leaveCreate'])->name('leave.create');



Route::get('filter', [ViewController::class, 'filter'])->name('filter');
Route::get('filters', [ViewController::class, 'filters'])->name('filters');


// Rank CRUD Routes
Route::resource('ranks', RankController::class);
Route::patch('ranks/{rank}/toggle-status', [RankController::class, 'toggleStatus'])->name('ranks.toggle-status');
Route::get('ranks-data', [RankController::class, 'getRanks'])->name('ranks.data');
