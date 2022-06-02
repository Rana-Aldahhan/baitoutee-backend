<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class InsureNotRestricted
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()->user()->tokenCan('chef') || auth()->user()->tokenCan('deliveryman'))
        {
                if(auth()->user()->deleted_at != null)
                    return $this->errorResponse("حسابك مقيّد",403);
        }else if(auth()->user()->tokenCan('user'))
        {
            if(auth()->user()->deleted_at != null || now()>auth()->user()->campus_card_expiry_date)
                return $this->errorResponse("حسابك مقيّد",403);
        }
        return $next($request);
    }
}
