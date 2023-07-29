<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Traits\UserRolesTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStaffRoles
{
    use UserRolesTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!$this->isStaff(auth()->id())) {
            return response()->json(['error' => 'You do not have sufficient permissions.'], 403);
        }
        return $next($request);
    }
}
