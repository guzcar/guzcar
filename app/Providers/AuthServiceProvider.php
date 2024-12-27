<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\TipoVehiculo;
use App\Policies\TipoVehiculoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        TipoVehiculo::class => TipoVehiculoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
