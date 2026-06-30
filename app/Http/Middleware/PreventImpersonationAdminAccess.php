<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventImpersonationAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ak je admin prepnutý na používateľa, zabráň prístupu do admin sekcie
        if (session()->has('impersonating_admin_id')) {
            return redirect()->route('dashboard')->with('error', 'Počas prepnutia na používateľa nemôžete pristupovať k admin sekcii. Najprv sa vráťte ako admin.');
        }

        return $next($request);
    }
}
