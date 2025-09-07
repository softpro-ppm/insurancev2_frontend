<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_name',
        'report_type',
        'start_date',
        'end_date',
        'filters',
        'status',
        'generated_by',
        'file_path'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'filters' => 'array',
    ];

    // Scopes
    public function scopeGenerated($query)
    {
        return $query->where('status', 'Generated');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'Failed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    // Accessors
    public function getFormattedDateRangeAttribute()
    {
        return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('M d, Y h:i A');
    }

    public function getFileSizeAttribute()
    {
        if ($this->file_path && file_exists(storage_path('app/' . $this->file_path))) {
            return number_format(filesize(storage_path('app/' . $this->file_path)) / 1024, 2) . ' KB';
        }
        return 'N/A';
    }
}
