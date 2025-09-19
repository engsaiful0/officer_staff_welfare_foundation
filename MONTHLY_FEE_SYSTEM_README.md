# Monthly Fee Due Report System

## Overview
This system provides comprehensive management of monthly fee payments for students, including configurable settings, automated fine calculations, and detailed reporting capabilities.

## Features Implemented

### 1. Fee Settings Management
- **Monthly Fee Amount**: Set the standard monthly fee amount for all students
- **Payment Deadline**: Configure the day of the month when payments are due (default: 10th)
- **Fine Configuration**: 
  - Fixed amount per day OR percentage-based fines
  - Maximum fine limit (optional)
  - Grace period days (no fine for specified days after deadline)
- **Settings History**: All changes are tracked and previous settings are preserved

### 2. Monthly Fee Payment Tracking
- **Automatic Payment Generation**: Generate monthly payment records for all students
- **Overdue Tracking**: Automatic calculation of overdue days and fine amounts
- **Payment Status**: Track paid/unpaid/overdue status for each student
- **Fine Calculation**: Automated fine calculation based on settings and overdue days

### 3. Comprehensive Reporting
- **Monthly Fee Due Report**: Detailed report showing all students' payment status
- **Filtering Options**: Filter by academic year, month, year, payment status, and search
- **Statistics Dashboard**: Real-time statistics showing collection rates and financial summaries
- **Export Options**: PDF and Excel export functionality
- **Bulk Actions**: Mark multiple payments as paid/unpaid in bulk

### 4. User Interface
- **Settings Page**: User-friendly interface to configure monthly fee settings
- **Report Dashboard**: Interactive table with filtering, sorting, and bulk operations
- **Navigation Integration**: Seamlessly integrated into the existing menu system

## Database Structure

### Tables Created
1. **fee_settings**: Stores fee configuration settings
2. **monthly_fee_payments**: Tracks individual student monthly payments

### Key Fields
- Payment tracking with due dates, amounts, fines, and payment status
- Student and academic year relationships
- Comprehensive indexing for performance

## Usage Instructions

### Initial Setup
1. Navigate to "Fee Management" > "Monthly Fee Settings"
2. Configure your monthly fee amount and deadline
3. Set fine structure (fixed amount or percentage)
4. Save settings

### Monthly Payment Generation
1. Go to Fee Settings page
2. Click "Generate Current Month Payments" 
3. Select month/year and academic year
4. System creates payment records for all students

### Viewing Reports
1. Navigate to "Fee Management" > "Monthly Fee Report"
2. Use filters to view specific data
3. Export reports as needed
4. Use bulk actions to update payment status

### Key Features
- **Automatic Fine Calculation**: System calculates fines based on overdue days
- **Grace Period**: Configure days after deadline before fines apply
- **Maximum Fine**: Set upper limit on fine amounts
- **Real-time Updates**: Overdue status updates automatically

## File Structure
```
app/
├── Models/
│   ├── FeeSettings.php
│   └── MonthlyFeePayment.php
├── Http/Controllers/
│   ├── FeeSettingsController.php
│   └── MonthlyFeeReportController.php
resources/
├── views/content/fee-management/
│   ├── settings.blade.php
│   ├── monthly-report.blade.php
│   └── pdf/monthly-report.blade.php
assets/js/
├── fee-settings.js
└── monthly-fee-report.js
database/
├── migrations/
│   ├── create_fee_settings_table.php
│   └── create_monthly_fee_payments_table.php
└── seeders/
    └── FeeSettingsSeeder.php
```

## Routes
- `/app/fee-management/settings` - Fee settings configuration
- `/app/fee-management/monthly-report` - Monthly fee report
- Various API endpoints for data operations and exports

## Default Settings
- Monthly Fee: ৳5,000
- Payment Deadline: 10th of each month  
- Fine: ৳50 per day after deadline
- Maximum Fine: ৳1,000
- Grace Period: 0 days

## Future Enhancements
The system is designed to be extensible and can accommodate:
- SMS/Email notifications for due payments
- Online payment integration
- Student portal for payment viewing
- Automated fine adjustments
- Multi-semester fee structures
- Payment plan options

## Technical Notes
- Built using Laravel framework
- Responsive Bootstrap 5 UI
- MySQL database with proper indexing
- AJAX-based interactions for seamless UX
- PDF/Excel export using DomPDF and PhpSpreadsheet
- Comprehensive validation and error handling

This system provides a complete solution for monthly fee management with the flexibility to adapt to various institutional requirements.
