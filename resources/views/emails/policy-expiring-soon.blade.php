<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Policy Expiring Soon - {{ $policy->company_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .highlight {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            background: #10B981;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔔 Policy Expiring Soon</h1>
        <p>Important Renewal Notice</p>
    </div>
    
    <div class="content">
        <h2>Dear {{ $policy->customer_name }},</h2>
        
        <p>We hope this message finds you well. This is a friendly reminder that your insurance policy is expiring soon.</p>
        
        <div class="highlight">
            <h3>📋 Policy Details:</h3>
            <ul>
                <li><strong>Policy Type:</strong> {{ $policy->policy_type }}</li>
                <li><strong>Company:</strong> {{ $policy->company_name }}</li>
                <li><strong>Expiry Date:</strong> {{ $policy->end_date->format('M d, Y') }}</li>
                <li><strong>Days Remaining:</strong> {{ $daysUntilExpiry }} days</li>
                <li><strong>Current Premium:</strong> ₹{{ number_format($policy->premium, 2) }}</li>
            </ul>
        </div>
        
        <p>To ensure continuous coverage and avoid any lapse in your insurance protection, we recommend renewing your policy before the expiry date.</p>
        
        <h3>Why Renew Early?</h3>
        <ul>
            <li>✅ Maintain continuous coverage</li>
            <li>✅ Avoid policy lapse penalties</li>
            <li>✅ Secure the best renewal rates</li>
            <li>✅ Peace of mind for you and your family</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="tel:{{ $agentPhone }}" class="cta-button">📞 Call Now to Renew</a>
        </div>
        
        <p>Our team is ready to assist you with the renewal process. Please don't hesitate to contact us if you have any questions or need assistance.</p>
        
        <p>Thank you for choosing {{ $policy->company_name }} for your insurance needs.</p>
        
        <p>Best regards,<br>
        <strong>{{ $agentName }}</strong><br>
        Insurance Agent<br>
        Phone: {{ $agentPhone }}<br>
        Email: {{ $agentEmail }}</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} {{ $policy->company_name }}. All rights reserved.</p>
    </div>
</body>
</html>
