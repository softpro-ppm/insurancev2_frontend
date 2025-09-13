<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Renewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone',
        'email',
        'policy_type',
        'current_premium',
        'renewal_premium',
        'due_date',
        'status',
        'agent_name',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'current_premium' => 'decimal:2',
        'renewal_premium' => 'decimal:2',
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

    public function scopeOverdue($query)
    {
        return $query->where('status', 'Overdue');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'Scheduled');
    }

    public function scopeDueSoon($query, $days = 30)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
                    ->where('due_date', '>=', now())
                    ->where('status', 'Pending');
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && $this->status !== 'Completed';
    }

    public function getDaysUntilDueAttribute()
    {
        return now()->diffInDays($this->due_date, false);
    }

    public function getPremiumDifferenceAttribute()
    {
        return $this->renewal_premium - $this->current_premium;
    }

    public function getFormattedCurrentPremiumAttribute()
    {
        $value = (float) $this->current_premium;
        return '₹' . number_format($value, 0, '.', ',');
    }

    public function getFormattedRenewalPremiumAttribute()
    {
        $value = (float) $this->renewal_premium;
        return '₹' . number_format($value, 0, '.', ',');
    }
}
