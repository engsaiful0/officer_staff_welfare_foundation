<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\laravel_example\UserManagement;

use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\Crm;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\layouts\CollapsedMenu;
use App\Http\Controllers\layouts\ContentNavbar;
use App\Http\Controllers\layouts\ContentNavSidebar;

use App\Http\Controllers\layouts\Horizontal;
use App\Http\Controllers\layouts\Vertical;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;

use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsSecurity;
use App\Http\Controllers\pages\AccountSettingsBilling;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\Faq;
use App\Http\Controllers\pages\Pricing as PagesPricing;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\pages\MiscComingSoon;
use App\Http\Controllers\pages\MiscNotAuthorized;
use App\Http\Controllers\authentications\LoginBasic;

use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\RegisterCover;
use App\Http\Controllers\authentications\RegisterMultiSteps;
use App\Http\Controllers\authentications\VerifyEmailBasic;
use App\Http\Controllers\authentications\VerifyEmailCover;
use App\Http\Controllers\authentications\ResetPasswordBasic;
use App\Http\Controllers\authentications\ResetPasswordCover;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\authentications\ForgotPasswordCover;
use App\Http\Controllers\authentications\TwoStepsBasic;
use App\Http\Controllers\authentications\TwoStepsCover;

use App\Http\Controllers\cards\CardAdvance;
use App\Http\Controllers\cards\CardStatistics;
use App\Http\Controllers\cards\CardAnalytics;
use App\Http\Controllers\cards\CardGamifications;
use App\Http\Controllers\cards\CardActions;

use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\form_layouts\StickyActions;

use App\Http\Controllers\settings\Semester;
use App\Http\Controllers\settings\Designation;
use App\Http\Controllers\settings\IncomeHead;
use App\Http\Controllers\settings\ExpenseHead;

use App\Http\Controllers\settings\Religion;

use App\Http\Controllers\settings\AppSettings;
use App\Http\Controllers\settings\PaymentMethod;
use App\Http\Controllers\settings\Nationality;

use App\Http\Controllers\settings\Month;

use App\Http\Controllers\settings\User;

use App\Http\Controllers\settings\Branch;
use App\Http\Controllers\settings\Zone;
use App\Http\Controllers\settings\Relation;
use App\Http\Controllers\settings\InvestmentType;
use App\Http\Controllers\CacheController;



use App\Http\Controllers\EmployeeController;

use App\Http\Controllers\FeeCollectController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\StudentFinalReportController;


Route::get('/app/expense-report', [ReportController::class, 'expenseReport'])->name('expense-report');
Route::get('/app/expense-report/pdf', [ReportController::class, 'expenseReportPdf'])->name('expense-report.pdf');
Route::get('/app/expense-report/excel', [ReportController::class, 'expenseReportExcel'])->name('expense-report.excel');

// Report Routes
Route::get('/app/employee-list-report', [ReportController::class, 'employeeListReport'])->name('employee-list-report');
Route::get('/app/teacher-list-report', [ReportController::class, 'teacherListReport'])->name('teacher-list-report');
Route::get('/app/head-wise-fee-report', [ReportController::class, 'headWiseFeeReport'])->name('head-wise-fee-report');
Route::get('/app/head-wise-fee-report/excel', [ReportController::class, 'headWiseFeeReportExcel'])->name('head-wise-fee-report.excel');

// Student Final Report Routes
Route::get('/app/final-report/student/{student}', [StudentFinalReportController::class, 'generateFinalReport'])->name('final-report.student');
Route::get('/app/final-report/student/{student}/completion-certificate', [StudentFinalReportController::class, 'generateCompletionCertificate'])->name('final-report.completion-certificate');
Route::get('/app/final-report/completed-students', [StudentFinalReportController::class, 'getCompletedStudents'])->name('final-report.completed-students');
Route::post('/app/final-report/bulk-certificates', [StudentFinalReportController::class, 'bulkGenerateCompletionCertificates'])->name('final-report.bulk-certificates');
Route::get('/app/final-report/export-excel', [StudentFinalReportController::class, 'exportFinalReports'])->name('final-report.export-excel');
Route::get('/app/final-report/completion-stats', [StudentFinalReportController::class, 'getCompletionStatistics'])->name('final-report.completion-stats');


