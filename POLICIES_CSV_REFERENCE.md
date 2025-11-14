# Policies Bulk Upload - CSV Template Reference Guide

## 📋 Template Format
The CSV template contains the following columns in order:

1. **Policy Type*** - Required
2. **Business Type*** - Required  
3. **Customer Name*** - Required
4. **Phone*** - Required
5. **Email** - Optional
6. **Vehicle Number** - Required for Motor policies
7. **Vehicle Type** - Required for Motor policies
8. **Insurance Company*** - Required
9. **Insurance Type*** - Required
10. **Start Date*** - Required
11. **End Date*** - Required
12. **Premium*** - Required
13. **Payout** - Optional
14. **Customer Paid Amount*** - Required
15. **Agent Name** - Optional

## 🎯 Field Validation Rules

### Policy Type*
**Valid Options:** Motor, Health, Life
- Must be exactly one of these three values
- Case sensitive

### Business Type*
**Valid Options:** Self, Agent
- Must be exactly one of these two values
- Case sensitive

### Customer Name*
**Format:** Full name (e.g., "Rajesh Kumar")
- Required field
- Maximum 255 characters

### Phone*
**Format:** 10 digit number (e.g., "9550755039")
- Must be exactly 10 digits
- Numbers only, no spaces or special characters

### Email
**Format:** Valid email address (e.g., "rajesh@example.com")
- Optional field
- Must be valid email format if provided
- Maximum 255 characters

### Vehicle Number
**Format:** Registration number (e.g., "MH12AB1234")
- **Required for Motor policies only**
- Leave empty for Health and Life policies
- Maximum 20 characters

### Vehicle Type
**Valid Options:** Car, Bike, Scooter, Truck, Bus, Van, SUV, Sedan, Hatchback, Motorcycle
- **Required for Motor policies only**
- Leave empty for Health and Life policies
- Must be exactly one of the listed options

### Insurance Company*
**Options vary by Policy Type:**

**Motor Insurance Companies:**
- ICICI Lombard General Insurance Co. Ltd.
- Bajaj Allianz General Insurance Co. Ltd.
- HDFC ERGO General Insurance Co. Ltd.
- Tata AIG General Insurance Co. Ltd.
- New India Assurance Co. Ltd.
- Oriental Insurance Co. Ltd.
- National Insurance Co. Ltd.
- United India Insurance Co. Ltd.
- Reliance General Insurance Co. Ltd.
- SBI General Insurance Co. Ltd.
- Bharti AXA General Insurance Co. Ltd.
- Future Generali India Insurance Co. Ltd.
- IFFCO Tokio General Insurance Co. Ltd.
- Liberty General Insurance Co. Ltd.
- Magma HDI General Insurance Co. Ltd.

**Health Insurance Companies:**
- Star Health and Allied Insurance Co. Ltd.
- Cigna TTK Health Insurance Co. Ltd.
- Max Bupa Health Insurance Co. Ltd.
- Care Health Insurance Co. Ltd.
- Religare Health Insurance Co. Ltd.
- Plus all Motor insurance companies

**Life Insurance Companies:**
- LIC of India
- HDFC Life Insurance Co. Ltd.
- ICICI Prudential Life Insurance Co. Ltd.
- SBI Life Insurance Co. Ltd.
- Bajaj Allianz Life Insurance Co. Ltd.
- Max Life Insurance Co. Ltd.
- Tata AIA Life Insurance Co. Ltd.
- Kotak Mahindra Life Insurance Co. Ltd.
- Aditya Birla Sun Life Insurance Co. Ltd.
- Reliance Nippon Life Insurance Co. Ltd.
- PNB MetLife India Insurance Co. Ltd.
- Aviva Life Insurance Co. India Ltd.
- Bharti AXA Life Insurance Co. Ltd.
- Future Generali India Life Insurance Co. Ltd.
- IDBI Federal Life Insurance Co. Ltd.

### Insurance Type*
**Options vary by Policy Type:**

**Motor Insurance Types:**
- Comprehensive
- Third Party
- Third Party Fire & Theft
- Zero Depreciation
- Engine Protect
- Roadside Assistance
- Personal Accident Cover

**Health Insurance Types:**
- Individual Health
- Family Floater
- Senior Citizen
- Critical Illness
- Maternity Cover
- Dental Cover
- OPD Cover
- Preventive Health Check-up

**Life Insurance Types:**
- Term Insurance
- Whole Life
- Endowment
- Money Back
- Unit Linked Insurance Plan (ULIP)
- Child Plan
- Retirement Plan
- Critical Illness Rider

### Start Date*
**Format:** DD-MM-YYYY (e.g., "01-09-2025")
- Must be in DD-MM-YYYY format
- Must be a valid date
- Cannot be in the past

### End Date*
**Format:** DD-MM-YYYY (e.g., "01-09-2026")
- Must be in DD-MM-YYYY format
- Must be a valid date
- Must be after Start Date

### Premium*
**Format:** Numeric value only (e.g., "15000")
- Must be a positive number
- No currency symbols or commas
- Maximum 2 decimal places allowed

### Payout
**Format:** Numeric value only (e.g., "0")
- Optional field
- Must be a positive number if provided
- No currency symbols or commas
- Maximum 2 decimal places allowed

### Customer Paid Amount*
**Format:** Numeric value only (e.g., "15000")
- Must be a positive number
- No currency symbols or commas
- Maximum 2 decimal places allowed

### Agent Name
**Options:** Self, John Smith, Sarah Johnson, Michael Brown
- Optional field
- Must be exactly one of the listed options
- "Self" for self-business
- Agent names from database

## 📅 Date Format Examples

**Correct Format (DD-MM-YYYY):**
- 01-09-2025
- 15-12-2025
- 31-03-2026

**Incorrect Formats:**
- 2025-09-01 (YYYY-MM-DD)
- 09/01/2025 (MM/DD/YYYY)
- 1-9-2025 (single digits)

## 💰 Amount Format Examples

**Correct Format:**
- 15000
- 15000.50
- 0

**Incorrect Formats:**
- ₹15000 (currency symbol)
- 15,000 (commas)
- 15000.123 (too many decimals)

## ✅ Sample Data

**Motor Policy:**
```
Motor,Self,Rajesh Kumar,9550755039,rajesh@example.com,MH12AB1234,Car,ICICI Lombard General Insurance Co. Ltd.,Comprehensive,01-09-2025,01-09-2026,15000,0,15000,Self
```

**Health Policy:**
```
Health,Agent,Priya Sharma,9876543210,priya@example.com,,,Star Health and Allied Insurance Co. Ltd.,Family Floater,01-09-2025,01-09-2026,8000,0,8000,John Smith
```

**Life Policy:**
```
Life,Self,Amit Patel,8765432109,amit@example.com,,,LIC of India,Term Insurance,01-09-2025,01-09-2026,12000,0,12000,Self
```

## ⚠️ Important Notes

1. **Revenue Calculation:** Revenue is automatically calculated as: Customer Paid Amount - (Premium - Payout)
2. **Vehicle Fields:** Only required for Motor policies
3. **Date Validation:** End Date must be after Start Date
4. **Phone Validation:** Must be exactly 10 digits
5. **Email Validation:** Must be valid email format if provided
6. **Amount Validation:** No currency symbols or commas allowed
7. **Case Sensitivity:** Policy Type, Business Type, Vehicle Type, and Insurance options are case sensitive

## 🚀 Upload Process

1. Download the CSV template
2. Fill in your data following the validation rules
3. Save as .csv format
4. Upload through the bulk upload form
5. Monitor progress and check for validation errors
6. Review success message with imported count
