<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PolicyVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'version_number',
        'policy_type',
        'business_type',
        'customer_name',
        'phone',
        'email',
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
        'policy_copy_path',
        'rc_copy_path',
        'aadhar_copy_path',
        'pan_copy_path',
        'notes',
        'created_by',
        'version_created_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'premium' => 'decimal:2',
        'payout' => 'decimal:2',
        'customer_paid_amount' => 'decimal:2',
        'revenue' => 'decimal:2',
        'version_created_at' => 'datetime',
    ];

    /**
     * Get the policy that owns this version
     */
    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }

    /**
     * Get the next version number for a policy
     */
    public static function getNextVersionNumber($policyId)
    {
        return self::where('policy_id', $policyId)->max('version_number') + 1;
    }

    /**
     * Create a version from current policy data
     */
    public static function createFromPolicy(Policy $policy, $notes = null, $createdBy = null)
    {
        return self::create([
            'policy_id' => $policy->id,
            'version_number' => self::getNextVersionNumber($policy->id),
            'policy_type' => $policy->policy_type,
            'business_type' => $policy->business_type,
            'customer_name' => $policy->customer_name,
            'phone' => $policy->phone,
            'email' => $policy->email,
            'vehicle_number' => $policy->vehicle_number,
            'vehicle_type' => $policy->vehicle_type,
            'company_name' => $policy->company_name,
            'insurance_type' => $policy->insurance_type,
            'start_date' => $policy->start_date,
            'end_date' => $policy->end_date,
            'premium' => $policy->premium,
            'payout' => $policy->payout,
            'customer_paid_amount' => $policy->customer_paid_amount,
            'revenue' => $policy->revenue,
            'status' => $policy->status,
            'policy_copy_path' => $policy->policy_copy_path,
            'rc_copy_path' => $policy->rc_copy_path,
            'aadhar_copy_path' => $policy->aadhar_copy_path,
            'pan_copy_path' => $policy->pan_copy_path,
            'notes' => $notes,
            'created_by' => $createdBy,
            'version_created_at' => now(),
        ]);
    }

    /**
     * Get formatted version label
     */
    public function getVersionLabelAttribute()
    {
        return "Version {$this->version_number} ({$this->version_created_at->format('M Y')})";
    }

    /**
     * Get policy period in human readable format
     */
    public function getPolicyPeriodAttribute()
    {
        return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
    }

    /**
     * Check if this version has documents that actually exist on disk
     */
    public function hasDocuments()
    {
        $documents = $this->getDocuments();
        foreach ($documents as $path) {
            if ($path && $this->documentExists($path)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all document paths as array, but only include existing files
     */
    public function getDocuments()
    {
        $documents = [
            'policy_copy' => $this->policy_copy_path,
            'rc_copy' => $this->rc_copy_path,
            'aadhar_copy' => $this->aadhar_copy_path,
            'pan_copy' => $this->pan_copy_path,
        ];
        
        // Filter out non-existent files
        return array_filter($documents, function($path) {
            return $path && $this->documentExists($path);
        });
    }

    /**
     * Check if a document file actually exists on disk
     */
    private function documentExists($filePath)
    {
        if (!$filePath) return false;
        
        return file_exists(storage_path('app/' . $filePath));
    }
}