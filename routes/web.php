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

Route::get('/', [LoginController::class, 'showLogin'])->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('loginDashboard');

Route::post('/logout', [LoginController::class, 'logout'])
->middleware('auth')
->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('permission:Dashboard View')
    ->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])
    ->middleware('permission:Users View')
    ->name('users.index');

    Route::get('/users/create', [UserController::class, 'create'])
    ->middleware('permission:Users Create')
    ->name('users.create');

    Route::post('/users', [UserController::class, 'store'])
    ->middleware('permission:Users Create')
    ->name('users.store');

    Route::get('/users/{id}', [UserController::class, 'show'])
    ->middleware('permission:Users View')
    ->name('users.show');

    Route::get('/users/{id}/edit', [UserController::class, 'edit'])
    ->middleware('permission:Users Edit')
    ->name('users.edit');

    Route::put('/users/{id}', [UserController::class, 'update'])
    ->middleware('permission:Users Edit')
    ->name('users.update');

    Route::patch('/users/{id}', [UserController::class, 'update'])
    ->middleware('permission:Users Edit')
    ->name('users.update.patch');

    Route::patch('/users/{id}/status', [UserController::class, 'changeStatus'])
    ->middleware('permission:Users Edit')
    ->name('users.statu');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])
    ->middleware('permission:Users Delete')
    ->name('users.destroy');

    Route::patch('/users/{id}/restore', [UserController::class, 'restore'])
    ->middleware('permission:Users Restore')
    ->name('users.restore');

    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])
    ->middleware('permission:Users Force Delete')
    ->name('users.forceDelete');

    Route::get('/roles', [RoleController::class, 'index'])
    ->middleware('permission:Roles View')
    ->name('roles.index');

    Route::get('/roles/create', [RoleController::class, 'create'])
    ->middleware('permission:Roles Create')
    ->name('roles.create');

    Route::post('/roles', [RoleController::class, 'store'])
    ->middleware('permission:Roles Create')
    ->name('roles.store');

    Route::get('/roles/{id}', [RoleController::class, 'show'])
    ->middleware('permission:Roles View')
    ->name('roles.show');

    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])
    ->middleware('permission:Roles Edit')
    ->name('roles.edit');

    Route::put('/roles/{id}', [RoleController::class, 'update'])
    ->middleware('permission:Roles Edit')
    ->name('roles.update');

    Route::patch('/roles/{id}', [RoleController::class, 'update'])
    ->middleware('permission:Roles Edit')
    ->name('roles.update.patch');

    Route::patch('/roles/{id}/status', [RoleController::class, 'changeStatus'])
    ->middleware('permission:Roles Edit')
    ->name('roles.status');

    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
    ->middleware('permission:Roles Delete')
    ->name('roles.destroy');

    Route::get('/permissions', [PermissionController::class, 'index'])
    ->middleware('permission:Roles View')
    ->name('permissions.index');

    Route::get('/permissions/create', [PermissionController::class, 'create'])
    ->middleware('permission:Roles Create')
    ->name('permissions.create');

    Route::post('/permissions', [PermissionController::class, 'store'])
    ->middleware('permission:Roles Create')
    ->name('permissions.store');

    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])
    ->middleware('permission:Roles Edit')
    ->name('permissions.edit');

    Route::put('/permissions/{id}', [PermissionController::class, 'update'])
    ->middleware('permission:Roles Edit')
    ->name('permissions.update');

    Route::patch('/permissions/{id}/status', [PermissionController::class, 'changeStatus'])
    ->middleware('permission:Roles Edit')
    ->name('permissions.status');

    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])
    ->middleware('permission:Roles Delete')
    ->name('permissions.destroy');

    Route::get('/leads', [LeadController::class, 'index'])
    ->middleware('permission:Leads View')
    ->name('leads.index');

    Route::get('/leads/create', [LeadController::class, 'create'])
    ->middleware('permission:Leads Create')
    ->name('leads.create');

    Route::post('/leads', [LeadController::class, 'store'])
    ->middleware('permission:Leads Create')
    ->name('leads.store');

    Route::post('/leads/import', [LeadController::class, 'import'])
    ->middleware('permission:Leads Create')
    ->name('leads.import');

    Route::get('/leads/{lead}', [LeadController::class, 'show'])
    ->middleware('permission:Leads View')
    ->name('leads.show');

    Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])
    ->middleware('permission:Leads Edit')
    ->name('leads.edit');

    Route::put('/leads/{lead}', [LeadController::class, 'update'])
    ->middleware('permission:Leads Edit')
    ->name('leads.update');

    Route::patch('/leads/{lead}', [LeadController::class, 'update'])
    ->middleware('permission:Leads Edit')
    ->name('leads.update.patch');

    Route::patch('/leads/{lead}/status', [LeadController::class, 'changeStatus'])
    ->middleware('permission:Leads Edit')
    ->name('leads.changeStatus');

    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])
    ->middleware('permission:Leads Delete')
    ->name('leads.destroy');

    Route::get('/leads-trash', [LeadController::class, 'trash'])
    ->middleware('permission:Leads Delete')
    ->name('leads.trash');

    Route::patch('/leads/{id}/restore', [LeadController::class, 'restore'])
    ->middleware('permission:Leads Delete')
    ->name('leads.restore');

    Route::delete('/leads/{id}/force-delete', [LeadController::class, 'forceDelete'])
    ->middleware('permission:Leads Delete')
    ->name('leads.forceDelete');

    Route::get('/clients', [ClientController::class, 'index'])
    ->middleware('permission:Clients View')
    ->name('clients.index');

    Route::get('/clients/create', [ClientController::class, 'create'])
    ->middleware('permission:Clients Create')
    ->name('clients.create');

    Route::post('/clients', [ClientController::class, 'store'])
    ->middleware('permission:Clients Create')
    ->name('clients.store');

    Route::post('/clients/import', [ClientController::class, 'import'])
    ->middleware('permission:Clients Create')
    ->name('clients.import');

    Route::get('/clients/{client}', [ClientController::class, 'show'])
    ->middleware('permission:Clients View')
    ->name('clients.show');

    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])
    ->middleware('permission:Clients Edit')
    ->name('clients.edit');

    Route::put('/clients/{client}', [ClientController::class, 'update'])
    ->middleware('permission:Clients Edit')
    ->name('clients.update');

    Route::patch('/clients/{client}', [ClientController::class, 'update'])
    ->middleware('permission:Clients Edit')
    ->name('clients.update.patch');

    Route::patch('/clients/{client}/status', [ClientController::class, 'changeStatus'])
    ->middleware('permission:Clients Edit')
    ->name('clients.status');

    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])
    ->middleware('permission:Clients Delete')
    ->name('clients.destroy');

    Route::get('/clients-trash', [ClientController::class, 'trash'])
    ->middleware('permission:Clients Delete')
    ->name('clients.trash');

    Route::patch('/clients/{id}/restore', [ClientController::class, 'restore'])
    ->middleware('permission:Clients Delete')
    ->name('clients.restore');

    Route::delete('/clients/{id}/force-delete', [ClientController::class, 'forceDelete'])
    ->middleware('permission:Clients Delete')
    ->name('clients.forceDelete');

    Route::get('/followups', [FollowUpController::class, 'index'])
    ->middleware('permission:Follow Ups View')
    ->name('followups.index');

    Route::get('/followups/create', [FollowUpController::class, 'create'])
    ->middleware('permission:Follow Ups Create')
    ->name('followups.create');

    Route::post('/followups', [FollowUpController::class, 'store'])
    ->middleware('permission:Follow Ups Create')
    ->name('followups.store');

    Route::get('/followups/{followup}', [FollowUpController::class, 'show'])
    ->middleware('permission:Follow Ups View')
    ->name('followups.show');

    Route::get('/followups/{followup}/edit', [FollowUpController::class, 'edit'])
    ->middleware('permission:Follow Ups Edit')
    ->name('followups.edit');

    Route::put('/followups/{followup}', [FollowUpController::class, 'update'])
    ->middleware('permission:Follow Ups Edit')
    ->name('followups.update');

    Route::patch('/followups/{followup}', [FollowUpController::class, 'update'])
    ->middleware('permission:Follow Ups Edit')
    ->name('followups.update.patch');

    Route::patch('/followups/{followup}/status', [FollowUpController::class, 'changeStatus'])
    ->middleware('permission:Follow Ups Edit')
    ->name('followups.status');

    Route::delete('/followups/{followup}', [FollowUpController::class, 'destroy'])
    ->middleware('permission:Follow Ups Delete')
    ->name('followups.destroy');

    Route::get('/projects', [ProjectController::class, 'index'])
    ->name('projects.index');

    Route::get('/projects/create', [ProjectController::class, 'create'])
    ->name('projects.create');

    Route::post('/projects', [ProjectController::class, 'store'])
    ->name('projects.store');

    Route::get('/projects/{project}', [ProjectController::class, 'show'])
    ->name('projects.show');

    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])
    ->name('projects.edit');

    Route::put('/projects/{project}', [ProjectController::class, 'update'])
    ->name('projects.update');

    Route::patch('/projects/{project}', [ProjectController::class, 'update'])
    ->name('projects.update.patch');

    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
    ->name('projects.destroy');

    Route::prefix('quotations')->name('quotations.')->group(function () {

        Route::get('/', [QuotationController::class, 'index'])
        ->middleware('permission:Quotations View')
        ->name('index');

        Route::get('/create', [QuotationController::class, 'create'])
        ->middleware('permission:Quotations Create')
        ->name('create');

        Route::post('/', [QuotationController::class, 'store'])
        ->middleware('permission:Quotations Create')
        ->name('store');

        Route::get('/trash', [QuotationController::class, 'trash'])
        ->middleware('permission:Quotations Delete')
        ->name('trash');

        Route::get('/{quotation}', [QuotationController::class, 'show'])
        ->middleware('permission:Quotations View')
        ->name('show');

        Route::get('/{quotation}/edit', [QuotationController::class, 'edit'])
        ->middleware('permission:Quotations Edit')
        ->name('edit');

        Route::put('/{quotation}', [QuotationController::class, 'update'])
        ->middleware('permission:Quotations Edit')
        ->name('update');

        Route::patch('/{quotation}/status', [QuotationController::class, 'changeStatus'])
        ->middleware('permission:Quotations Edit')
        ->name('changeStatus');

        Route::delete('/{quotation}', [QuotationController::class, 'destroy'])
        ->middleware('permission:Quotations Delete')
        ->name('destroy');

        Route::patch('/{quotation}/restore', [QuotationController::class, 'restore'])
        ->middleware('permission:Quotations Delete')
        ->name('restore');

        Route::delete('/{quotation}/force-delete', [QuotationController::class, 'forceDelete'])
        ->middleware('permission:Quotations Delete')
        ->name('forceDelete');
    });

    Route::prefix('invoices')->name('invoices.')->group(function () {

        Route::get('/', [InvoiceController::class, 'index'])
        ->middleware('permission:Invoices View')
        ->name('index');

        Route::get('/create', [InvoiceController::class, 'create'])
        ->middleware('permission:Invoices Create')
        ->name('create');

        Route::post('/', [InvoiceController::class, 'store'])
        ->middleware('permission:Invoices Create')
        ->name('store');

        Route::get('/trash', [InvoiceController::class, 'trash'])
        ->middleware('permission:Invoices Delete')
        ->name('trash');

        Route::get('/{invoice}', [InvoiceController::class, 'show'])
        ->middleware('permission:Invoices View')
        ->name('show');

        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])
        ->middleware('permission:Invoices Edit')
        ->name('edit');

        Route::put('/{invoice}', [InvoiceController::class, 'update'])
        ->middleware('permission:Invoices Edit')
        ->name('update');

        Route::patch('/{invoice}/status', [InvoiceController::class, 'changeStatus'])
        ->middleware('permission:Invoices Edit')
        ->name('changeStatus');

        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])
        ->middleware('permission:Invoices Delete')
        ->name('destroy');

        Route::patch('/{invoice}/restore', [InvoiceController::class, 'restore'])
        ->middleware('permission:Invoices Delete')
        ->name('restore');

        Route::delete('/{invoice}/force-delete', [InvoiceController::class, 'forceDelete'])
        ->middleware('permission:Invoices Delete')
        ->name('forceDelete');
    });

    Route::get('/payments', [PaymentController::class, 'index'])
    ->middleware('permission:Payments View')
    ->name('payments.index');

    Route::get('/payments/create', [PaymentController::class, 'create'])
    ->middleware('permission:Payments Create')
    ->name('payments.create');

    Route::post('/payments', [PaymentController::class, 'store'])
    ->middleware('permission:Payments Create')
    ->name('payments.store');

    Route::get('/payments/{payment}', [PaymentController::class, 'show'])
    ->middleware('permission:Payments View')
    ->name('payments.show');

    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])
    ->middleware('permission:Payments Edit')
    ->name('payments.edit');

    Route::put('/payments/{payment}', [PaymentController::class, 'update'])
    ->middleware('permission:Payments Edit')
    ->name('payments.update');

    Route::patch('/payments/{payment}', [PaymentController::class, 'update'])
    ->middleware('permission:Payments Edit')
    ->name('payments.update.patch');

    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
    ->middleware('permission:Payments Delete')
    ->name('payments.destroy');

    Route::get('/payments/trash', [PaymentController::class, 'trash'])
    ->middleware('permission:Payments Delete')
    ->name('payments.trash');

    Route::patch('/payments/{payment}/restore', [PaymentController::class, 'restore'])
    ->middleware('permission:Payments Delete')
    ->name('payments.restore');

    Route::delete('/payments/{payment}/force-delete', [PaymentController::class, 'forceDelete'])
    ->middleware('permission:Payments Delete')
    ->name('payments.forceDelete');

    Route::get('/reports/leads', [ReportController::class, 'leadReport'])
    ->middleware('permission:Reports View')
    ->name('reports.leads');

    Route::get('/reports/sales', [ReportController::class, 'salesReport'])
    ->middleware('permission:Reports View')
    ->name('reports.sales');

    Route::get('/reports/users', [ReportController::class, 'userReport'])
    ->middleware('permission:Reports View')
    ->name('reports.users');

    Route::get('/settings/company', [SettingController::class, 'company'])
    ->middleware('permission:Settings View')
    ->name('settings.company');

    Route::post('/settings/company', [SettingController::class, 'updateCompany'])
    ->middleware('permission:Settings Edit')
    ->name('settings.company.update');

    Route::get('/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');

    Route::post('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');

    Route::put('/profile', [ProfileController::class, 'update'])
    ->name('profile.update.put');
});
