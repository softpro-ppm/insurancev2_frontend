<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>URGENT: Policy Expired - {{ $policy->company_name }}</title>
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
            background: linear-gradient(135deg, #EF4444, #DC2626);
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
        .urgent-notice {
            background: #fee2e2;
            border: 2px solid #ef4444;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button {
            display: inline-block;
            background: #EF4444;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
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
        <h1>🚨 URGENT: Policy Expired</h1>
        <p>Immediate Action Required</p>
    </div>
    
    <div class="content">
        <h2>Dear {{ $policy->customer_name }},</h2>
        
        <div class="urgent-notice">
            <h3>⚠️ URGENT NOTICE</h3>
            <p><strong>Your insurance policy has EXPIRED on {{ $policy->end_date->format('M d, Y') }}</strong></p>
            <p>You are currently without insurance coverage!</p>
        </div>
        
        <h3>📋 Expired Policy Details:</h3>
        <ul>
            <li><strong>Policy Type:</strong> {{ $policy->policy_type }}</li>
            <li><strong>Company:</strong> {{ $policy->company_name }}</li>
            <li><strong>Expiry Date:</strong> {{ $policy->end_date->format('M d, Y') }}</li>
            <li><strong>Days Since Expiry:</strong> {{ $daysSinceExpiry }} days</li>
            <li><strong>Previous Premium:</strong> ₹{{ number_format($policy->premium, 2) }}</li>
        </ul>
        
        <h3>⚠️ Important Consequences:</h3>
        <ul>
            <li>❌ No insurance coverage in case of accidents</li>
            <li>❌ Legal penalties for driving without insurance</li>
            <li>❌ Higher premiums for lapsed policies</li>
            <li>❌ No claims can be processed</li>
        </ul>
        
        <p><strong>We strongly recommend immediate renewal to restore your coverage and avoid any legal issues.</strong></p>
        
        <div style="text-align: center;">
            <a href="tel:{{ $agentPhone }}" class="cta-button">📞 CALL NOW TO RENEW</a>
        </div>
        
        <h3>What We Can Do:</h3>
        <ul>
            <li>✅ Process immediate renewal</li>
            <li>✅ Find the best available rates</li>
            <li>✅ Ensure continuous coverage</li>
            <li>✅ Handle all paperwork</li>
        </ul>
        
        <p><strong>Time is critical!</strong> Please contact us immediately to restore your insurance coverage.</p>
        
        <p>Best regards,<br>
        <strong>{{ $agentName }}</strong><br>
        Insurance Agent<br>
        Phone: {{ $agentPhone }}<br>
        Email: {{ $agentEmail }}</p>
    </div>
    
    <div class="footer">
        <p>This is an urgent automated message. Please contact us immediately.</p>
        <p>© {{ date('Y') }} {{ $policy->company_name }}. All rights reserved.</p>
    </div>
</body>
</html>
