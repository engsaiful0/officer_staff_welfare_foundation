<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\laravel_example\UserManagement;
use App\Http\Controllers\UserController;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\Crm;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\layouts\CollapsedMenu;
use App\Http\Controllers\layouts\ContentNavbar;
use App\Http\Controllers\layouts\ContentNavSidebar;
// use App\Http\Controllers\layouts\NavbarFull;
// use App\Http\Controllers\layouts\NavbarFullSidebar;
use App\Http\Controllers\layouts\Horizontal;
use App\Http\Controllers\layouts\Vertical;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;
// use App\Http\Controllers\front_pages\Landing;
// use App\Http\Controllers\front_pages\Pricing;
// use App\Http\Controllers\front_pages\Payment;
// use App\Http\Controllers\front_pages\Checkout;
// use App\Http\Controllers\front_pages\HelpCenter;
// use App\Http\Controllers\front_pages\HelpCenterArticle;
use App\Http\Controllers\apps\Email;
use App\Http\Controllers\apps\Chat;
use App\Http\Controllers\apps\Calendar;
use App\Http\Controllers\apps\Kanban;
use App\Http\Controllers\apps\EcommerceDashboard;
use App\Http\Controllers\apps\EcommerceProductList;
use App\Http\Controllers\apps\EcommerceProductAdd;
use App\Http\Controllers\apps\EcommerceProductCategory;
use App\Http\Controllers\apps\EcommerceOrderList;
use App\Http\Controllers\apps\EcommerceOrderDetails;
use App\Http\Controllers\apps\EcommerceCustomerAll;
use App\Http\Controllers\apps\EcommerceCustomerDetailsOverview;
use App\Http\Controllers\apps\EcommerceCustomerDetailsSecurity;
use App\Http\Controllers\apps\EcommerceCustomerDetailsBilling;
use App\Http\Controllers\apps\EcommerceCustomerDetailsNotifications;
use App\Http\Controllers\apps\EcommerceManageReviews;
use App\Http\Controllers\apps\EcommerceReferrals;
use App\Http\Controllers\apps\EcommerceSettingsDetails;
use App\Http\Controllers\apps\EcommerceSettingsPayments;
use App\Http\Controllers\apps\EcommerceSettingsCheckout;
use App\Http\Controllers\apps\EcommerceSettingsShipping;
use App\Http\Controllers\apps\EcommerceSettingsLocations;
use App\Http\Controllers\apps\EcommerceSettingsNotifications;
use App\Http\Controllers\apps\AcademyDashboard;
use App\Http\Controllers\apps\AcademyCourse;
use App\Http\Controllers\apps\AcademyCourseDetails;
use App\Http\Controllers\apps\LogisticsDashboard;
use App\Http\Controllers\apps\LogisticsFleet;
use App\Http\Controllers\apps\InvoiceList;
use App\Http\Controllers\apps\InvoicePreview;
use App\Http\Controllers\apps\InvoicePrint;
use App\Http\Controllers\apps\InvoiceEdit;
use App\Http\Controllers\apps\InvoiceAdd;
use App\Http\Controllers\apps\UserList;
use App\Http\Controllers\apps\UserViewAccount;
use App\Http\Controllers\apps\UserViewSecurity;
use App\Http\Controllers\apps\UserViewBilling;
use App\Http\Controllers\apps\UserViewNotifications;
use App\Http\Controllers\apps\UserViewConnections;
use App\Http\Controllers\apps\AccessRoles;
use App\Http\Controllers\apps\AccessPermission;
// use App\Http\Controllers\pages\UserProfile;
// use App\Http\Controllers\pages\UserTeams;
// use App\Http\Controllers\pages\UserProjects;
// use App\Http\Controllers\pages\UserConnections;
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
use App\Http\Controllers\authentications\LoginCover;
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
use App\Http\Controllers\wizard_example\Checkout as WizardCheckout;
use App\Http\Controllers\wizard_example\PropertyListing;
use App\Http\Controllers\wizard_example\CreateDeal;
// use App\Http\Controllers\modal\ModalExample;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\cards\CardAdvance;
use App\Http\Controllers\cards\CardStatistics;
use App\Http\Controllers\cards\CardAnalytics;
use App\Http\Controllers\cards\CardGamifications;
use App\Http\Controllers\cards\CardActions;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use App\Http\Controllers\extended_ui\Avatar;
use App\Http\Controllers\extended_ui\BlockUI;
use App\Http\Controllers\extended_ui\DragAndDrop;
use App\Http\Controllers\extended_ui\MediaPlayer;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\StarRatings;
use App\Http\Controllers\extended_ui\SweetAlert;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\extended_ui\TimelineBasic;
use App\Http\Controllers\extended_ui\TimelineFullscreen;
use App\Http\Controllers\extended_ui\Tour;
use App\Http\Controllers\extended_ui\Treeview;
use App\Http\Controllers\extended_ui\Misc;
use App\Http\Controllers\icons\Tabler;
use App\Http\Controllers\icons\FontAwesome;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_elements\CustomOptions;
use App\Http\Controllers\form_elements\Editors;
use App\Http\Controllers\form_elements\FileUpload;
use App\Http\Controllers\form_elements\Picker;
use App\Http\Controllers\form_elements\Selects;
use App\Http\Controllers\form_elements\Sliders;
use App\Http\Controllers\form_elements\Switches;
use App\Http\Controllers\form_elements\Extras;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\form_layouts\StickyActions;
use App\Http\Controllers\form_wizard\Numbered as FormWizardNumbered;
use App\Http\Controllers\form_wizard\Icons as FormWizardIcons;
use App\Http\Controllers\form_validation\Validation;
use App\Http\Controllers\tables\Basic as TablesBasic;
use App\Http\Controllers\tables\DatatableBasic;
use App\Http\Controllers\tables\DatatableAdvanced;
use App\Http\Controllers\tables\DatatableExtensions;
use App\Http\Controllers\charts\ApexCharts;
use App\Http\Controllers\charts\ChartJs;
use App\Http\Controllers\maps\Leaflet;
use App\Http\Controllers\settings\Semester;
use App\Http\Controllers\settings\Designation;
use App\Http\Controllers\settings\IncomeHead;
use App\Http\Controllers\settings\ExpenseHead;
use App\Http\Controllers\settings\FeeHead;
use App\Http\Controllers\settings\Board;
use App\Http\Controllers\settings\Religion;
use App\Http\Controllers\settings\Shift;
use App\Http\Controllers\settings\Technology;
use App\Http\Controllers\settings\AppSettings;
use App\Http\Controllers\settings\PaymentMethod;
use App\Http\Controllers\settings\Nationality;
use App\Http\Controllers\settings\AcademicYear;
use App\Http\Controllers\settings\Month;
use App\Http\Controllers\settings\SscPassingSession;
use App\Http\Controllers\settings\SscPassingYear;
use App\Http\Controllers\settings\User;
use App\Http\Controllers\settings\FeeSettings;
use App\Http\Controllers\settings\Branch;
use App\Http\Controllers\settings\Relation;
use App\Http\Controllers\CacheController;


