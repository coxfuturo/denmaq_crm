<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\FollowUpController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProfileController;

Route::get('/', [LoginController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('loginDashboard');

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:users.view')
            ->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->middleware('permission:users.create')
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->middleware('permission:users.create')
            ->name('users.store');

        Route::get('/users/{id}', [UserController::class, 'show'])
            ->middleware('permission:users.view')
            ->name('users.show');

        Route::get('/users/{id}/edit', [UserController::class, 'edit'])
            ->middleware('permission:users.edit')
            ->name('users.edit');

        Route::put('/users/{id}', [UserController::class, 'update'])
            ->middleware('permission:users.edit')
            ->name('users.update');

        Route::patch('/users/{id}', [UserController::class, 'update'])
            ->middleware('permission:users.edit')
            ->name('users.update.patch');

        Route::delete('/users/{id}', [UserController::class, 'destroy'])
            ->middleware('permission:users.delete')
            ->name('users.destroy');

        Route::get('/users/{id}/restore', [UserController::class, 'restore'])
            ->middleware('permission:users.restore')
            ->name('users.restore');

        Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])
            ->middleware('permission:users.force-delete')
            ->name('users.forceDelete');

        Route::get('/users/{id}/status', [UserController::class, 'status'])
            ->middleware('permission:users.status')
            ->name('users.status');

        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('permission:roles.view')
            ->name('roles.index');

        Route::get('/roles/create', [RoleController::class, 'create'])
            ->middleware('permission:roles.create')
            ->name('roles.create');

        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('permission:roles.create')
            ->name('roles.store');

        Route::get('/roles/{id}', [RoleController::class, 'show'])
            ->middleware('permission:roles.view')
            ->name('roles.show');

        Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:roles.edit')
            ->name('roles.edit');

        Route::put('/roles/{id}', [RoleController::class, 'update'])
            ->middleware('permission:roles.edit')
            ->name('roles.update');

        Route::patch('/roles/{id}', [RoleController::class, 'update'])
            ->middleware('permission:roles.edit')
            ->name('roles.update.patch');

        Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles.delete')
            ->name('roles.destroy');

        Route::get('/roles/{id}/status', [RoleController::class, 'status'])
            ->middleware('permission:roles.status')
            ->name('roles.status');

        Route::get('/roles/{id}/restore', [RoleController::class, 'restore'])
            ->middleware('permission:roles.restore')
            ->name('roles.restore');

        Route::resource('permissions', PermissionController::class)
            ->except(['show'])
            ->middleware('permission:permissions.view');

        Route::get('/permissions/create', [PermissionController::class, 'create'])
            ->middleware('permission:permissions.create')
            ->name('permissions.create');

        Route::post('/permissions', [PermissionController::class, 'store'])
            ->middleware('permission:permissions.create')
            ->name('permissions.store');

        Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])
            ->middleware('permission:permissions.edit')
            ->name('permissions.edit');

        Route::put('/permissions/{id}', [PermissionController::class, 'update'])
            ->middleware('permission:permissions.edit')
            ->name('permissions.update');

        Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])
            ->middleware('permission:permissions.delete')
            ->name('permissions.destroy');

        Route::get('/permissions/{id}/status', [PermissionController::class, 'status'])
            ->middleware('permission:permissions.status')
            ->name('permissions.status');

        Route::get('/leads', [LeadController::class, 'index'])
            ->middleware('permission:leads.view')
            ->name('leads.index');

        Route::get('/leads/create', [LeadController::class, 'create'])
            ->middleware('permission:leads.create')
            ->name('leads.create');

        Route::post('/leads', [LeadController::class, 'store'])
            ->middleware('permission:leads.create')
            ->name('leads.store');

        Route::get('/leads/{lead}', [LeadController::class, 'show'])
            ->middleware('permission:leads.view')
            ->name('leads.show');

        Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])
            ->middleware('permission:leads.edit')
            ->name('leads.edit');

        Route::put('/leads/{lead}', [LeadController::class, 'update'])
            ->middleware('permission:leads.edit')
            ->name('leads.update');

        Route::patch('/leads/{lead}', [LeadController::class, 'update'])
            ->middleware('permission:leads.edit')
            ->name('leads.update.patch');

        Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])
            ->middleware('permission:leads.delete')
            ->name('leads.destroy');

        Route::post('/leads/{lead}/status', [LeadController::class, 'changeStatus'])
            ->middleware('permission:leads.status')
            ->name('leads.changeStatus');

        Route::get('/leads-trash', [LeadController::class, 'trash'])
            ->middleware('permission:leads.view')
            ->name('leads.trash');

        Route::post('/leads/{id}/restore', [LeadController::class, 'restore'])
            ->middleware('permission:leads.restore')
            ->name('leads.restore');

        Route::delete('/leads/{id}/force-delete', [LeadController::class, 'forceDelete'])
            ->middleware('permission:leads.force-delete')
            ->name('leads.forceDelete');

        Route::post('/leads/import', [LeadController::class, 'import'])
            ->middleware('permission:leads.import')
            ->name('leads.import');

        Route::get('/clients', [ClientController::class, 'index'])
            ->middleware('permission:clients.view')
            ->name('clients.index');

        Route::get('/clients/create', [ClientController::class, 'create'])
            ->middleware('permission:clients.create')
            ->name('clients.create');

        Route::post('/clients', [ClientController::class, 'store'])
            ->middleware('permission:clients.create')
            ->name('clients.store');

        Route::get('/clients/{client}', [ClientController::class, 'show'])
            ->middleware('permission:clients.view')
            ->name('clients.show');

        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])
            ->middleware('permission:clients.edit')
            ->name('clients.edit');

        Route::put('/clients/{client}', [ClientController::class, 'update'])
            ->middleware('permission:clients.edit')
            ->name('clients.update');

        Route::patch('/clients/{client}', [ClientController::class, 'update'])
            ->middleware('permission:clients.edit')
            ->name('clients.update.patch');

        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])
            ->middleware('permission:clients.delete')
            ->name('clients.destroy');

        Route::post('/clients/import', [ClientController::class, 'import'])
            ->middleware('permission:clients.import')
            ->name('clients.import');

        Route::get('/clients-trash', [ClientController::class, 'trash'])
            ->middleware('permission:clients.view')
            ->name('clients.trash');

        Route::post('/clients/{id}/restore', [ClientController::class, 'restore'])
            ->middleware('permission:clients.restore')
            ->name('clients.restore');

        Route::delete('/clients/{id}/force-delete', [ClientController::class, 'forceDelete'])
            ->middleware('permission:clients.force-delete')
            ->name('clients.forceDelete');

        Route::patch('/clients/{id}/status', [ClientController::class, 'changeStatus'])
            ->middleware('permission:clients.status')
            ->name('clients.changeStatus');

        Route::resource('followups', FollowUpController::class)
            ->middleware('permission:followups.view');

        Route::resource('projects', ProjectController::class)
            ->middleware('permission:projects.view');

        Route::resource('quotations', QuotationController::class)
            ->middleware('permission:quotations.view');

        Route::resource('invoices', InvoiceController::class)
            ->middleware('permission:invoices.view');

        Route::resource('payments', PaymentController::class)
            ->middleware('permission:payments.view');

        Route::get('/reports/leads', [ReportController::class, 'leadReport'])
            ->middleware('permission:reports.leads')
            ->name('reports.leads');

        Route::get('/reports/sales', [ReportController::class, 'salesReport'])
            ->middleware('permission:reports.sales')
            ->name('reports.sales');

        Route::get('/reports/users', [ReportController::class, 'userReport'])
            ->middleware('permission:reports.users')
            ->name('reports.users');

        Route::get('/settings/company', [SettingController::class, 'company'])
            ->middleware('permission:settings.company')
            ->name('settings.company');

        Route::post('/settings/company', [SettingController::class, 'updateCompany'])
            ->middleware('permission:settings.company.update')
            ->name('settings.company.update');

        Route::get('/profile', [ProfileController::class, 'index'])
            ->middleware('permission:profile.view')
            ->name('profile');

        Route::post('/profile', [ProfileController::class, 'update'])
            ->middleware('permission:profile.edit')
            ->name('profile.update');
    });