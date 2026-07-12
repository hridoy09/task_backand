<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (generalSetting('maintenance_mode')) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'note'    => 'maintenance_mode',
                    'status'  => 'error',
                    'message' => __('Site is under maintenance'),
                ], Response::HTTP_FORBIDDEN);
            }

            return response()->view('theme::sections.maintenance');
        }

        return $next($request);
    }
}
