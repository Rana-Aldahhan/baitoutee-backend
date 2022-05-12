<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponser;

use function PHPUnit\Framework\isNull;

class InsurePhoneNumberIsVerified
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
        //request should have phone or phone_number input
        $phone=$request['phone_number'];
        $phone==null?$request['phone']:$request['phone_number'];
        $numberOfMinutesOfValidVerrificationCode=10;
        if($phone !=null){
           $verifiedAt= DB::table('phone_number_verifications')
            ->where('phone_number', $phone)->select('verified_at')->first()->verified_at;
            if(is_null($verifiedAt) || now()->diffInMinutes($verifiedAt)>$numberOfMinutesOfValidVerrificationCode)
                return $this->errorResponse('رقم الهاتف لم يتم التحقق منه أو رمز التحقق انتهت صلاحيته',403);
        }
        return $next($request);
    }
}
