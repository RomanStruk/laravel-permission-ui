<?php
declare(strict_types=1);

namespace RomanStruk\LaravelPermissionUi;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use RomanStruk\LaravelPermissionUi\Http\Livewire\DashboardPage;
use RomanStruk\LaravelPermissionUi\Http\Livewire\EditPermission;
use RomanStruk\LaravelPermissionUi\Http\Livewire\Modal;
use RomanStruk\LaravelPermissionUi\Http\Livewire\PermissionPage;
use RomanStruk\LaravelPermissionUi\Http\Livewire\RolePage;
use RomanStruk\LaravelPermissionUi\View\Components\AppLayout;

class PermissionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/permissions-ui.php', 'permissions-ui');
        $this->app->afterResolving(BladeCompiler::class, function () {
            if (class_exists(Livewire::class)) {
                Livewire::component('dashboard-page', DashboardPage::class);
                Livewire::component('permission-page', PermissionPage::class);
                Livewire::component('role-page', RolePage::class);
                Livewire::component('edit-permission-ui-modal', EditPermission::class);
                Livewire::component('permission-ui-modal', Modal::class);
            }
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'permissions-ui');

        $this->configureRoutes();

        $this->loadViewComponentsAs('permissions-ui', [
            AppLayout::class,
        ]);

        $this->callAfterResolving(BladeCompiler::class, function ($blade): void {
            Blade::component('permissions-ui::components.modal', 'permissions-ui-modal');
//            dd($blade);
        });
    }

    /**
     * Налаштування роутів
     */
    protected function configureRoutes(): void
    {
//        Route::middlewareGroup('kaca', config('kaca.middleware', ['web']));

        Route::group([
//            'namespace' => '',
            'domain' => config('permissions-ui.domain'),
            'prefix' => config('permissions-ui.prefix'),
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/permissions-ui.php');
        });
    }
}
