<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('loginDashboard');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('users', UserController::class);        
Route::resource('roles', RoleController::class);
Route::get('/roles/{id}/status', [RoleController::class, 'status'])->name('roles.status');
Route::get('/roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
Route::resource('permissions', PermissionController::class);
Route::resource('leads', LeadController::class);

    });