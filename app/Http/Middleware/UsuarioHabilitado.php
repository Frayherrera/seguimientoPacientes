<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cierra la sesión de un usuario que fue desactivado (o cuyo rol fue
 * desactivado/eliminado) mientras tenía la sesión abierta.
 */
class UsuarioHabilitado
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->estaHabilitado()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta está desactivada. Contacta al administrador del sistema.',
            ]);
        }

        return $next($request);
    }
}