use App\Http\Controllers\StudentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\FeeCollectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MyCollectionReportController;
use App\Http\Controllers\FeeSummaryController;
use App\Http\Controllers\StudentFinalReportController;
use App\Http\Controllers\FeeSettingsController;
use App\Http\Controllers\MonthlyFeeReportController;

// Fee Management Routes
Route::get('/app/fee-management/settings', [FeeSettingsController::class, 'index'])->name('fee-management.settings');
Route::post('/app/fee-settings', [FeeSettingsController::class, 'store'])->name('fee-settings.store');
Route::get('/app/fee-settings/data', [FeeSettingsController::class, 'getFeeSettings'])->name('fee-settings.data');
Route::post('/app/fee-settings/generate-payments', [FeeSettingsController::class, 'generateMonthlyPayments'])->name('fee-settings.generate-payments');
Route::post('/app/fee-settings/update-overdue', [FeeSettingsController::class, 'updateOverdueStatus'])->name('fee-settings.update-overdue');
Route::post('/app/fee-settings/update-fee-amounts', [FeeSettingsController::class, 'updateFeeAmounts'])->name('fee-settings.update-fee-amounts');

// Monthly Fee Report Routes
Route::get('/app/fee-management/monthly-report', [MonthlyFeeReportController::class, 'index'])->name('fee-management.monthly-report');
Route::get('/app/fee-management/monthly-report/data', [MonthlyFeeReportController::class, 'getReportData'])->name('fee-management.monthly-report.data');
Route::get('/app/fee-management/monthly-report/export-pdf', [MonthlyFeeReportController::class, 'exportPdf'])->name('fee-management.monthly-report.export-pdf');
Route::get('/app/fee-management/monthly-report/export-excel', [MonthlyFeeReportController::class, 'exportExcel'])->name('fee-management.monthly-report.export-excel');
Route::get('/app/fee-management/monthly-report/dashboard-stats', [MonthlyFeeReportController::class, 'getDashboardStats'])->name('fee-management.monthly-report.dashboard-stats');
Route::post('/app/fee-management/monthly-report/bulk-update', [MonthlyFeeReportController::class, 'bulkUpdateStatus'])->name('fee-management.monthly-report.bulk-update');

Route::get('/app/student-wise-report', [ReportController::class, 'studentWiseReport'])->name('student-wise-report');
Route::get('/app/student-wise-report/pdf', [ReportController::class, 'generateStudentWiseReport'])->name('student-wise-report.pdf');
Route::get('/app/student-list-report', [ReportController::class, 'studentListReport'])->name('student-list-report');
Route::get('/app/get-student-list', [ReportController::class, 'getStudentList'])->name('get-student-list');

