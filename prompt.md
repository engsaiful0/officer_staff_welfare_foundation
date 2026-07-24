# ROLE

You are a Senior Laravel 12 Architect, Senior Banking Software Engineer, and Islamic Banking Domain Expert with extensive experience in developing Core Banking Systems for Islamic Financial Institutions.

Your task is to refactor and complete my existing Laravel Investment Module into a production-ready Islamic Banking Investment System similar to Bangladesh Islami Bank PLC.

Do NOT create a demo application.

Do NOT rebuild the project from scratch.

Instead, improve my existing codebase while preserving compatibility.

Always follow Laravel 12 Best Practices, SOLID Principles, PSR-12 Coding Standards, Clean Architecture, Dependency Injection, DRY, and KISS.

--------------------------------------------------
PROJECT BACKGROUND
--------------------------------------------------

The project already contains:

• Investment CRUD
• Investment Account
• Investment Installments
• Ledger Entries
• Investment Account Number
• AJAX Form
• Bootstrap 5 UI
• jQuery
• MySQL Database

These modules already work.

Refactor them professionally instead of replacing them.

--------------------------------------------------
GOAL
--------------------------------------------------

Develop a complete Islamic Banking Investment Module supporting:

1. Bai-Muajjal
2. HPSM (Hire Purchase under Shirkatul Melk)

HPSM must support:

• Annuity Method
• Reducing Balance Method

The module should be scalable so that future Islamic investment products can be added without modifying existing business logic.

--------------------------------------------------
DO NOT
--------------------------------------------------

❌ Do not calculate financial values inside JavaScript.

❌ Do not trust browser values.

❌ Do not put business logic inside controllers.

❌ Do not duplicate code.

❌ Do not hardcode formulas inside Blade files.

--------------------------------------------------
ARCHITECTURE
--------------------------------------------------

Implement the following architecture.

Controllers

InvestmentController

↓

Services

InvestmentService

↓

Factory

InvestmentCalculatorFactory

↓

Interface

InvestmentCalculatorInterface

↓

Calculators

BaiMuajjalCalculator

HpsmAnnuityCalculator

HpsmReducingCalculator

↓

DTO

InvestmentCalculationResult

--------------------------------------------------
SOLID PRINCIPLES
--------------------------------------------------

Follow:

Single Responsibility Principle

Open Closed Principle

Liskov Substitution Principle

Interface Segregation Principle

Dependency Injection

Every calculator must be replaceable without changing the service.

--------------------------------------------------
INVESTMENT TYPES
--------------------------------------------------

Support exactly two products.

1. Bai-Muajjal

Characteristics

• Fixed Selling Price

• Fixed Profit

• Equal Monthly Installments

• Monthly Payment Only

• No Calculation Method

--------------------------------------------------

2. HPSM

Characteristics

• Monthly Payment Only

• Requires Calculation Method

Calculation Method

• Annuity

• Reducing Balance

--------------------------------------------------
USER INTERFACE
--------------------------------------------------

Create a professional banking interface.

Use Bootstrap 5.

Responsive layout.

Minimal clicks.

Card-based design.

Real-time installment preview.

Sticky summary panel.

Proper spacing.

Professional color scheme.

--------------------------------------------------
PAYMENT TYPE
--------------------------------------------------

Support ONLY

Monthly

Remove

Quarterly

Yearly

Daily

--------------------------------------------------
CREATE INVESTMENT FORM
--------------------------------------------------

Fields

Member

Account Number (Auto)

Investment Type

Calculation Method (Visible only for HPSM)

Principal Amount

Profit / Rent Rate

Investment Years

Number of Installments (Auto)

Account Opening Date

Investment Start Date

Gestation Date

Notes

--------------------------------------------------
AUTO GENERATED FIELDS
--------------------------------------------------

These must NEVER be editable.

Number of Installments

Principal Per Installment

Profit Per Installment

Monthly Installment

Total Profit

Selling Price

Outstanding Balance

Maturity Date

Schedule

--------------------------------------------------
DYNAMIC UI
--------------------------------------------------

If Investment Type = Bai-Muajjal

Hide Calculation Method.

If Investment Type = HPSM

Show Calculation Method.

Whenever user changes

Principal

Rate

Years

Investment Type

Calculation Method

Immediately call

POST

/api/investments/calculate

Laravel performs calculations.

Return JSON.

Update preview automatically.

--------------------------------------------------
VERY IMPORTANT
--------------------------------------------------

JavaScript must NEVER calculate:

Installment

Profit

Selling Price

Rent

EMI

Outstanding Balance

Schedule

JavaScript only displays data returned by Laravel.

--------------------------------------------------
CONTROLLER RESPONSIBILITIES
--------------------------------------------------

Validate Request

Call Service

Return View

Return JSON

Nothing else.

--------------------------------------------------
SERVICE RESPONSIBILITIES
--------------------------------------------------

Validate business rules.

Choose calculator.

Calculate schedule.

Create Investment.

Create Installments.

Create Ledger Entries.

Create Investment Account.

Create Account Number.

Use DB Transactions.

--------------------------------------------------
FACTORY RESPONSIBILITIES
--------------------------------------------------

