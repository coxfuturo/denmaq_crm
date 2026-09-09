<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', [LoginController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('loginDashboard');

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('users', UserController::class);

        Route::get('/users/{id}/restore', [UserController::class, 'restore'])
            ->name('users.restore');

        Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])
            ->name('users.forceDelete');

        Route::get('/users/{id}/status', [UserController::class, 'status'])
            ->name('users.status');

        Route::resource('roles', RoleController::class);

        Route::get('/roles/{id}/status', [RoleController::class, 'status'])
            ->name('roles.status');

        Route::get('/roles/{id}/restore', [RoleController::class, 'restore'])
            ->name('roles.restore');

        Route::post('/leads/{lead}/status', [LeadController::class, 'changeStatus'])
            ->name('leads.changeStatus');

        Route::get('/leads-trash', [LeadController::class, 'trash'])
            ->name('leads.trash');

        Route::post('/leads/{id}/restore', [LeadController::class, 'restore'])
            ->name('leads.restore');

        Route::delete('/leads/{id}/force-delete', [LeadController::class, 'forceDelete'])
            ->name('leads.forceDelete');

         Route::resource('leads', LeadController::class);

    });