Route::get('/app/fee-collection-report', [ReportController::class, 'feeCollectionReport'])->name('fee-collection-report');
Route::get('/app/fee-collection-report/excel', [ReportController::class, 'feeCollectionReportExcel'])->name('fee-collection-report.excel');
Route::get('/app/fee-collection/details-pdf/{id}', [ReportController::class, 'generateFeeCollectionDetailsPdf'])->name('fee-collection.details-pdf');
Route::get('/app/get-students-by-year/{academic_year_id}', [ReportController::class, 'getStudentsByYear'])->name('get-students-by-year');

Route::get('/app/expense-report', [ReportController::class, 'expenseReport'])->name('expense-report');
Route::get('/app/expense-report/pdf', [ReportController::class, 'expenseReportPdf'])->name('expense-report.pdf');
Route::get('/app/expense-report/excel', [ReportController::class, 'expenseReportExcel'])->name('expense-report.excel');

// Report Routes
Route::get('/app/employee-list-report', [ReportController::class, 'employeeListReport'])->name('employee-list-report');
Route::get('/app/teacher-list-report', [ReportController::class, 'teacherListReport'])->name('teacher-list-report');
Route::get('/app/head-wise-fee-report', [ReportController::class, 'headWiseFeeReport'])->name('head-wise-fee-report');
Route::get('/app/head-wise-fee-report/excel', [ReportController::class, 'headWiseFeeReportExcel'])->name('head-wise-fee-report.excel');

// My Collection Report Routes
Route::get('/app/my-collection-report', [MyCollectionReportController::class, 'index'])->name('my-collection-report');
Route::get('/app/my-collection-report/excel', [MyCollectionReportController::class, 'exportExcel'])->name('my-collection-report.excel');
Route::get('/app/my-collection-report/pdf', [MyCollectionReportController::class, 'exportPdf'])->name('my-collection-report.pdf');

// Fee Summary Routes
Route::get('/app/fee-summary', [FeeSummaryController::class, 'index'])->name('fee-summary.index');
Route::get('/app/fee-summary/student/{student}', [FeeSummaryController::class, 'showStudentReport'])->name('fee-summary.student-report');
Route::get('/app/fee-summary/student/{student}/print', [FeeSummaryController::class, 'printStudentReport'])->name('fee-summary.print-student-report');
Route::get('/app/fee-summary/student/{student}/export-pdf', [FeeSummaryController::class, 'exportStudentReportPdf'])->name('fee-summary.export-student-pdf');
Route::get('/app/fee-summary/student/{student}/export-excel', [FeeSummaryController::class, 'exportStudentReportExcel'])->name('fee-summary.export-student-excel');
Route::get('/app/fee-summary/export-all-pdf', [FeeSummaryController::class, 'exportAllStudentsPdf'])->name('fee-summary.export-all-pdf');
Route::get('/app/fee-summary/export-all-excel', [FeeSummaryController::class, 'exportAllStudentsExcel'])->name('fee-summary.export-all-excel');
Route::get('/app/fee-summary/stats', [FeeSummaryController::class, 'getStats'])->name('fee-summary.stats');
Route::post('/app/fee-summary/student/{student}/update', [FeeSummaryController::class, 'updateStudentSummary'])->name('fee-summary.update-student');
Route::get('/app/fee-summary/student/{student}/breakdown', [FeeSummaryController::class, 'getStudentFeeBreakdown'])->name('fee-summary.student-breakdown');

// Student Final Report Routes
Route::get('/app/final-report/student/{student}', [StudentFinalReportController::class, 'generateFinalReport'])->name('final-report.student');
Route::get('/app/final-report/student/{student}/completion-certificate', [StudentFinalReportController::class, 'generateCompletionCertificate'])->name('final-report.completion-certificate');
Route::get('/app/final-report/completed-students', [StudentFinalReportController::class, 'getCompletedStudents'])->name('final-report.completed-students');
Route::post('/app/final-report/bulk-certificates', [StudentFinalReportController::class, 'bulkGenerateCompletionCertificates'])->name('final-report.bulk-certificates');
Route::get('/app/final-report/export-excel', [StudentFinalReportController::class, 'exportFinalReports'])->name('final-report.export-excel');
Route::get('/app/final-report/completion-stats', [StudentFinalReportController::class, 'getCompletionStatistics'])->name('final-report.completion-stats');

// Test route for My Collection Report
Route::get('/test-my-collection-report', function() {
    $feeCollections = App\Models\FeeCollect::with(['user', 'student'])->get();
    $expenses = App\Models\Expense::with(['user', 'expenseHead'])->get();
    $users = App\Models\User::with('rule')->get();
    
    return response()->json([
        'message' => 'My Collection Report Test',
        'fee_collections_count' => $feeCollections->count(),
        'expenses_count' => $expenses->count(),
        'users_count' => $users->count(),
        'sample_fee_collection' => $feeCollections->first(),
        'sample_expense' => $expenses->first(),
        'sample_user' => $users->first()
    ]);
});