Route::get('/app/get-past-fee', [FeeCollectController::class, 'getPastFee'])->name('get-past-fee');
Route::get('/app/collect-fee', [FeeCollectController::class, 'create'])->name('app-collect-fee.create')->middleware('permission:fee-collect-add');
Route::post('/app/collect-fee', [FeeCollectController::class, 'store'])->name('app-collect-fee.store')->middleware('permission:fee-collect-add');
Route::get('/app/collect-fee/receipt/{id}', [FeeCollectController::class, 'showReceipt'])->name('app-collect-fee.receipt');
Route::get('/app/collect-fee/details/{id}', [FeeCollectController::class, 'showDetails'])->name('app-collect-fee.details');
Route::get('/app/collect-fee/get-students/{academic_year_id}/{semester_id}', [FeeCollectController::class, 'getStudents'])->name('app-collect-fee.get-students');
Route::get('/app/collect-fee/get-fees/{semester_id}/{fee_type}', [FeeCollectController::class, 'getFees'])->name('app-collect-fee.get-fees');
Route::post('/app/collect-fee/check-paid-status', [FeeCollectController::class, 'checkPaidStatus'])->name('app-collect-fee.check-paid-status');
Route::get('/app/collect-fee/get-paid-fee-heads/{student_id}/{academic_year_id}/{semester_id}', [FeeCollectController::class, 'getPaidFeeHeads'])->name('app-collect-fee.get-paid-fee-heads');
Route::get('/app/collect-fee/get-fee-settings', [FeeCollectController::class, 'getFeeSettings'])->name('app-collect-fee.get-fee-settings');
Route::get('/app/view-collect-fee', [FeeCollectController::class, 'index'])->name('app-collect-fee.view-collect-fee');
Route::get('/app/collect-fee/{id}/edit', [FeeCollectController::class, 'edit'])->name('app-collect-fee.edit')->middleware('permission:fee-collect-edit');
Route::put('/app/collect-fee/{id}', [FeeCollectController::class, 'update'])->name('app-collect-fee.update')->middleware('permission:fee-collect-edit');
Route::delete('/app/collect-fee/{id}', [FeeCollectController::class, 'destroy'])->name('app-collect-fee.destroy')->middleware('permission:fee-collect-delete');

Route::resource('employees', EmployeeController::class)->except(['create', 'edit', 'destroy']);
Route::get('/app/employees/add-employee', [EmployeeController::class, 'create'])->name('employees.add-employee')->middleware('permission:employee-add');
Route::get('/app/employees/view-employee', [EmployeeController::class, 'index'])->name('employees.view-employee');
Route::get('/app/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit')->middleware('permission:employee-edit');
Route::delete('/app/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy')->middleware('permission:employee-delete');

Route::get('/app/settings/users', [User::class, 'index'])->name('app-settings-users');
Route::get('/app/settings/get-users', [User::class, 'getUsers'])->name('app-settings-get-users');
Route::post('/app/settings/users', [User::class, 'store'])->name('app-settings-users.store');
Route::put('/app/settings/users/{id}', [User::class, 'update'])->name('app-settings-users.update');
Route::delete('/app/settings/users/{id}', [User::class, 'destroy'])->name('app-settings-users.destroy');


Route::get('/app/settings/nationality', [Nationality::class, 'index'])->name('app-settings-nationality');
Route::get('/app/settings/get-nationality', [Nationality::class, 'getNationalities'])->name('app-settings-get-nationality');
Route::post('/app/settings/nationality', [Nationality::class, 'store'])->name('app-settings-nationality.store');
Route::put('/app/settings/nationality/{id}', [Nationality::class, 'update'])->name('app-settings-nationality.update');
Route::delete('/app/settings/nationality/{id}', [Nationality::class, 'destroy'])->name('app-settings-nationality.destroy');


