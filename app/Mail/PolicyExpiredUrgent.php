<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Policy;

class PolicyExpiredUrgent extends Mailable
{
    use Queueable, SerializesModels;

    public $policy;
    public $daysSinceExpiry;
    public $agentName;
    public $agentPhone;
    public $agentEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(Policy $policy, $daysSinceExpiry, $agentName, $agentPhone, $agentEmail)
    {
        $this->policy = $policy;
        $this->daysSinceExpiry = $daysSinceExpiry;
        $this->agentName = $agentName;
        $this->agentPhone = $agentPhone;
        $this->agentEmail = $agentEmail;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('URGENT: Policy Expired - ' . $this->policy->company_name)
                    ->view('emails.policy-expired-urgent')
                    ->with([
                        'policy' => $this->policy,
                        'daysSinceExpiry' => $this->daysSinceExpiry,
                        'agentName' => $this->agentName,
                        'agentPhone' => $this->agentPhone,
                        'agentEmail' => $this->agentEmail,
                    ]);
    }
}
