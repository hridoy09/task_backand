<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileCompleteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->pc == 0) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'note'    => 'incomplete_profile',
                    'status'  => 'error',
                    'message' => __('Please complete your profile'),
                ], Response::HTTP_FORBIDDEN);
            }

            return to_route('user.profile_data')->withError(__('Please complete your profile'));
        }

        return $next($request);
    }
}