Route::get('/app/settings/payment-method', [PaymentMethod::class, 'index'])->name('app-settings-payment-method');
Route::get('/app/settings/get-payment-method', [PaymentMethod::class, 'getPaymentMethods'])->name('app-settings-get-payment-method');
Route::post('/app/settings/payment-method', [PaymentMethod::class, 'store'])->name('app-settings-payment-method.store');
Route::put('/app/settings/payment-method/{id}', [PaymentMethod::class, 'update'])->name('app-settings-payment-method.update');
Route::delete('/app/settings/payment-method/{id}', [PaymentMethod::class, 'destroy'])->name('app-settings-payment-method.destroy');


Route::get('/app/settings/app-settings', [AppSettings::class, 'index'])->name('app-settings.index');
Route::put('/app/settings/app-settings/{id}', [AppSettings::class, 'update'])->name('app-settings.update');




Route::get('/app/settings/religion', [Religion::class, 'index'])->name('settings-religion');
Route::get('/app/settings/get-religion', [Religion::class, 'getReligions'])->name('settings-religion.get-religion');
Route::post('/app/settings/religion', [Religion::class, 'store'])->name('settings-religion.store');
Route::put('/app/settings/religion/{id}', [Religion::class, 'update'])->name('settings-religion.update');
Route::delete('/app/settings/religion/{id}', [Religion::class, 'destroy'])->name('settings-religion.destroy');


Route::get('/app/settings/designation', [Designation::class, 'index'])->name('app-settings-designation');
Route::get('/app/settings/get-designation', [Designation::class, 'getDesignation'])->name('app-settings-get-designation');
Route::post('/app/settings/designation', [Designation::class, 'store'])->name('app-settings-designation.store');
Route::put('/app/settings/designation/{id}', [Designation::class, 'update'])->name('app-settings-designation.update');
Route::delete('/app/settings/designation/{id}', [Designation::class, 'destroy'])->name('app-settings-designation.destroy');


Route::get('/app/settings/semester', [Semester::class, 'index'])->name('app-settings-semester');
Route::get('/app/settings/get-semester', [Semester::class, 'getSemester'])->name('app-settings-get-semester');
Route::post('/app/settings/semester', [Semester::class, 'store'])->name('app-settings-semester.store');
Route::put('/app/settings/semester/{id}', [Semester::class, 'update'])->name('app-settings-semester.update');
Route::delete('/app/settings/semester/{id}', [Semester::class, 'destroy'])->name('app-settings-semester.destroy');

Route::get('/app/settings/month', [Month::class, 'index'])->name('app-settings-month');
Route::get('/app/settings/get-month', [Month::class, 'getMonth'])->name('app-settings-get-month');
Route::post('/app/settings/month', [Month::class, 'store'])->name('app-settings-month.store');
Route::put('/app/settings/month/{id}', [Month::class, 'update'])->name('app-settings-month.update');
Route::delete('/app/settings/month/{id}', [Month::class, 'destroy'])->name('app-settings-month.destroy');

Route::get('/app/settings/income-head', [IncomeHead::class, 'index'])->name('app-settings-income-head');
Route::get('/app/settings/get-income-head', [IncomeHead::class, 'getIncomeHead'])->name('app-settings-get-income-head');
Route::post('/app/settings/income-head', [IncomeHead::class, 'store'])->name('app-settings-income-head.store');
Route::put('/app/settings/income-head/{id}', [IncomeHead::class, 'update'])->name('app-settings-income-head.update');
Route::delete('/app/settings/income-head/{id}', [IncomeHead::class, 'destroy'])->name('app-settings-income-head.destroy');

