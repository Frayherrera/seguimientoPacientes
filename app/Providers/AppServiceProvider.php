<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Si la petición llega por HTTPS (incluido un proxy con TrustProxies),
        // todas las URLs absolutas generadas deben seguir siendo https para no
        // provocar envíos mixtos http desde páginas https.
        if (request()->isSecure()) {
            URL::forceScheme('https');
        }

        $this->registrarPermisos();
    }

    /**
     * Registra un Gate por cada permiso de config/permisos.php.
     * El Administrador pasa todos; un usuario o rol inactivo no pasa ninguno.
     */
    private function registrarPermisos(): void
    {
        Gate::before(function (User $user) {
            if (! $user->estaHabilitado()) {
                return false;
            }

            return $user->esAdministrador() ? true : null;
        });

        foreach (config('permisos.permisos', []) as $permiso => $roles) {
            Gate::define($permiso, fn (User $user) => $user->tieneRol(...$roles));
        }
    }
}
