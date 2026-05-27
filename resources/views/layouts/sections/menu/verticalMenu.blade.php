@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
$configData = Helper::appClasses();
$currentRouteName = Route::currentRouteName();

// Helper function to check if menu item is active
if (!function_exists('isMenuActive')) {
function isMenuActive($slug, $currentRoute) {
if ($currentRoute === $slug) {
return 'active';
}
if (is_array($slug)) {
foreach($slug as $s) {
if (str_contains($currentRoute, $s) && strpos($currentRoute, $s) === 0) {
return 'active open';
}
}
} else {
if (str_contains($currentRoute, $slug) && strpos($currentRoute, $slug) === 0) {
return 'active open';
}
}
return '';
}
}
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- ! Hide app brand if navbar-full -->
    @if(!isset($navbarFull))
    <div class="app-brand demo">
        <a href="{{ url('/layouts/vertical') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                @if(isset($appSettings) && $appSettings->logo)
                <img src="{{ asset('public/assets/img/branding/' . $appSettings->logo) }}" alt="Logo" style="max-height: 50px; max-width: 150px; width: auto; height: auto; object-fit: contain;">
                @else
                @include('_partials.macros',["height"=>20])
                @endif
            </span>
            <span class="app-brand-text demo menu-text fw-bold">{{ ($appSettings && $appSettings->app_name) ? $appSettings->app_name : config('variables.templateName') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>
    </div>
    @endif

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <!-- Dashboards -->
        <li class="menu-item {{ $currentRouteName === 'dashboard' ? 'active' : '' }}">
            <a href="{{ url('/layouts/vertical') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div>Dashboards</div>
            </a>
        </li>

        <!-- Member -->
        @permission('member-add')
        @permission('member-view')
        <li class="menu-item {{ (str_contains($currentRouteName, 'members') && strpos($currentRouteName, 'members') === 0) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div>Member</div>
            </a>
            <ul class="menu-sub">
                @permission('member-add')
                <li class="menu-item {{ $currentRouteName === 'members.add-member' ? 'active' : '' }}">
                    <a href="{{ url('/app/members/add-member') }}" class="menu-link">
                        <div>Add</div>
                    </a>
                </li>
                @endpermission
                @permission('member-view')
                <li class="menu-item {{ $currentRouteName === 'members.view-member' ? 'active' : '' }}">
                    <a href="{{ url('/app/members/view-member') }}" class="menu-link">
                        <div>View</div>
                    </a>
                </li>
                @endpermission

            </ul>
        </li>
        @endpermission

        <!-- Member -->
        @permission('monthly-deposit-installment-settings-view')
        <li class="menu-item {{ (str_contains($currentRouteName, 'members') && strpos($currentRouteName, 'members') === 0) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-settings"></i>
                <div>Deposit Installment Settings</div>
            </a>
            <ul class="menu-sub">
                @permission('monthly-deposit-installment-settings-add')
                <li class="menu-item {{ $currentRouteName === 'members.monthly-deposit-installment-settings.create' ? 'active' : '' }}">
                    <a href="{{ route('members.monthly-deposit-installment-settings.create') }}" class="menu-link">
                        <div>Add</div>
                    </a>
                </li>
                @endpermission
                @permission('monthly-deposit-installment-settings-view')
                <li class="menu-item {{ $currentRouteName === 'members.monthly-deposit-installment-settings.index' ? 'active' : '' }}">
                    <a href="{{ url('/app/members/monthly-deposit-installment-settings') }}" class="menu-link">
                        <div>View</div>
                    </a>
                </li>
                @endpermission

            </ul>
        </li>
        @endpermission
        @endpermission

        <!-- Employee -->
        @permission('employee-add')
        @permission('employee-view')
        <li class="menu-item {{ (str_contains($currentRouteName, 'app-employee') || str_contains($currentRouteName, 'employee')) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div>Employee</div>
            </a>
            <ul class="menu-sub">
                @permission('employee-add')
                <li class="menu-item {{ $currentRouteName === 'app-add-employee' ? 'active' : '' }}">
                    <a href="{{ url('app/employees/add-employee') }}" class="menu-link">
                        <div>Add</div>
                    </a>
                </li>
                @endpermission
                @permission('employee-view')
                <li class="menu-item {{ $currentRouteName === 'app-view-employee' ? 'active' : '' }}">
                    <a href="{{ url('app/employees/view-employee') }}" class="menu-link">
                        <div>View</div>
                    </a>
                </li>
                @endpermission
            </ul>
        </li>
        @endpermission
        @endpermission
        <!-- Expense -->
        <li class="menu-item {{ (str_contains($currentRouteName, 'app-expense') || str_contains($currentRouteName, 'expenses')) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                <div>Dedeuction</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ $currentRouteName === 'app-expenses' ? 'active' : '' }}">
                    <a href="{{ url('app/expenses') }}" class="menu-link">
                        <div>Manage</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Expense -->
        @php
        $hasDeductionPermission = false;
        if (Auth::check()) {
        $user = Auth::user();
        $hasDeductionPermission = $user->hasPermissionTo('deduction-add') ||
        $user->hasPermissionTo('view-deduction');
        }
        @endphp
        @if($hasDeductionPermission)
        <li class="menu-item {{ (str_contains($currentRouteName, 'deductions') || str_contains($currentRouteName, 'deductions')) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                <div>Deduction</div>
            </a>
            <ul class="menu-sub">
                @permission('add-deduction')
                <li class="menu-item {{ $currentRouteName === 'deductions.add-deduction' ? 'active' : '' }}">
                    <a href="{{ url('app/deductions/add-deduction') }}" class="menu-link">
                        <div>Add Deduction</div>
                    </a>
                </li>
                @endpermission
                @permission('add-deduction')
                <li class="menu-item {{ $currentRouteName === 'deductions.monthly-deduction-list' ? 'active' : '' }}">
                    <a href="{{ url('app/deductions/monthly-deduction-list') }}" class="menu-link">
                        <div>Monthly Deduction List</div>
                    </a>
                </li>
                @endpermission
                @permission('view-deduction')
                <li class="menu-item {{ $currentRouteName === 'deductions.view-deduction' ? 'active' : '' }}">
                    <a href="{{ url('app/deductions/view-deduction') }}" class="menu-link">
                        <div>View Deduction</div>
                    </a>
                </li>
                @endpermission
            </ul>
        </li>
        @endif
        <!-- Investment -->
        @php
        $hasInvestmentPermission = false;
        if (Auth::check()) {
        $user = Auth::user();
        $hasInvestmentPermission = $user->hasPermissionTo('investment-add') ||
        $user->hasPermissionTo('investment-view') ||
        $user->hasPermissionTo('payment-investment') ||
        $user->hasPermissionTo('investment-collection-add') ||
        $user->hasPermissionTo('investment-collection-view') ||
        $user->hasPermissionTo('investment-reports');
        }
        @endphp
        @if($hasInvestmentPermission)
        <li class="menu-item {{ (str_contains($currentRouteName, 'investment') || str_contains($currentRouteName, 'investments')) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                <div>Investment</div>
            </a>
            <ul class="menu-sub">
                @permission('investment-add')
                <li class="menu-item {{ $currentRouteName === 'investments.add-investment' ? 'active' : '' }}">
                    <a href="{{ url('/app/investments/add-investment') }}" class="menu-link">
                        <div>Add</div>
                    </a>
                </li>
                @endpermission
                @permission('investment-view')
                <li class="menu-item {{ $currentRouteName === 'investments.view-investments' ? 'active' : '' }}">
                    <a href="{{ url('/app/investments/view-investments') }}" class="menu-link">
                        <div>View</div>
                    </a>
                </li>
                @endpermission
                @permission('payment-investment')
                <li class="menu-item {{ $currentRouteName === 'investments.collection.index' ? 'active' : '' }}">
                    <a href="{{ url('/app/investments/collection') }}" class="menu-link">
                        <div>Payment Investment</div>
                    </a>
                </li>
                @endpermission
                @permission('investment-collection-add')
                <li class="menu-item {{ $currentRouteName === 'investments.collection.index' ? 'active' : '' }}">
                    <a href="{{ url('/app/investments/collection') }}" class="menu-link">
                        <div>Investment Collection</div>
                    </a>
                </li>
                @endpermission
                @permission('investment-collection-view')
                <li class="menu-item {{ $currentRouteName === 'investments.view-collection' ? 'active' : '' }}">
                    <a href="{{ url('/app/investments/view-collection') }}" class="menu-link">
                        <div>View Collection</div>
                    </a>
                </li>
                @endpermission

                @permission('investment-reports')
                <li class="menu-item {{ $currentRouteName === 'investments.reports' ? 'active' : '' }}">
                    <a href="{{ url('/app/investments/reports') }}" class="menu-link">
                        <div>Reports</div>
                    </a>
                </li>
                @endpermission
            </ul>
        </li>
        @endif

        <!-- Deposit -->
        @php
        $hasDepositPermission = false;
        if (Auth::check()) {
        $user = Auth::user();
        $hasAnuityToHpsmPermission = $user->hasPermissionTo('anuity-to-hpsm-add') ||
        $user->hasPermissionTo('anuity-to-hpsm-view');
        }
        @endphp
        @if($hasAnuityToHpsmPermission)
        <li class="menu-item {{ (str_contains($currentRouteName, 'anuity-to-hpsm') || str_contains($currentRouteName, 'anuity-to-hpsms')) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                <div>Anuity To HPSM</div>
            </a>
            <ul class="menu-sub">
                @permission('anuity-to-hpsm-add')
                <li class="menu-item {{ $currentRouteName === 'anuity-to-hpsm.add-anuity-to-hpsm' ? 'active' : '' }}">
                    <a href="{{ url('/app/anuity-to-hpsm/add-anuity-to-hpsm') }}" class="menu-link">
                        <div>Add</div>
                    </a>
                </li>
                @endpermission
                @permission('anuity-to-hpsm-view')
                <li class="menu-item {{ $currentRouteName === 'anuity-to-hpsm.view-anuity-to-hpsm' ? 'active' : '' }}">
                    <a href="{{ url('/app/anuity-to-hpsm/view-anuity-to-hpsm') }}" class="menu-link">
                        <div>View</div>
                    </a>
                </li>
                @endpermission

            </ul>
        </li>
        @endif
        <!-- Deposit -->
        @php
        $hasDepositPermission = false;
        if (Auth::check()) {
        $user = Auth::user();
        $hasDepositPermission = $user->hasPermissionTo('deposit-add') ||
        $user->hasPermissionTo('deposit-view') ||
        $user->hasPermissionTo('deposit-reports');
        }
        @endphp
        @if($hasDepositPermission)
        <li class="menu-item {{ (str_contains($currentRouteName, 'deposit') || str_contains($currentRouteName, 'deposits')) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                <div>Deposit</div>
            </a>
            <ul class="menu-sub">
                @permission('deposit-add')
                <li class="menu-item {{ $currentRouteName === 'deposits.add-deposit' ? 'active' : '' }}">
                    <a href="{{ url('/app/deposits/add-deposit') }}" class="menu-link">
                        <div>Add</div>
                    </a>
                </li>
                @endpermission
                @permission('deposit-view')
                <li class="menu-item {{ $currentRouteName === 'deposits.view-deposits' ? 'active' : '' }}">
                    <a href="{{ url('/app/deposits/view-deposits') }}" class="menu-link">
                        <div>View</div>
                    </a>
                </li>
                @endpermission

            </ul>
        </li>
        @endif

        <!-- Deposit -->
        @php
        $hasDepositPermission = false;
        if (Auth::check()) {
        $user = Auth::user();
        $hasDepositPermission = $user->hasPermissionTo('quard-add') ||
        $user->hasPermissionTo('quard-view') ||
        $user->hasPermissionTo('quard-edit') ||
        $user->hasPermissionTo('quard-delete') ||
        $user->hasPermissionTo('quard-reports');
        }
        @endphp
        @if($hasDepositPermission)
        <li class="menu-item {{ (str_contains($currentRouteName, 'quard') || str_contains($currentRouteName, 'deposits')) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                <div>Quard</div>
            </a>
            <ul class="menu-sub">
                @permission('quard-add')
                <li class="menu-item {{ $currentRouteName === 'quards.add-quard' ? 'active' : '' }}">
                    <a href="{{ url('/app/quards/add-quard') }}" class="menu-link">
                        <div>Add</div>
                    </a>
                </li>
                @endpermission
                @permission('quard-view')
                <li class="menu-item {{ $currentRouteName === 'quards.view-quards' ? 'active' : '' }}">
                    <a href="{{ url('/app/quards/view-quards') }}" class="menu-link">
                        <div>View</div>
                    </a>
                </li>
                @endpermission

            </ul>
        </li>
        @endif

        <!-- Deposit -->
        @php
        $hasDepositPermission = false;
        if (Auth::check()) {
        $user = Auth::user();
        $hasDepositPermission = $user->hasPermissionTo('quard-payment-add') ||
        $user->hasPermissionTo('quard-payment-view') ||
        $user->hasPermissionTo('quard-payment-edit') ||
        $user->hasPermissionTo('quard-payment-delete');
        }
        @endphp
        @if($hasDepositPermission)
        <li class="menu-item {{ (str_contains($currentRouteName, 'quard-payment') || str_contains($currentRouteName, 'deposits')) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-dollar"></i>
                <div>Quard Payment</div>
            </a>
            <ul class="menu-sub">
                @permission('quard-payment-add')
                <li class="menu-item {{ $currentRouteName === 'quard-payment.add-quard-payment' ? 'active' : '' }}">
                    <a href="{{ url('/app/quard-payment/add-quard-payment') }}" class="menu-link">
                        <div>Add</div>
                    </a>
                </li>
                @endpermission
                @permission('quard-payment-view')
                <li class="menu-item {{ $currentRouteName === 'quard-payment.view-quard-payment' ? 'active' : '' }}">
                    <a href="{{ url('/app/quard-payment/view-quard-payment') }}" class="menu-link">
                        <div>View</div>
                    </a>
                </li>
                @endpermission

            </ul>
        </li>
        @endif
        <!-- Report -->
        <li class="menu-item {{ (str_contains($currentRouteName, 'report') || str_contains($currentRouteName, '-report')) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-report"></i>
                <div>Report</div>
            </a>
            <ul class="menu-sub">

                <li class="menu-item {{ $currentRouteName === 'expense-report' ? 'active' : '' }}">
                    <a href="{{ url('app/expense-report') }}" class="menu-link">
                        <div>Expense Report</div>
                    </a>
                </li>
                <li class="menu-item {{ $currentRouteName === 'employee-list-report' ? 'active' : '' }}">
                    <a href="{{ url('app/employee-list-report') }}" class="menu-link">
                        <div>Employee List Report</div>
                    </a>
                </li>



            </ul>
        </li>

        <!-- Settings -->
        @permission('settings-view')
        <li class="menu-item {{ (str_contains($currentRouteName, 'settings') || str_contains($currentRouteName, 'basic-settings')) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div>Settings</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ ($currentRouteName === 'app-settings.index' || $currentRouteName === 'app-setting') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/app-settings') }}" class="menu-link">
                        <div>App Setting</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-designation' || $currentRouteName === 'designation') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/designation') }}" class="menu-link">
                        <div>Designation</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-expense-head' || $currentRouteName === 'expense-head') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/expense-head') }}" class="menu-link">
                        <div>Expense Head</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-income-head' || $currentRouteName === 'income-head') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/income-head') }}" class="menu-link">
                        <div>Income Head</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-nationality' || $currentRouteName === 'nationality') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/nationality') }}" class="menu-link">
                        <div>Nationality</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-payment-method' || $currentRouteName === 'payment-method') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/payment-method') }}" class="menu-link">
                        <div>Payment Method</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'settings-religion' || $currentRouteName === 'religion') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/religion') }}" class="menu-link">
                        <div>Religion</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-access-rules.index' || $currentRouteName === 'app-access-rules') ? 'active' : '' }}">
                    <a href="{{ url('rules') }}" class="menu-link">
                        <div>Assign Permissions</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-permission' || $currentRouteName === 'permissions') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/permission') }}" class="menu-link">
                        <div>Permissions</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-users' || $currentRouteName === 'users') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/users') }}" class="menu-link">
                        <div>Users</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-investment-type' || $currentRouteName === 'investment-type') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/investment-type') }}" class="menu-link">
                        <div>Investment Type</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-cache-clear' || $currentRouteName === 'cache-clear') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/cache-clear') }}" class="menu-link">
                        <div>Cache Clear</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-branch' || $currentRouteName === 'branch') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/branch') }}" class="menu-link">
                        <div>Branch</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-zone' || $currentRouteName === 'zone') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/zone') }}" class="menu-link">
                        <div>Zone</div>
                    </a>
                </li>
                <li class="menu-item {{ ($currentRouteName === 'app-settings-relation' || $currentRouteName === 'relation') ? 'active' : '' }}">
                    <a href="{{ url('app/settings/relation') }}" class="menu-link">
                        <div>Relation</div>
                    </a>
                </li>
            </ul>
        </li>
        @endpermission

    </ul>

</aside>
