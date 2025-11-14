<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank You for Renewal - {{ $policy->company_name }}</title>
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
        .success-box {
            background: #d1fae5;
            border: 1px solid #10b981;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
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
        <h1>🎉 Thank You for Renewing!</h1>
        <p>Your Policy Has Been Successfully Renewed</p>
    </div>
    
    <div class="content">
        <h2>Dear {{ $policy->customer_name }},</h2>
        
        <div class="success-box">
            <h3>✅ Renewal Successful!</h3>
            <p>Your insurance policy has been successfully renewed. You are now covered for another year!</p>
        </div>
        
        <h3>📋 Renewed Policy Details:</h3>
        <ul>
            <li><strong>Policy Type:</strong> {{ $policy->policy_type }}</li>
            <li><strong>Company:</strong> {{ $policy->company_name }}</li>
            <li><strong>New Expiry Date:</strong> {{ $newEndDate }}</li>
            <li><strong>Renewal Premium:</strong> ₹{{ number_format($renewalPremium, 2) }}</li>
            <li><strong>Policy Number:</strong> {{ $policy->policy_number ?? 'Will be provided soon' }}</li>
        </ul>
        
        <h3>What's Next?</h3>
        <ul>
            <li>📄 You will receive your renewed policy document shortly</li>
            <li>📱 Keep your policy details handy for any claims</li>
            <li>📞 Contact us if you have any questions</li>
            <li>📅 We'll remind you about next year's renewal</li>
        </ul>
        
        <h3>Important Reminders:</h3>
        <ul>
            <li>✅ Keep your policy document in a safe place</li>
            <li>✅ Update your vehicle registration if needed</li>
            <li>✅ Inform us of any changes in your details</li>
            <li>✅ Save our contact information for emergencies</li>
        </ul>
        
        <p>We appreciate your continued trust in our services. Your satisfaction is our priority, and we're committed to providing you with the best insurance solutions.</p>
        
        <p>If you have any questions or need assistance with your renewed policy, please don't hesitate to contact us.</p>
        
        <p>Thank you for choosing us for your insurance needs!</p>
        
        <p>Best regards,<br>
        <strong>{{ $agentName }}</strong><br>
        Insurance Agent<br>
        Phone: {{ $agentPhone }}<br>
        Email: {{ $agentEmail }}</p>
    </div>
    
    <div class="footer">
        <p>This is a confirmation email for your policy renewal.</p>
        <p>© {{ date('Y') }} {{ $policy->company_name }}. All rights reserved.</p>
    </div>
</body>
</html>