// Test route
Route::get('/test-head-wise-fee', function() {
    $students = App\Models\Student::all();
    $feeHeads = App\Models\FeeHead::all();
    $feeCollections = App\Models\FeeCollect::with(['student'])->get();
    
    return response()->json([
        'students_count' => $students->count(),
        'fee_heads_count' => $feeHeads->count(),
        'fee_collections_count' => $feeCollections->count(),
        'first_fee_collection' => $feeCollections->first()
    ]);
});

// Test phone duplicate check
Route::get('/test-phone-check', function() {
    $students = App\Models\Student::select('personal_number')->get();
    return response()->json([
        'message' => 'Phone duplicate check test',
        'existing_phones' => $students->pluck('personal_number')->toArray()
    ]);
});

// Test student form
Route::get('/test-student-form', function() {
    return view('content.students.create');
});

// Test employee route
Route::get('/test-employees', function() {
    $employees = App\Models\Employee::with('designation')->get();
    return response()->json(['data' => $employees]);
});


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

// Teacher Routes
Route::get('/app/teachers/view-teacher', [TeacherController::class, 'index'])->name('teachers.view-teacher');
Route::get('/app/teachers/export-excel', [TeacherController::class, 'exportExcel'])->name('teachers.export-excel');
Route::get('/app/teachers/export-pdf', [TeacherController::class, 'exportPdf'])->name('teachers.export-pdf');
Route::get('/app/teachers/add-teacher', [TeacherController::class, 'create'])->name('teachers.add-teacher')->middleware('permission:teacher-add');
Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store')->middleware('permission:teacher-add');
Route::get('/app/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit')->middleware('permission:teacher-edit');
Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update')->middleware('permission:teacher-edit');
Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy')->middleware('permission:teacher-delete');

Route::get('/app/settings/academic-year', [AcademicYear::class, 'index'])->name('app-settings-academic-year');
Route::get('/app/settings/get-academic-year', [AcademicYear::class, 'getAcademicYear'])->name('app-settings-get-academic-year');
Route::post('/app/settings/academic-year', [AcademicYear::class, 'store'])->name('app-settings-academic-year.store');
Route::put('/app/settings/academic-year/{id}', [AcademicYear::class, 'update'])->name('app-settings-academic-year.update');
Route::delete('/app/settings/academic-year/{id}', [AcademicYear::class, 'destroy'])->name('app-settings-academic-year.destroy');


Route::get('/app/settings/ssc-session', [SscPassingSession::class, 'index'])->name('app-settings-ssc-session');
Route::get('/app/settings/get-ssc-session', [SscPassingSession::class, 'getSscSession'])->name('app-settings-get-ssc-session');
Route::post('/app/settings/ssc-session', [SscPassingSession::class, 'store'])->name('app-settings-ssc-session.store');
Route::get('/app/settings/ssc-session/{id}/edit', [SscPassingSession::class, 'edit'])->name('app-settings-ssc-session.edit');
Route::put('/app/settings/ssc-session/{id}', [SscPassingSession::class, 'update'])->name('app-settings-ssc-session.update');
Route::delete('/app/settings/ssc-session/{id}', [SscPassingSession::class, 'destroy'])->name('app-settings-ssc-session.destroy');

Route::get('/app/settings/ssc-passing-year', [SscPassingYear::class, 'index'])->name('app-settings-ssc-passing-year');
Route::get('/app/settings/get-ssc-passing-year', [SscPassingYear::class, 'getSscPassingYear'])->name('app-settings-get-ssc-passing-year');
Route::post('/app/settings/ssc-passing-year', [SscPassingYear::class, 'store'])->name('app-settings-ssc-passing-year.store');
Route::put('/app/settings/ssc-passing-year/{id}', [SscPassingYear::class, 'update'])->name('app-settings-ssc-passing-year.update');
Route::delete('/app/settings/ssc-passing-year/{id}', [SscPassingYear::class, 'destroy'])->name('app-settings-ssc-passing-year.destroy');

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


Route::get('/app/settings/technology', [Technology::class, 'index'])->name('app-settings-technology');
Route::get('/app/settings/get-technology', [Technology::class, 'getTechnology'])->name('app-settings-get-technology');
Route::post('/app/settings/technology', [Technology::class, 'store'])->name('app-settings-technology.store');
Route::put('/app/settings/technology/{id}', [Technology::class, 'update'])->name('app-settings-technology.update');
Route::delete('/app/settings/technology/{id}', [Technology::class, 'destroy'])->name('app-settings-technology.destroy');


Route::get('students/{id}/pdf', [StudentController::class, 'generatePdf'])->name('students.pdf');

