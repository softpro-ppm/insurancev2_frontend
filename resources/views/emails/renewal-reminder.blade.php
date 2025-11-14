<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Renewal Reminder - {{ $policy->company_name }}</title>
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
            background: linear-gradient(135deg, #10B981, #059669);
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
        .reminder-box {
            background: #d1fae5;
            border: 1px solid #10b981;
            padding: 20px;
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
        <h1>🔄 Renewal Reminder</h1>
        <p>Your Policy is Due for Renewal</p>
    </div>
    
    <div class="content">
        <h2>Dear {{ $policy->customer_name }},</h2>
        
        <p>We hope you're doing well! This is a gentle reminder about your upcoming policy renewal.</p>
        
        <div class="reminder-box">
            <h3>📅 Renewal Details:</h3>
            <ul>
                <li><strong>Policy Type:</strong> {{ $policy->policy_type }}</li>
                <li><strong>Company:</strong> {{ $policy->company_name }}</li>
                <li><strong>Current Expiry:</strong> {{ $policy->end_date->format('M d, Y') }}</li>
                <li><strong>Days Until Expiry:</strong> {{ $daysUntilExpiry }} days</li>
                <li><strong>Current Premium:</strong> ₹{{ number_format($policy->premium, 2) }}</li>
            </ul>
        </div>
        
        <h3>Why Renew with Us?</h3>
        <ul>
            <li>✅ Competitive renewal rates</li>
            <li>✅ Seamless renewal process</li>
            <li>✅ No paperwork hassles</li>
            <li>✅ Dedicated customer support</li>
            <li>✅ Quick claim processing</li>
        </ul>
        
        <h3>Renewal Options:</h3>
        <ul>
            <li><strong>Online Renewal:</strong> Quick and convenient</li>
            <li><strong>Phone Renewal:</strong> Call us for assistance</li>
            <li><strong>In-Person:</strong> Visit our office</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="tel:{{ $agentPhone }}" class="cta-button">📞 Call to Renew</a>
        </div>
        
        <p>We're here to make your renewal process as smooth as possible. Our team is ready to assist you with any questions or special requirements.</p>
        
        <p>Thank you for your continued trust in our services.</p>
        
        <p>Best regards,<br>
        <strong>{{ $agentName }}</strong><br>
        Insurance Agent<br>
        Phone: {{ $agentPhone }}<br>
        Email: {{ $agentEmail }}</p>
    </div>
    
    <div class="footer">
        <p>This is an automated reminder. Please contact us for any assistance.</p>
        <p>© {{ date('Y') }} {{ $policy->company_name }}. All rights reserved.</p>
    </div>
</body>
</html>
