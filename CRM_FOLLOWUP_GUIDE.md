# CRM Follow-up System Guide

## Overview
The CRM Follow-up system has been enhanced to provide a comprehensive client relationship management solution for insurance policies. This system helps track and manage client follow-ups, especially for policies that are expiring or have expired.

## Features

### 1. CRM Dashboard
- **Overview Statistics**: Shows pending follow-ups, overdue follow-ups, completed today, and expiring policies
- **Expiring Policies Table**: Lists all policies expiring in the next 30 days with quick action buttons
- **Recent Follow-ups**: Shows the latest follow-up activities

### 2. Policy Integration
- Automatically identifies policies expiring within 30 days
- Color-coded status indicators:
  - 🔴 **Urgent**: Expires within 7 days
  - 🟡 **Warning**: Expires within 15 days  
  - 🟢 **Normal**: Expires within 30 days

### 3. Quick Actions
- **Call Client**: Direct phone call functionality
- **Send Email**: Automated email templates based on policy status
- **Create Follow-up**: One-click follow-up creation from expiring policies

### 4. Email Templates
The system includes 4 professional email templates:

#### Policy Expiring Soon
- Sent when policy expires within 30 days
- Includes policy details and renewal benefits
- Call-to-action for immediate renewal

#### Policy Expired (Urgent)
- Sent when policy has already expired
- Emphasizes urgency and consequences
- Strong call-to-action for immediate renewal

#### Renewal Reminder
- Gentle reminder for upcoming renewals
- Highlights benefits of renewing early
- Multiple renewal options

#### Thank You for Renewal
- Sent after successful renewal
- Confirms new policy details
- Provides important reminders

## Usage

### Accessing the CRM
1. Navigate to `/followups` in your application
2. The CRM dashboard will load automatically
3. View expiring policies and recent follow-ups

### Creating Follow-ups from Expiring Policies
1. In the "Policies Expiring Soon" section, click the "Follow-up" button
2. A new follow-up will be created automatically with:
   - Customer details from the policy
   - Follow-up type set to "Renewal"
   - Scheduled for the next day
   - Pre-filled notes with policy expiry information

### Sending Emails
1. Click the "Email" button next to any policy or follow-up
2. The system will automatically determine the appropriate email template based on:
   - Policy expiry status
   - Days until/since expiry
   - Follow-up status

### Quick Actions
- **Call**: Opens the phone dialer with the client's number
- **Email**: Sends an appropriate email template
- **Follow-up**: Creates a new follow-up record

## API Endpoints

### CRM Dashboard
```
GET /api/followups/crm-dashboard
```
Returns statistics, expiring policies, and recent follow-ups.

### Create Follow-up from Policy
```
POST /api/followups/create-from-policy/{policyId}
```
Creates a new follow-up based on an expiring policy.

### Send Email
```
POST /api/followups/send-email/{policyId}
Body: { "emailType": "expiring|expired|reminder|thankyou" }
```
Sends an email using the appropriate template.

## Email Types
- `expiring`: For policies expiring soon (30 days)
- `expired`: For policies that have already expired
- `reminder`: General renewal reminder
- `thankyou`: Thank you message after successful renewal

## Configuration

### Agent Information
Update the agent details in the `FollowupController::sendEmailToClient` method:
- `$agentName`: Your name
- `$agentPhone`: Your phone number
- `$agentEmail`: Your email address

### Email Settings
Ensure your Laravel mail configuration is properly set up in `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@domain.com
MAIL_FROM_NAME="Insurance Management"
```

## Benefits

1. **Proactive Client Management**: Never miss an expiring policy
2. **Automated Communication**: Professional email templates
3. **Quick Actions**: One-click calling and emailing
4. **Visual Status Tracking**: Color-coded urgency levels
5. **Comprehensive Dashboard**: All important information in one place

## Future Enhancements

- SMS notifications
- Automated follow-up scheduling
- Integration with calendar systems
- Advanced reporting and analytics
- Bulk email campaigns
- Custom email templates

## Support

For any issues or questions about the CRM system, please contact the development team or refer to the application documentation.
