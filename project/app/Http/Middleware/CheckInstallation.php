<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    public function handle(Request $request, Closure $next)
    {
        $envFile = base_path('.env');
        $appKey = env('APP_KEY');

        return $next($request);
    }
}
