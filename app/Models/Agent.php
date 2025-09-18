<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Agent extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'user_id',
        'status',
        'policies_count',
        'performance',
        'address',
        'password'
    ];

    protected $casts = [
        'performance' => 'decimal:2',
        'policies_count' => 'integer',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relationships
    public function policies()
    {
        return $this->hasMany(Policy::class, 'agent_name', 'name');
    }

    public function renewals()
    {
        return $this->hasMany(Renewal::class, 'agent_name', 'name');
    }

    public function followups()
    {
        return $this->hasMany(Followup::class, 'agent_name', 'name');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }

    // Accessors
    public function getPerformancePercentageAttribute()
    {
        return $this->performance . '%';
    }

    // Authentication methods
    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function getAuthIdentifier()
    {
        return $this->email;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
