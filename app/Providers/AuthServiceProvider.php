<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        // 開発者グループ
        Gate::define('system-only', function ($user) {
            return ($user->role == 10);
        });
        // 管理者グループ
        Gate::define('admin-higher', function ($user) {
            return ($user->role >= 8);
        });
        Gate::define('admin-midle', function ($user) {
            return ($user->role >= 5);
        });
        // 一般ユーザ以上（つまり全権限）に許可
        Gate::define('user-higher', function ($user) {
            return ($user->role > 0 && $user->role <= 10);
        });
    }
}
