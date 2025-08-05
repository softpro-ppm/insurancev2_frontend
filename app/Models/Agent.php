<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agent extends Model
{
    use HasFactory;

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
    ];

    protected $hidden = [
        'password',
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

    // Mutators
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