Automatically return

BaiMuajjalCalculator

or

HpsmAnnuityCalculator

or

HpsmReducingCalculator

based on

Investment Type

and

Calculation Method.

--------------------------------------------------
INTERFACE
--------------------------------------------------

Every calculator must implement

calculate()

generateSchedule()

calculateSummary()

--------------------------------------------------
BAI-MUAJJAL
--------------------------------------------------

Input

Principal

Profit Rate

Years

Monthly

Calculate

Profit

Selling Price

Monthly Installment

Outstanding Balance

Schedule

Equal Installments

--------------------------------------------------
HPSM ANNUITY
--------------------------------------------------

Generate

EMI

Principal

Rent

Outstanding Principal

Ownership Ratio

Monthly Schedule

--------------------------------------------------
HPSM REDUCING
--------------------------------------------------

Generate

Monthly Profit

Monthly Principal

Outstanding Principal

Ownership Ratio

Monthly Schedule

Profit decreases every month.

--------------------------------------------------
INSTALLMENT SCHEDULE
--------------------------------------------------

Each installment should contain

Installment No

Date

Beginning Balance

Principal

Profit

Monthly Installment

Ending Balance

Outstanding Balance

Cumulative Profit

Status

Payment Date

Payment Reference

--------------------------------------------------
SUMMARY PANEL
--------------------------------------------------

Display

Principal

Profit

Selling Price

Monthly Installment

Investment Years

Number of Installments

Expected Maturity Date

Total Payable

--------------------------------------------------
SCHEDULE TABLE
--------------------------------------------------

Display

Installment

Date

Beginning Balance

Principal

Profit

Installment

Ending Balance

Status

Outstanding Balance

--------------------------------------------------
AJAX API
--------------------------------------------------

Create endpoint

POST

/api/investments/calculate

Return

{
    "summary": {},
    "schedule": [],
    "totals": {}
}

--------------------------------------------------
SAVE INVESTMENT
--------------------------------------------------

When user clicks

Create Investment

Laravel MUST

Recalculate everything.

Ignore browser totals.

Save only backend calculated values.

--------------------------------------------------
VALIDATION
--------------------------------------------------

Validate

Member

Investment Type

Calculation Method

Principal

Rate

Years

Monthly Payment

Positive Values

Duplicate Account Numbers

Invalid Dates

Grace Date

--------------------------------------------------
DATABASE
--------------------------------------------------

Reuse existing tables.

Add only necessary columns.

Possible additions

calculation_method

investment_product

selling_price

profit_amount

emi_amount

remaining_principal

ownership_ratio

--------------------------------------------------
LEDGER
--------------------------------------------------

Automatically create

Investment Entry

Profit Entry

Installment Entry

Settlement Entry

Penalty Entry

Rebate Entry

Adjustment Entry

--------------------------------------------------
INVESTMENT ACCOUNT
--------------------------------------------------

Automatically maintain

Opening Balance

Outstanding Balance

Profit Balance

Installments Paid

Installments Pending

Status

--------------------------------------------------
FUTURE READY
--------------------------------------------------

Architecture must support future Islamic investment products

Murabaha

Ijarah

Mudarabah

Musharakah

Bai Salam

Istisna

without modifying existing code.

Only new calculator classes should be required.

--------------------------------------------------
REPORTS
--------------------------------------------------

Prepare architecture for

Investment Statement

Ledger Statement

Installment Schedule

Profit Statement

Settlement Statement

Customer Statement

--------------------------------------------------
SECURITY
--------------------------------------------------

Never trust browser values.

Validate everything again.

Use Transactions.

Prevent duplicate submission.

Use Form Requests.

Use Dependency Injection.

Use Service Container.

No Raw SQL.

--------------------------------------------------
CODE QUALITY
--------------------------------------------------

Laravel 12

PSR-12

SOLID

DRY

KISS

Dependency Injection

Type Hinting

Return Types

Repositories where appropriate

Service Layer

Factory Pattern

Strategy Pattern

DTO Pattern

No Business Logic inside Controllers.

No Business Logic inside Blade.

No Business Logic inside JavaScript.

--------------------------------------------------
DOCUMENTATION
--------------------------------------------------

Every class must include PHPDoc.

Explain

Purpose

Inputs

Outputs

Business Rules

Formula

Example Usage

--------------------------------------------------
DELIVERABLES
--------------------------------------------------

Implement the module completely.

Generate:

1. Folder Structure

2. Form Request Classes

3. Controller Updates

4. Service Classes

5. Factory

6. Interface

7. DTO

8. BaiMuajjalCalculator

9. HpsmAnnuityCalculator

10. HpsmReducingCalculator

11. AJAX Endpoint

12. JavaScript Updates

13. Blade Updates

14. Database Migration (if required)

15. Eloquent Relationship Updates

16. Installment Generator

17. Ledger Integration

18. Investment Account Integration

19. Validation Rules

20. Unit-Test-Friendly Architecture

Do not leave TODO comments.

Do not leave placeholder methods.

Implement the entire module completely with production-quality Laravel code that is maintainable, extensible, secure, user-friendly, and suitable for real-world Islamic banking operations.