Route::resource('students', StudentController::class)->except(['create', 'edit', 'destroy']);
Route::post('/get-last-serial', [StudentController::class, 'getLastSerialNumber']);
Route::get('/app/students/add-student', [StudentController::class, 'create'])->name('students.add-student')->middleware('permission:student-add');
Route::get('/app/students/view-student', [StudentController::class, 'index'])->name('students.view-student');
Route::get('/app/students/export-excel', [StudentController::class, 'exportExcel'])->name('students.export-excel');
Route::get('/app/students/export-pdf', [StudentController::class, 'exportPdf'])->name('students.export-pdf');
Route::post('/students/check-personal-number-duplicate', [StudentController::class, 'checkPersonalNumberDuplicate'])->name('students.check-personal-number-duplicate');
Route::post('/students/check-email-duplicate', [StudentController::class, 'checkEmailDuplicate'])->name('students.check-email-duplicate');
Route::get('/app/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit')->middleware('permission:student-edit');
Route::delete('/app/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy')->middleware('permission:student-delete');

Route::get('/app/settings/app-settings', [AppSettings::class, 'index'])->name('app-settings.index');
Route::put('/app/settings/app-settings/{id}', [AppSettings::class, 'update'])->name('app-settings.update');




Route::get('/app/settings/religion', [Religion::class, 'index'])->name('settings-religion');
Route::get('/app/settings/get-religion', [Religion::class, 'getReligions'])->name('settings-religion.get-religion');
Route::post('/app/settings/religion', [Religion::class, 'store'])->name('settings-religion.store');
Route::put('/app/settings/religion/{id}', [Religion::class, 'update'])->name('settings-religion.update');
Route::delete('/app/settings/religion/{id}', [Religion::class, 'destroy'])->name('settings-religion.destroy');

Route::get('/app/settings/shift', [Shift::class, 'index'])->name('settings-shift');
Route::get('/app/settings/shift/get', [Shift::class, 'getShifts'])->name('settings-shift.get');
Route::post('/app/settings/shift', [Shift::class, 'store'])->name('settings-shift.store');
Route::put('/app/settings/shift/{id}', [Shift::class, 'update'])->name('settings-shift.update');
Route::delete('/app/settings/shift/{id}', [Shift::class, 'destroy'])->name('settings-shift.destroy');


Route::get('/app/settings/board', [Board::class, 'index'])->name('app-settings-board');
Route::get('/app/settings/get-board', [Board::class, 'getBoard'])->name('app-settings-get-board');
Route::post('/app/settings/board', [Board::class, 'store'])->name('app-settings-board.store');
Route::put('/app/settings/board/{id}', [Board::class, 'update'])->name('app-settings-board.update');
Route::delete('/app/settings/board/{id}', [Board::class, 'destroy'])->name('app-settings-board.destroy');

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

Route::get('/app/settings/fee-head', [FeeHead::class, 'index'])->name('app-settings-fee-head');
Route::get('/app/settings/get-fee-head', [FeeHead::class, 'getFeeHead'])->name('app-settings-get-fee-head');
Route::post('/app/settings/fee-head', [FeeHead::class, 'store'])->name('app-settings-fee-head.store');
Route::put('/app/settings/fee-head', [FeeHead::class, 'update'])->name('app-settings-fee-head.update');
Route::put('/app/settings/fee-head/{id}', [FeeHead::class, 'update'])->name('app-settings-fee-head.update-by-id');
Route::delete('/app/settings/fee-head/{id}', [FeeHead::class, 'destroy'])->name('app-settings-fee-head.destroy');

Route::get('/app/settings/fee-settings', [FeeSettings::class, 'index'])->name('app-settings-fee-settings');
Route::get('/app/settings/get-fee-settings', [FeeSettings::class, 'getFeeSettings'])->name('app-settings-get-fee-settings');
Route::post('/app/settings/fee-settings', [FeeSettings::class, 'store'])->name('app-settings-fee-settings.store');
Route::put('/app/settings/fee-settings/{id}', [FeeSettings::class, 'update'])->name('app-settings-fee-settings.update');
Route::delete('/app/settings/fee-settings/{id}', [FeeSettings::class, 'destroy'])->name('app-settings-fee-settings.destroy');

Route::get('/app/settings/branch', [Branch::class, 'index'])->name('app-settings-branch');
Route::get('/app/settings/get-branch', [Branch::class, 'getbranch'])->name('app-settings-get-branch');
Route::post('/app/settings/branch', [Branch::class, 'store'])->name('app-settings-branch.store');
Route::put('/app/settings/branch/{id}', [Branch::class, 'update'])->name('app-settings-branch.update');
Route::delete('/app/settings/branch/{id}', [Branch::class, 'destroy'])->name('app-settings-branch.destroy');

