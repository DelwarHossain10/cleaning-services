<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\TaskAssignCompanyController;


// Public Routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/password_recovery', [UserController::class, 'password_recovery']);
Route::post('/verify_and_reset_password', [UserController::class, 'verify_and_reset_password']);
// Route::post('/send-reset-password-email', [PasswordResetController::class, 'send_reset_password_email']);
// Route::post('/reset-password/{token}', [PasswordResetController::class, 'reset']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/loggeduser', [UserController::class, 'logged_user']);
    Route::post('/change_password', [UserController::class, 'change_password']);

    //List
    Route::get('/role_list', [UserController::class, 'role_list']);
    Route::get('/permission_list', [UserController::class, 'permission_list']);
    Route::get('/user_list', [UserController::class, 'user_list']);

    //User Update
    Route::put('/user_update/{id}', [UserController::class, 'user_update']);

    //Assign Permission
    Route::put('/assign_permission/{id}', [UserController::class, 'assign_permission']);

    //role & permission
    Route::resource('roles', RolesController::class);
    Route::resource('permissions', PermissionsController::class);

    Route::get('get_activities', [CommonController::class, 'get_activities']);
    Route::get('get_activity_details', [CommonController::class, 'get_activity_details']);
    Route::get('get_activity_detail_comments', [CommonController::class, 'get_activity_detail_comments']);
    Route::get('get_categories', [CommonController::class, 'get_categories']);
    Route::get('get_company_details', [CommonController::class, 'get_company_details']);
    Route::get('get_company_masters', [CommonController::class, 'get_company_masters']);

    //Activities
    Route::resource('activities', ActivityController::class);

    //Categories
    Route::resource('categories', CategoryController::class);

    //Company
    Route::resource('company', CompanyController::class);

    //Task Assign Company
    Route::resource('task_assign_company', TaskAssignCompanyController::class);

    //Task 
    Route::resource('task', TaskController::class);
});

Route::any('{any}', function () {
    return response()->json([
        'status'    => false,
        'message'   => 'This Api does not exists Or Please Login!',
    ], 404);
})->where('any', '.*');
