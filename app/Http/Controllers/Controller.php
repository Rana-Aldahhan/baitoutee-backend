<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,ApiResponser;

    public function getSupport()
    {
        $support_phone_number = DB::table('global_variables')->where('id', 5)->get('value')->first();
        return $this->successResponse($support_phone_number);
    }
}
