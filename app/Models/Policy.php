<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_number',
        'customer_name',
        'phone',
        'email',
        'policy_type',
        'vehicle_number',
        'vehicle_type',
        'company_name',
        'insurance_type',
        'start_date',
        'end_date',
        'premium',
        'payout',
        'customer_paid_amount',
        'revenue',
        'status',
        'business_type',
        'agent_name',
        'policy_copy_path',
        'rc_copy_path',
        'aadhar_copy_path',
        'pan_copy_path'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'premium' => 'decimal:2',
        'payout' => 'decimal:2',
        'customer_paid_amount' => 'decimal:2',
        'revenue' => 'decimal:2',
    ];

    // Relationships
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_name', 'name');
    }

    public function renewals()
    {
        return $this->hasMany(Renewal::class, 'policy_number', 'policy_number');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('end_date', '<=', now()->addDays($days))
                    ->where('end_date', '>=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('policy_type', $type);
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        return $this->end_date < now();
    }

    public function getDaysUntilExpiryAttribute()
    {
        return now()->diffInDays($this->end_date, false);
    }

    public function getFormattedPremiumAttribute()
    {
        return '₹' . number_format($this->premium, 2);
    }

    public function getFormattedRevenueAttribute()
    {
        return '₹' . number_format($this->revenue, 2);
    }
}
