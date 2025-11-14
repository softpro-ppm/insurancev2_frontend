<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Policy;

class ThankYouRenewal extends Mailable
{
    use Queueable, SerializesModels;

    public $policy;
    public $newEndDate;
    public $renewalPremium;
    public $agentName;
    public $agentPhone;
    public $agentEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(Policy $policy, $newEndDate, $renewalPremium, $agentName, $agentPhone, $agentEmail)
    {
        $this->policy = $policy;
        $this->newEndDate = $newEndDate;
        $this->renewalPremium = $renewalPremium;
        $this->agentName = $agentName;
        $this->agentPhone = $agentPhone;
        $this->agentEmail = $agentEmail;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Thank You for Renewal - ' . $this->policy->company_name)
                    ->view('emails.thank-you-renewal')
                    ->with([
                        'policy' => $this->policy,
                        'newEndDate' => $this->newEndDate,
                        'renewalPremium' => $this->renewalPremium,
                        'agentName' => $this->agentName,
                        'agentPhone' => $this->agentPhone,
                        'agentEmail' => $this->agentEmail,
                    ]);
    }
}
