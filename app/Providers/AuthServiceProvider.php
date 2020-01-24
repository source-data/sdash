<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('access-panel', 'App\Gates\PanelAccessGates@canAccessPanel');
        Gate::define('view-panel', 'App\Gates\PanelAccessGates@canViewPanel');
        Gate::define('modify-panel', 'App\Gates\PanelAccessGates@canModifyPanel');
        Gate::define('modify-comment', 'App\Gates\CommentAccessGates@canModifyComment');
        Gate::define('modify-group', 'App\Gates\GroupAccessGates@canModifyGroup');
        Gate::define('view-group', 'App\Gates\GroupAccessGates@canViewGroup');

        Passport::routes();

        Passport::cookie('sdash_token');
    }
}
