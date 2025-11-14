<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

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
        'policy_issue_date', // NEW: When policy was issued by agent
        'start_date',
        'end_date',
        'premium',
        'payout',
        'customer_paid_amount',
        'revenue',
        'status',
        'is_renewed', // NEW: Yes/No flag for renewal tracking
        'business_type',
        'agent_name',
        'policy_copy_path',
        'rc_copy_path',
        'aadhar_copy_path',
        'pan_copy_path',
        // Health/Life
        'customer_age',
        'customer_gender',
        'sum_insured',
        'sum_assured',
        'policy_term',
        'premium_frequency'
    ];

    protected $casts = [
        'policy_issue_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'premium' => 'decimal:2',
        'payout' => 'decimal:2',
        'customer_paid_amount' => 'decimal:2',
        'revenue' => 'decimal:2',
        'sum_insured' => 'decimal:2',
        'sum_assured' => 'decimal:2',
    ];

    public const VEHICLE_TYPE_REMAP = [
        'Auto (Goods)' => 'Auto (G)',
        'Auto (Passenger)' => 'Auto',
        'Car (Commercial)' => 'Car (Taxi)',
        'Car (Private)' => 'Car',
        'E-Rickshaw' => 'E-Auto',
        'Electric Car' => 'E-Car',
        'HGV (Goods)' => 'HGV',
        'LCV (Goods)' => 'LCV',
        'Others / Misc.' => 'Others',
        'Private Car' => 'Car',
        'School Bus' => 'Bus',
        'Commercial Vehicle' => 'LCV',
        'Taxi' => 'Car (Taxi)',
        'Truck' => 'HGV',
        'Other' => 'Others',
        'Two-Wheeler' => '2-Wheeler',
        'Four-Wheeler' => 'Car'
    ];

    public static function normalizeVehicleType(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        return self::VEHICLE_TYPE_REMAP[$trimmed] ?? $trimmed;
    }

    public function getVehicleTypeAttribute($value)
    {
        return self::normalizeVehicleType($value);
    }

    public function setVehicleTypeAttribute($value)
    {
        $this->attributes['vehicle_type'] = self::normalizeVehicleType($value);
    }

    protected static function boot()
    {
        parent::boot();

        static::retrieved(function (Policy $policy) {
            $policy->refreshStatusIfExpired();
        });

        static::saving(function (Policy $policy) {
            $policy->refreshStatusIfExpired();

            // Enforce unique vehicle number (canonicalized) at application level
            if (!empty($policy->vehicle_number)) {
                $canonical = self::canonicalizeVehicleNumber($policy->vehicle_number);
                if (!empty($canonical)) {
                    $duplicateExists = Policy::where('id', '!=', $policy->id)
                        ->get()
                        ->contains(function ($existing) use ($canonical) {
                            return self::canonicalizeVehicleNumber($existing->vehicle_number) === $canonical;
                        });
                    if ($duplicateExists) {
                        throw new \RuntimeException('Duplicate vehicle number not allowed');
                    }
                }
            }
        });
    }

    public function refreshStatusIfExpired(): void
    {
        if ($this->end_date instanceof Carbon) {
            $today = Carbon::today();
            $thirtyDaysFromNow = Carbon::today()->addDays(30);
            
            if ($this->end_date->lt($today)) {
                // Policy expired before today
                $this->status = 'Expired';
            } elseif ($this->end_date->between($today, $thirtyDaysFromNow)) {
                // Policy expiring today or within next 30 days
                $this->status = 'Pending';
            } else {
                // Policy expires more than 30 days from now
                $this->status = 'Active';
            }
        }
    }

    // Relationships
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_name', 'name');
    }

    public function versions()
    {
        return $this->hasMany(PolicyVersion::class)->orderBy('version_number', 'desc');
    }

    public function latestVersion()
    {
        return $this->hasOne(PolicyVersion::class)->latestOfMany('version_number');
    }

    public function firstVersion()
    {
        return $this->hasOne(PolicyVersion::class)->oldestOfMany('version_number');
    }



    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'Expired');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        $today = Carbon::today();
        $futureDate = Carbon::today()->addDays($days);
        return $query->where('end_date', '<=', $futureDate)
                    ->where('end_date', '>=', $today);
    }

    public function scopeByType($query, $type)
    {
        if ($type && $type !== 'All') {
            return $query->where('policy_type', $type);
        }
        return $query;
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
        $value = (float) $this->premium;
        return '₹' . number_format($value, 0, '.', ',');
    }

    public function getFormattedRevenueAttribute()
    {
        $value = (float) $this->revenue;
        return '₹' . number_format($value, 0, '.', ',');
    }

    private static function canonicalizeVehicleNumber(?string $value): string
    {
        $value = (string) $value;
        $value = strtoupper($value);
        return preg_replace('/[^A-Z0-9]/', '', $value);
    }
}
