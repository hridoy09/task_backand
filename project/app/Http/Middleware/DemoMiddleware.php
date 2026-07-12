<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(generalSetting('demo_mode') && in_array($request->method(), ['POST', 'PATCH', 'DELETE'])) {
            $message = __('Sorry this is not allowed in demo mode');
            
            if($request->ajax()) {
                return response()->json([
                    'status' => 'info',
                    'message' => $message,
                    'success' => false
                ]);
            }
            
            return back()->withInfo($message);
        }
        
        return $next($request);
    }
}
