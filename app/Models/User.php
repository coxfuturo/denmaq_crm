<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    protected $guard_name = 'web';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password',
        'type',
        'is_admin',
        'status',
        'company_name',
        'profile_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function assignedLeads()
    {
        return $this->hasMany(
            Lead::class,
            'assigned_to'
        );
    }

    public function createdLeads()
    {
        return $this->hasMany(
            Lead::class,
            'created_by'
        );
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'created_by');
    }
}
