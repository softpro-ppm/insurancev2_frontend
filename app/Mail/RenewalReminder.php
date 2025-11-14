<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Policy;

class RenewalReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $policy;
    public $daysUntilExpiry;
    public $agentName;
    public $agentPhone;
    public $agentEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(Policy $policy, $daysUntilExpiry, $agentName, $agentPhone, $agentEmail)
    {
        $this->policy = $policy;
        $this->daysUntilExpiry = $daysUntilExpiry;
        $this->agentName = $agentName;
        $this->agentPhone = $agentPhone;
        $this->agentEmail = $agentEmail;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Renewal Reminder - ' . $this->policy->company_name)
                    ->view('emails.renewal-reminder')
                    ->with([
                        'policy' => $this->policy,
                        'daysUntilExpiry' => $this->daysUntilExpiry,
                        'agentName' => $this->agentName,
                        'agentPhone' => $this->agentPhone,
                        'agentEmail' => $this->agentEmail,
                    ]);
    }
}