Route::get('/app/settings/expense-head', [ExpenseHead::class, 'index'])->name('app-settings-expense-head');
Route::get('/app/settings/get-expense-head', [ExpenseHead::class, 'getExpenseHead'])->name('app-settings-get-expense-head');
Route::post('/app/settings/expense-head', [ExpenseHead::class, 'store'])->name('app-settings-expense-head.store');
Route::put('/app/settings/expense-head/{id}', [ExpenseHead::class, 'update'])->name('app-settings-expense-head.update');
Route::delete('/app/settings/expense-head/{id}', [ExpenseHead::class, 'destroy'])->name('app-settings-expense-head.destroy');

Route::get('/app/settings/branch', [Branch::class, 'index'])->name('app-settings-branch');
Route::get('/app/settings/get-branch', [Branch::class, 'getbranch'])->name('app-settings-get-branch');
Route::post('/app/settings/branch', [Branch::class, 'store'])->name('app-settings-branch.store');
Route::put('/app/settings/branch/{id}', [Branch::class, 'update'])->name('app-settings-branch.update');
Route::delete('/app/settings/branch/{id}', [Branch::class, 'destroy'])->name('app-settings-branch.destroy');

Route::get('/app/settings/zone', [Zone::class, 'index'])->name('app-settings-zone');
Route::get('/app/settings/get-zone', [Zone::class, 'getZone'])->name('app-settings-get-zone');
Route::post('/app/settings/zone', [Zone::class, 'store'])->name('app-settings-zone.store');
Route::put('/app/settings/zone/{id}', [Zone::class, 'update'])->name('app-settings-zone.update');
Route::delete('/app/settings/zone/{id}', [Zone::class, 'destroy'])->name('app-settings-zone.destroy');

Route::get('/app/settings/relation', [Relation::class, 'index'])->name('app-settings-relation');
Route::get('/app/settings/get-relation', [Relation::class, 'getrelation'])->name('app-settings-get-relation');
Route::post('/app/settings/relation', [Relation::class, 'store'])->name('app-settings-relation.store');
Route::put('/app/settings/relation/{id}', [Relation::class, 'update'])->name('app-settings-relation.update');
Route::delete('/app/settings/relation/{id}', [Relation::class, 'destroy'])->name('app-settings-relation.destroy');

Route::get('/app/settings/investment-type', [InvestmentType::class, 'index'])->name('app-settings-investment-type');
Route::get('/app/settings/get-investment-type', [InvestmentType::class, 'getInvestmentTypes'])->name('app-settings-get-investment-type');
Route::post('/app/settings/investment-type', [InvestmentType::class, 'store'])->name('app-settings-investment-type.store');
Route::put('/app/settings/investment-type/{id}', [InvestmentType::class, 'update'])->name('app-settings-investment-type.update');
Route::delete('/app/settings/investment-type/{id}', [InvestmentType::class, 'destroy'])->name('app-settings-investment-type.destroy');

// Cache Management Routes
Route::get('/app/settings/cache-clear', [CacheController::class, 'index'])->name('app-settings-cache-clear');
Route::get('/app/settings/clear-cache', [CacheController::class, 'clearCache'])->name('clear-cache');

