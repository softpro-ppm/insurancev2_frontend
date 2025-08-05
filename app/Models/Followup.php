<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Followup extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone',
        'email',
        'policy_type',
        'followup_type',
        'followup_date',
        'followup_time',
        'status',
        'agent_name',
        'notes',
        'outcome'
    ];

    protected $casts = [
        'followup_date' => 'date',
        'followup_time' => 'datetime',
    ];

    // Relationships
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_name', 'name');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'Cancelled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('followup_date', today());
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('followup_date', '>=', today())
                    ->where('followup_date', '<=', today()->addDays($days))
                    ->where('status', 'Pending');
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        return $this->followup_date < today() && $this->status === 'Pending';
    }

    public function getFormattedDateTimeAttribute()
    {
        return $this->followup_date->format('M d, Y') . ' at ' . $this->followup_time->format('h:i A');
    }

    public function getDaysUntilFollowupAttribute()
    {
        return today()->diffInDays($this->followup_date, false);
    }
}
