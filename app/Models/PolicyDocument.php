<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
        'description',
        'is_latest'
    ];

    protected $casts = [
        'is_latest' => 'boolean',
    ];

    /**
     * Get the policy that owns the document.
     */
    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }

    /**
     * Get the latest document for a specific type
     */
    public function scopeLatest($query, $documentType = null)
    {
        if ($documentType) {
            return $query->where('document_type', $documentType)->where('is_latest', true);
        }
        return $query->where('is_latest', true);
    }

    /**
     * Get all documents for a specific type (including history)
     */
    public function scopeOfType($query, $documentType)
    {
        return $query->where('document_type', $documentType);
    }
}