Route::get('/app/settings/relation', [Relation::class, 'index'])->name('app-settings-relation');
Route::get('/app/settings/get-relation', [Relation::class, 'getrelation'])->name('app-settings-get-relation');
Route::post('/app/settings/relation', [Relation::class, 'store'])->name('app-settings-relation.store');
Route::put('/app/settings/relation/{id}', [Relation::class, 'update'])->name('app-settings-relation.update');
Route::delete('/app/settings/relation/{id}', [Relation::class, 'destroy'])->name('app-settings-relation.destroy');

// Cache Management Routes
Route::get('/app/settings/cache-clear', [CacheController::class, 'index'])->name('app-settings-cache-clear');
Route::get('/app/settings/clear-cache', [CacheController::class, 'clearCache'])->name('clear-cache');

// Test URL Route
Route::get('/test-url', function() {
    return view('test-url');
});

// Test Member CRUD Route
Route::get('/test-member-crud', function() {
    $members = App\Models\Member::with(['designation', 'branch', 'religion', 'introducer'])->get();
    return response()->json([
        'message' => 'Member CRUD Test',
        'members_count' => $members->count(),
        'sample_member' => $members->first(),
        'routes' => [
            'index' => route('members.index'),
            'create' => route('members.create'),
            'store' => route('members.store'),
        ]
    ]);
});

// Test Member DataTable Route
Route::get('/test-member-datatable', function() {
    $request = new Illuminate\Http\Request();
    $request->merge(['draw' => 1, 'start' => 0, 'length' => 10]);
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    
    $controller = new App\Http\Controllers\MemberController();
    return $controller->index($request);
});

