<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'company_id',
        'email',
        'phone',
        'profile_picture',
        'user_id'
    ];
    
    /**
     * Get the company that owns the employee.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    /**
     * Get the user account for this employee (if they are an admin).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Check if this employee is an admin.
     */
    public function isAdmin()
    {
        return $this->user_id !== null && $this->user && $this->user->is_admin;
    }
}
