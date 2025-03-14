<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class Ensure2FAVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            // Verifica si el usuario tiene 2FA habilitado
            $breezySession = DB::table('breezy_sessions')
                ->where('authenticatable_id', $user->id)
                ->where('authenticatable_type', get_class($user))
                ->first();

            if ($breezySession && $breezySession->two_factor_secret) {
                // Redirige a la vista de verificación de 2FA de Filament
                return redirect()->route('filament.admin.auth.two-factor');
            }
        }

        // Si no tiene 2FA habilitado, continúa con la solicitud
        return $next($request);
    }
}