// Main Page Route
Route::get('/', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::post('/auth/login-basic', [LoginBasic::class, 'login'])->name('auth-login-basic.post');

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

// Front Pages
// Route::get('/front-pages/landing', [Landing::class, 'index'])->name('front-pages-landing');
// Route::get('/front-pages/pricing', [Pricing::class, 'index'])->name('front-pages-pricing');
// Route::get('/front-pages/payment', [Payment::class, 'index'])->name('front-pages-payment');
// Route::get('/front-pages/checkout', [Checkout::class, 'index'])->name('front-pages-checkout');
// Route::get('/front-pages/help-center', [HelpCenter::class, 'index'])->name('front-pages-help-center');
// Route::get('/front-pages/help-center-article', [HelpCenterArticle::class, 'index'])->name('front-pages-help-center-article');

// apps
Route::get('/app/email', [Email::class, 'index'])->name('app-email');
Route::get('/app/chat', [Chat::class, 'index'])->name('app-chat');
Route::get('/app/calendar', [Calendar::class, 'index'])->name('app-calendar');
Route::get('/app/kanban', [Kanban::class, 'index'])->name('app-kanban');
Route::get('/app/ecommerce/dashboard', [EcommerceDashboard::class, 'index'])->name('app-ecommerce-dashboard');
Route::get('/app/ecommerce/product/list', [EcommerceProductList::class, 'index'])->name('app-ecommerce-product-list');
Route::get('/app/ecommerce/product/add', [EcommerceProductAdd::class, 'index'])->name('app-ecommerce-product-add');
Route::get('/app/ecommerce/product/category', [EcommerceProductCategory::class, 'index'])->name('app-ecommerce-product-category');
Route::get('/app/ecommerce/order/list', [EcommerceOrderList::class, 'index'])->name('app-ecommerce-order-list');
Route::get('/app/ecommerce/order/details', [EcommerceOrderDetails::class, 'index'])->name('app-ecommerce-order-details');
Route::get('/app/ecommerce/customer/all', [EcommerceCustomerAll::class, 'index'])->name('app-ecommerce-customer-all');
Route::get('/app/ecommerce/customer/details/overview', [EcommerceCustomerDetailsOverview::class, 'index'])->name('app-ecommerce-customer-details-overview');
Route::get('/app/ecommerce/customer/details/security', [EcommerceCustomerDetailsSecurity::class, 'index'])->name('app-ecommerce-customer-details-security');
Route::get('/app/ecommerce/customer/details/billing', [EcommerceCustomerDetailsBilling::class, 'index'])->name('app-ecommerce-customer-details-billing');
Route::get('/app/ecommerce/customer/details/notifications', [EcommerceCustomerDetailsNotifications::class, 'index'])->name('app-ecommerce-customer-details-notifications');
Route::get('/app/ecommerce/manage/reviews', [EcommerceManageReviews::class, 'index'])->name('app-ecommerce-manage-reviews');
Route::get('/app/ecommerce/referrals', [EcommerceReferrals::class, 'index'])->name('app-ecommerce-referrals');
Route::get('/app/ecommerce/settings/details', [EcommerceSettingsDetails::class, 'index'])->name('app-ecommerce-settings-details');
Route::get('/app/ecommerce/settings/payments', [EcommerceSettingsPayments::class, 'index'])->name('app-ecommerce-settings-payments');
Route::get('/app/ecommerce/settings/checkout', [EcommerceSettingsCheckout::class, 'index'])->name('app-ecommerce-settings-checkout');
Route::get('/app/ecommerce/settings/shipping', [EcommerceSettingsShipping::class, 'index'])->name('app-ecommerce-settings-shipping');
Route::get('/app/ecommerce/settings/locations', [EcommerceSettingsLocations::class, 'index'])->name('app-ecommerce-settings-locations');
Route::get('/app/ecommerce/settings/notifications', [EcommerceSettingsNotifications::class, 'index'])->name('app-ecommerce-settings-notifications');
Route::get('/app/academy/dashboard', [AcademyDashboard::class, 'index'])->name('app-academy-dashboard');
Route::get('/app/academy/course', [AcademyCourse::class, 'index'])->name('app-academy-course');
Route::get('/app/academy/course-details', [AcademyCourseDetails::class, 'index'])->name('app-academy-course-details');
Route::get('/app/logistics/dashboard', [LogisticsDashboard::class, 'index'])->name('app-logistics-dashboard');
Route::get('/app/logistics/fleet', [LogisticsFleet::class, 'index'])->name('app-logistics-fleet');
Route::get('/app/invoice/list', [InvoiceList::class, 'index'])->name('app-invoice-list');
Route::get('/app/invoice/preview', [InvoicePreview::class, 'index'])->name('app-invoice-preview');
Route::get('/app/invoice/print', [InvoicePrint::class, 'index'])->name('app-invoice-print');
Route::get('/app/invoice/edit', [InvoiceEdit::class, 'index'])->name('app-invoice-edit');
Route::get('/app/invoice/add', [InvoiceAdd::class, 'index'])->name('app-invoice-add');
Route::get('/app/user/list', [UserList::class, 'index'])->name('app-user-list');
Route::get('/app/user/view/account', [UserViewAccount::class, 'index'])->name('app-user-view-account');
Route::get('/app/user/view/security', [UserViewSecurity::class, 'index'])->name('app-user-view-security');
Route::get('/app/user/view/billing', [UserViewBilling::class, 'index'])->name('app-user-view-billing');
Route::get('/app/user/view/notifications', [UserViewNotifications::class, 'index'])->name('app-user-view-notifications');
Route::get('/app/user/view/connections', [UserViewConnections::class, 'index'])->name('app-user-view-connections');
Route::get('/app/access-roles', [AccessRoles::class, 'index'])->name('app-access-roles');
Route::get('/app/access-permission', [AccessPermission::class, 'index'])->name('app-access-permission');

// pages
// Route::get('/pages/profile-user', [UserProfile::class, 'index'])->name('pages-profile-user');
// Route::get('/pages/profile-teams', [UserTeams::class, 'index'])->name('pages-profile-teams');
// Route::get('/pages/profile-projects', [UserProjects::class, 'index'])->name('pages-profile-projects');
// Route::get('/pages/profile-connections', [UserConnections::class, 'index'])->name('pages-profile-connections');
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

// wizard example
Route::get('/wizard/ex-checkout', [WizardCheckout::class, 'index'])->name('wizard-ex-checkout');
Route::get('/wizard/ex-property-listing', [PropertyListing::class, 'index'])->name('wizard-ex-property-listing');
Route::get('/wizard/ex-create-deal', [CreateDeal::class, 'index'])->name('wizard-ex-create-deal');

// modal
// Route::get('/modal-examples', [ModalExample::class, 'index'])->name('modal-examples');

// cards
Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');
Route::get('/cards/advance', [CardAdvance::class, 'index'])->name('cards-advance');
Route::get('/cards/statistics', [CardStatistics::class, 'index'])->name('cards-statistics');
Route::get('/cards/analytics', [CardAnalytics::class, 'index'])->name('cards-analytics');
Route::get('/cards/gamifications', [CardGamifications::class, 'index'])->name('cards-gamifications');
Route::get('/cards/actions', [CardActions::class, 'index'])->name('cards-actions');

// User Interface
Route::get('/ui/accordion', [Accordion::class, 'index'])->name('ui-accordion');
Route::get('/ui/alerts', [Alerts::class, 'index'])->name('ui-alerts');
Route::get('/ui/badges', [Badges::class, 'index'])->name('ui-badges');
Route::get('/ui/buttons', [Buttons::class, 'index'])->name('ui-buttons');
Route::get('/ui/carousel', [Carousel::class, 'index'])->name('ui-carousel');
Route::get('/ui/collapse', [Collapse::class, 'index'])->name('ui-collapse');
Route::get('/ui/dropdowns', [Dropdowns::class, 'index'])->name('ui-dropdowns');
Route::get('/ui/footer', [Footer::class, 'index'])->name('ui-footer');
Route::get('/ui/list-groups', [ListGroups::class, 'index'])->name('ui-list-groups');
Route::get('/ui/modals', [Modals::class, 'index'])->name('ui-modals');
Route::get('/ui/navbar', [Navbar::class, 'index'])->name('ui-navbar');
Route::get('/ui/offcanvas', [Offcanvas::class, 'index'])->name('ui-offcanvas');
Route::get('/ui/pagination-breadcrumbs', [PaginationBreadcrumbs::class, 'index'])->name('ui-pagination-breadcrumbs');
Route::get('/ui/progress', [Progress::class, 'index'])->name('ui-progress');
Route::get('/ui/spinners', [Spinners::class, 'index'])->name('ui-spinners');
Route::get('/ui/tabs-pills', [TabsPills::class, 'index'])->name('ui-tabs-pills');
Route::get('/ui/toasts', [Toasts::class, 'index'])->name('ui-toasts');
Route::get('/ui/tooltips-popovers', [TooltipsPopovers::class, 'index'])->name('ui-tooltips-popovers');
Route::get('/ui/typography', [Typography::class, 'index'])->name('ui-typography');

// extended ui
Route::get('/extended/ui-avatar', [Avatar::class, 'index'])->name('extended-ui-avatar');
Route::get('/extended/ui-blockui', [BlockUI::class, 'index'])->name('extended-ui-blockui');
Route::get('/extended/ui-drag-and-drop', [DragAndDrop::class, 'index'])->name('extended-ui-drag-and-drop');
Route::get('/extended/ui-media-player', [MediaPlayer::class, 'index'])->name('extended-ui-media-player');
Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name('extended-ui-perfect-scrollbar');
Route::get('/extended/ui-star-ratings', [StarRatings::class, 'index'])->name('extended-ui-star-ratings');
Route::get('/extended/ui-sweetalert2', [SweetAlert::class, 'index'])->name('extended-ui-sweetalert2');
Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');
Route::get('/extended/ui-timeline-basic', [TimelineBasic::class, 'index'])->name('extended-ui-timeline-basic');
Route::get('/extended/ui-timeline-fullscreen', [TimelineFullscreen::class, 'index'])->name('extended-ui-timeline-fullscreen');
Route::get('/extended/ui-tour', [Tour::class, 'index'])->name('extended-ui-tour');
Route::get('/extended/ui-treeview', [Treeview::class, 'index'])->name('extended-ui-treeview');
Route::get('/extended/ui-misc', [Misc::class, 'index'])->name('extended-ui-misc');

// icons
Route::get('/icons/tabler', [Tabler::class, 'index'])->name('icons-tabler');
Route::get('/icons/font-awesome', [FontAwesome::class, 'index'])->name('icons-font-awesome');

// form elements
Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');
Route::get('/forms/custom-options', [CustomOptions::class, 'index'])->name('forms-custom-options');
Route::get('/forms/editors', [Editors::class, 'index'])->name('forms-editors');
Route::get('/forms/file-upload', [FileUpload::class, 'index'])->name('forms-file-upload');
Route::get('/forms/pickers', [Picker::class, 'index'])->name('forms-pickers');
Route::get('/forms/selects', [Selects::class, 'index'])->name('forms-selects');
Route::get('/forms/sliders', [Sliders::class, 'index'])->name('forms-sliders');
Route::get('/forms/switches', [Switches::class, 'index'])->name('forms-switches');
Route::get('/forms/extras', [Extras::class, 'index'])->name('forms-extras');

// form layouts
Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');
Route::get('/form/layouts-sticky', [StickyActions::class, 'index'])->name('form-layouts-sticky');

// form wizards
Route::get('/form/wizard-numbered', [FormWizardNumbered::class, 'index'])->name('form-wizard-numbered');
Route::get('/form/wizard-icons', [FormWizardIcons::class, 'index'])->name('form-wizard-icons');
Route::get('/form/validation', [Validation::class, 'index'])->name('form-validation');

// tables
Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');
Route::get('/tables/datatables-basic', [DatatableBasic::class, 'index'])->name('tables-datatables-basic');
Route::get('/tables/datatables-advanced', [DatatableAdvanced::class, 'index'])->name('tables-datatables-advanced');
Route::get('/tables/datatables-extensions', [DatatableExtensions::class, 'index'])->name('tables-datatables-extensions');

// charts
Route::get('/charts/apex', [ApexCharts::class, 'index'])->name('charts-apex');
Route::get('/charts/chartjs', [ChartJs::class, 'index'])->name('charts-chartjs');

// maps
Route::get('/maps/leaflet', [Leaflet::class, 'index'])->name('maps-leaflet');

// laravel example
Route::get('/laravel/user-management', [UserManagement::class, 'UserManagement'])->name('laravel-example-user-management');
Route::resource('/user-list', UserManagement::class);

use App\Http\Controllers\RuleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MemberController;

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
