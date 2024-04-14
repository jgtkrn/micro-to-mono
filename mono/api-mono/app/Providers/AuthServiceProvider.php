<?php

namespace App\Providers;

use App\Models\v2\Users\Group;
use App\Models\v2\Users\User;
use App\Policies\GroupPolicy;
use App\Policies\ManageUserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => ManageUserPolicy::class,
        Group::class => GroupPolicy::class,
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        //
    }
}
