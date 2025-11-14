<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'recipient_type',
        'recipient_id',
        'status',
        'scheduled_at',
        'sent_at',
        'sent_by'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'Sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'Failed');
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
                    ->where('scheduled_at', '>', now())
                    ->where('status', 'Pending');
    }

    public function scopeForRecipient($query, $recipientType, $recipientId = null)
    {
        return $query->where('recipient_type', $recipientType)
                    ->when($recipientId, function($q) use ($recipientId) {
                        return $q->where('recipient_id', $recipientId);
                    });
    }

    // Accessors
    public function getIsScheduledAttribute()
    {
        return $this->scheduled_at && $this->scheduled_at > now();
    }

    public function getIsOverdueAttribute()
    {
        return $this->scheduled_at && $this->scheduled_at < now() && $this->status === 'Pending';
    }

    public function getFormattedScheduledAtAttribute()
    {
        return $this->scheduled_at ? $this->scheduled_at->format('M d, Y h:i A') : null;
    }

    public function getFormattedSentAtAttribute()
    {
        return $this->sent_at ? $this->sent_at->format('M d, Y h:i A') : null;
    }
}