// Main Page Route
Route::get('/', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('/login', [LoginBasic::class, 'index'])->name('login');
Route::post('/auth/login-basic', [LoginBasic::class, 'login'])->name('auth-login-basic.post');

// Logout Route
Route::post('/logout', [LoginBasic::class, 'logout'])->name('logout');

Route::get('/dashboard/analytics', [Analytics::class, 'index'])->name('dashboard-analytics');
Route::get('/dashboard/crm', [Crm::class, 'index'])->name('dashboard-crm');
// locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);

// layout
Route::get('/layouts/collapsed-menu', [CollapsedMenu::class, 'index'])->name('layouts-collapsed-menu');
Route::get('/layouts/content-navbar', [ContentNavbar::class, 'index'])->name('layouts-content-navbar');
Route::get('/layouts/content-nav-sidebar', [ContentNavSidebar::class, 'index'])->name('layouts-content-nav-sidebar');
// Route::get('/layouts/navbar-full', [NavbarFull::class, 'index'])->name('layouts-navbar-full');
// Route::get('/layouts/navbar-full-sidebar', [NavbarFullSidebar::class, 'index'])->name('layouts-navbar-full-sidebar');
Route::get('/layouts/horizontal', [Horizontal::class, 'index'])->name('dashboard-analytics');
Route::get('/layouts/vertical', [Vertical::class, 'index'])->name('dashboard-analytics');
Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name('pages-account-settings-account');
Route::get('/pages/account-settings-security', [AccountSettingsSecurity::class, 'index'])->name('pages-account-settings-security');
Route::get('/pages/account-settings-billing', [AccountSettingsBilling::class, 'index'])->name('pages-account-settings-billing');
Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name('pages-account-settings-notifications');
Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name('pages-account-settings-connections');
Route::get('/pages/faq', [Faq::class, 'index'])->name('pages-faq');
Route::get('/pages/pricing', [PagesPricing::class, 'index'])->name('pages-pricing');
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name('pages-misc-under-maintenance');
Route::get('/pages/misc-comingsoon', [MiscComingSoon::class, 'index'])->name('pages-misc-comingsoon');
Route::get('/pages/misc-not-authorized', [MiscNotAuthorized::class, 'index'])->name('pages-misc-not-authorized');

// authentication

Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::get('/auth/register-cover', [RegisterCover::class, 'index'])->name('auth-register-cover');
Route::get('/auth/register-multisteps', [RegisterMultiSteps::class, 'index'])->name('auth-register-multisteps');
Route::get('/auth/verify-email-basic', [VerifyEmailBasic::class, 'index'])->name('auth-verify-email-basic');
Route::get('/auth/verify-email-cover', [VerifyEmailCover::class, 'index'])->name('auth-verify-email-cover');
Route::get('/auth/reset-password-basic', [ResetPasswordBasic::class, 'index'])->name('auth-reset-password-basic');
Route::get('/auth/reset-password-cover', [ResetPasswordCover::class, 'index'])->name('auth-reset-password-cover');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');
Route::get('/auth/forgot-password-cover', [ForgotPasswordCover::class, 'index'])->name('auth-forgot-password-cover');
Route::get('/auth/two-steps-basic', [TwoStepsBasic::class, 'index'])->name('auth-two-steps-basic');
Route::get('/auth/two-steps-cover', [TwoStepsCover::class, 'index'])->name('auth-two-steps-cover');

// modal
// Route::get('/modal-examples', [ModalExample::class, 'index'])->name('modal-examples');

// cards
// Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');
// Route::get('/cards/advance', [CardAdvance::class, 'index'])->name('cards-advance');
// Route::get('/cards/statistics', [CardStatistics::class, 'index'])->name('cards-statistics');
// Route::get('/cards/analytics', [CardAnalytics::class, 'index'])->name('cards-analytics');
// Route::get('/cards/gamifications', [CardGamifications::class, 'index'])->name('cards-gamifications');
// Route::get('/cards/actions', [CardActions::class, 'index'])->name('cards-actions');



// form layouts
// Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
// Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');
// Route::get('/form/layouts-sticky', [StickyActions::class, 'index'])->name('form-layouts-sticky');


// laravel example
Route::get('/laravel/user-management', [UserManagement::class, 'UserManagement'])->name('laravel-example-user-management');
Route::resource('/user-list', UserManagement::class);

use App\Http\Controllers\RuleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DepositInstallmentAmountController;

Route::get('/app/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
Route::get('/app/expenses/export-excel', [ExpenseController::class, 'exportExcel'])->name('expenses.export-excel');
Route::get('/app/expenses/export-pdf', [ExpenseController::class, 'exportPdf'])->name('expenses.export-pdf');
Route::get('/app/get-expenses', [ExpenseController::class, 'getExpenses'])->name('app-get-expenses');
Route::post('/app/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
Route::put('/app/expenses/{id}', [ExpenseController::class, 'update'])->name('expenses.update');
Route::delete('/app/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

Route::resource('rules', RuleController::class)->names([
    'index' => 'app-access-rules.index',
    'store' => 'app-access-rules.store',
    'edit' => 'app-access-rules.edit',
    'update' => 'app-access-rules.update',
    'destroy' => 'app-access-rules.destroy',
]);
Route::get('app/settings/get-rules', [RuleController::class, 'getRules'])->name('get-rules');
Route::resource('permissions', PermissionController::class)->names([
    'index' => 'app-access-permission.index',
    'store' => 'app-access-permission.store',
    'edit' => 'app-access-permission.edit',
    'update' => 'app-access-permission.update',
    'destroy' => 'app-access-permission.destroy',
]);

// Member Routes
Route::resource('members', MemberController::class)->except(['create', 'edit', 'destroy']);
Route::get('/app/members/view-member', [MemberController::class, 'index'])->name('members.view-member');
Route::get('/app/members/add-member', [MemberController::class, 'create'])->name('members.add-member')->middleware('permission:member-add');
Route::post('/members', [MemberController::class, 'store'])->name('members.store')->middleware('permission:member-add');
Route::get('/app/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit')->middleware('permission:member-edit');
Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update')->middleware('permission:member-edit');
Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy')->middleware('permission:member-delete');
Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
Route::get('/members/get-members', [MemberController::class, 'getMembers'])->name('members.get-members');
Route::post('/members/check-email-unique', [MemberController::class, 'checkEmailUnique'])->name('members.check-email-unique');
Route::post('/members/check-mobile-unique', [MemberController::class, 'checkMobileUnique'])->name('members.check-mobile-unique');
Route::post('/members/check-nid-unique', [MemberController::class, 'checkNidUnique'])->name('members.check-nid-unique');

// Export routes
Route::get('/members/export/excel', [MemberController::class, 'exportExcel'])->name('members.export-excel');
Route::get('/members/export/pdf', [MemberController::class, 'exportPdf'])->name('members.export-pdf');

// Monthly Deposit Installment Settings (under Member)
Route::get('/app/members/monthly-deposit-installment-settings', [DepositInstallmentAmountController::class, 'index'])->name('members.monthly-deposit-installment-settings.index');
Route::get('/app/members/monthly-deposit-installment-settings/get-members', [DepositInstallmentAmountController::class, 'getMembers'])->name('members.monthly-deposit-installment-settings.get-members');
Route::get('/app/members/monthly-deposit-installment-settings/get-data', [DepositInstallmentAmountController::class, 'getData'])->name('members.monthly-deposit-installment-settings.get-data');
Route::get('/app/members/monthly-deposit-installment-settings/last-amount/{memberId}', [DepositInstallmentAmountController::class, 'getLastAmount'])->name('members.monthly-deposit-installment-settings.last-amount');
Route::post('/app/members/monthly-deposit-installment-settings', [DepositInstallmentAmountController::class, 'store'])->name('members.monthly-deposit-installment-settings.store');
Route::get('/app/members/monthly-deposit-installment-settings/{id}', [DepositInstallmentAmountController::class, 'show'])->name('members.monthly-deposit-installment-settings.show');
Route::put('/app/members/monthly-deposit-installment-settings/{id}', [DepositInstallmentAmountController::class, 'update'])->name('members.monthly-deposit-installment-settings.update');
Route::delete('/app/members/monthly-deposit-installment-settings/{id}', [DepositInstallmentAmountController::class, 'destroy'])->name('members.monthly-deposit-installment-settings.destroy');

// Investment Module Routes
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\LedgerEntryController;
use App\Http\Controllers\InvestmentImportController;
use App\Http\Controllers\InvestmentReportController;

// Investment Routes
Route::resource('investments', InvestmentController::class);

// Investment Payment Routes (must be before parameterized routes)
use App\Http\Controllers\InvestmentPaymentController;
use App\Http\Controllers\InvestmentCollectionController;
Route::get('/app/investments/payment-investment', [InvestmentPaymentController::class, 'paymentInvestment'])->name('investments.payments.payment-investment');

// Investment Collection Routes
Route::get('/app/investments/collection', [InvestmentCollectionController::class, 'index'])->name('investments.collection.index');
Route::get('/app/investments/collection/get-installments', [InvestmentCollectionController::class, 'getInstallments'])->name('investments.collection.get-installments');
Route::post('/app/investments/collection', [InvestmentCollectionController::class, 'store'])->name('investments.collection.store');
Route::post('/app/investments/collection/calculate-fine', [InvestmentCollectionController::class, 'calculateFine'])->name('investments.collection.calculate-fine');
Route::get('/app/investments/view-collection', [InvestmentCollectionController::class, 'viewCollection'])->name('investments.view-collection');
Route::get('/app/investments/collection/export', [InvestmentCollectionController::class, 'export'])->name('investments.collection.export');

Route::get('/app/investments/view-investments', [InvestmentController::class, 'index'])->name('investments.view-investments');
Route::get('/app/investments/add-investment', [InvestmentController::class, 'create'])->name('investments.add-investment');
Route::get('/app/investments/member/{memberId}', [InvestmentController::class, 'getByMember'])->name('investments.by-member');
Route::get('/app/investments/{investment}', [InvestmentController::class, 'show'])->name('investments.show');
Route::get('/app/investments/{investment}/edit', [InvestmentController::class, 'edit'])->name('investments.edit');
Route::get('/app/investments/{investment}/pay', [InvestmentPaymentController::class, 'payInvestment'])->name('investments.payments.pay-investment');
Route::get('/app/investments/{investment}/payments', [InvestmentPaymentController::class, 'index'])->name('investments.payments.index');
Route::get('/app/investments/{investment}/payments/{installmentId}', [InvestmentPaymentController::class, 'show'])->name('investments.payments.show');
Route::post('/app/investments/{investment}/payments/{installmentId}', [InvestmentPaymentController::class, 'store'])->name('investments.payments.store');
Route::post('/app/investments/{investment}/payments/{installmentId}/calculate-fine', [InvestmentPaymentController::class, 'calculateFine'])->name('investments.payments.calculate-fine');

// Ledger Entry Routes
Route::resource('ledger-entries', LedgerEntryController::class);
Route::get('/app/ledger-entries/view-entries', [LedgerEntryController::class, 'index'])->name('ledger-entries.view-entries');
Route::get('/app/ledger-entries/add-entry', [LedgerEntryController::class, 'create'])->name('ledger-entries.add-entry');
Route::post('/app/ledger-entries/create-accrual', [LedgerEntryController::class, 'createAccrual'])->name('ledger-entries.create-accrual');

// Investment Import Routes
Route::get('/app/investments/import', [InvestmentImportController::class, 'index'])->name('investments.import');
Route::post('/app/investments/import', [InvestmentImportController::class, 'import'])->name('investments.import.store');
Route::get('/app/investments/import/history', [InvestmentImportController::class, 'history'])->name('investments.import.history');

// Investment Report Routes
Route::get('/app/investments/reports', [InvestmentReportController::class, 'index'])->name('investments.reports');
Route::get('/app/investments/reports/portfolio', [InvestmentReportController::class, 'portfolioReport'])->name('investments.reports.portfolio');
Route::get('/app/investments/reports/interest', [InvestmentReportController::class, 'interestReport'])->name('investments.reports.interest');
Route::get('/app/investments/reports/maturity', [InvestmentReportController::class, 'maturityReport'])->name('investments.reports.maturity');
Route::get('/app/investments/reports/export-pdf', [InvestmentReportController::class, 'exportPdf'])->name('investments.reports.export-pdf');
Route::get('/app/investments/reports/export-excel', [InvestmentReportController::class, 'exportExcel'])->name('investments.reports.export-excel');

// Deposit Module Routes
use App\Http\Controllers\DepositController;
use App\Http\Controllers\DepositLedgerController;
use App\Http\Controllers\DepositImportController;
use App\Http\Controllers\DepositReportController;

// Deposit Routes
Route::resource('deposits', DepositController::class);
Route::get('/app/deposits/view-deposits', [DepositController::class, 'index'])->name('deposits.view-deposits');
Route::get('/app/deposits/add-deposit', [DepositController::class, 'create'])->name('deposits.add-deposit');
Route::get('/app/deposits/{deposit}', [DepositController::class, 'show'])->name('deposits.show');
Route::get('/app/deposits/{deposit}/edit', [DepositController::class, 'edit'])->name('deposits.edit');
Route::patch('/app/deposits/{deposit}/close', [DepositController::class, 'close'])->name('deposits.close');
Route::get('/app/deposits/member/{memberId}', [DepositController::class, 'getByMember'])->name('deposits.by-member');

// Deposit Ledger Routes
Route::get('/app/deposits/{deposit}/ledger', [DepositLedgerController::class, 'index'])->name('deposits.ledger.index');
Route::post('/app/deposits/{deposit}/ledger/deposit', [DepositLedgerController::class, 'deposit'])->name('deposits.ledger.deposit');
Route::post('/app/deposits/{deposit}/ledger/withdrawal', [DepositLedgerController::class, 'withdrawal'])->name('deposits.ledger.withdrawal');
Route::post('/app/deposits/{deposit}/ledger/accrue', [DepositLedgerController::class, 'accrue'])->name('deposits.ledger.accrue');
Route::post('/app/deposits/{deposit}/ledger/adjustment', [DepositLedgerController::class, 'adjustment'])->name('deposits.ledger.adjustment');
Route::put('/app/deposits/{deposit}/ledger/{ledgerEntry}', [DepositLedgerController::class, 'update'])->name('deposits.ledger.update');
Route::delete('/app/deposits/{deposit}/ledger/{ledgerEntry}', [DepositLedgerController::class, 'destroy'])->name('deposits.ledger.destroy');

// Deposit Import Routes
Route::get('/app/deposits/import', [DepositImportController::class, 'index'])->name('deposits.import');
Route::post('/app/deposits/import', [DepositImportController::class, 'import'])->name('deposits.import.store');
Route::get('/app/deposits/import/template', [DepositImportController::class, 'downloadTemplate'])->name('deposits.import.template');
Route::get('/app/deposits/import/history', [DepositImportController::class, 'history'])->name('deposits.import.history');
Route::get('/app/deposits/import/{import}', [DepositImportController::class, 'show'])->name('deposits.import.show');

// Deposit Report Routes
Route::get('/app/deposits/reports', [DepositReportController::class, 'index'])->name('deposits.reports');
Route::get('/app/deposits/reports/portfolio', [DepositReportController::class, 'portfolioReport'])->name('deposits.reports.portfolio');
Route::get('/app/deposits/reports/interest', [DepositReportController::class, 'interestReport'])->name('deposits.reports.interest');
Route::get('/app/deposits/reports/maturity', [DepositReportController::class, 'maturityReport'])->name('deposits.reports.maturity');
Route::get('/app/deposits/reports/export-pdf', [DepositReportController::class, 'exportPdf'])->name('deposits.reports.export-pdf');
Route::get('/app/deposits/reports/export-excel', [DepositReportController::class, 'exportExcel'])->name('deposits.reports.export-excel');
