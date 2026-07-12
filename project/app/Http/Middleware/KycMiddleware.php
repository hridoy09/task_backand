<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KycMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $kycRequred = $user->kyc_required ?? false;

        if($kycRequred && !$user->kyc_verified) {
            return to_route('user.kyc.form')->withError(__('Please submit your kyc data'));
        }
            
        return $next($request);
    }
}
