<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * This is the actual fix for "hitting a protected /admin/* URL while
     * logged out shows a raw Laravel error page": Laravel's default
     * behavior calls route('login'), and since this app has no route
     * literally named "login" (ours is "admin.login"), that throws
     * RouteNotFoundException — which renders as an unstyled framework
     * error page. Pointing it at the real route name fixes that outright.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // A message the login page displays, so a guest who got bounced
        // here sees an explanation ("this area is for admins") instead of
        // landing on a bare login form with no context.
        session()->flash('auth_notice', "This area is for ZABIDA admins — please sign in to continue.");

        return route('admin.login');
    }
}
