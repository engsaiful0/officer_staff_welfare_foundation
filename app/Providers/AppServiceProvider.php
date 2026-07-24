<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use App\Helpers\Helpers;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    // Register Helper class as a singleton
    $this->app->singleton('Helper', function ($app) {
      return new Helpers();
    });

    $this->app->singleton(
      \App\Services\Investment\InvestmentCalculatorFactory::class
    );

    $this->app->singleton(
      \App\Services\Investment\InvestmentService::class
    );

    $this->app->singleton(
      \App\Services\Investment\InvestmentCollectionService::class
    );
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
      if ($src !== null) {
        return [
          'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src) ? 'template-customizer-core-css' :
                    (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src) ? 'template-customizer-theme-css' : '')
        ];
      }
      return [];
    });

    Blade::if('permission', function ($permission) {
        return Auth::check() && Auth::user()->hasPermissionTo($permission);
    });
    
    // Configure pagination to use Bootstrap 5
    Paginator::defaultView('vendor.pagination.custom-bootstrap-5');
    Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-5');

    \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
      'investment' => \App\Models\Investment::class,
      'deposit' => \App\Models\Deposit::class,
    ]);
  }
}