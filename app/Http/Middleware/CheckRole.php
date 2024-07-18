<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
         $user = Auth::user();

        if (!$user || !$user->role) {
            return response()->json(['message' => 'Fobidden, No user or permission'], 403);
        }

        $rolesArray = explode('|', $roles);

       if (!in_array($user->role->name, $rolesArray)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
        
    }
}
