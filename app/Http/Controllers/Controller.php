<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Traits\PictureHelper;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests,ApiResponser,PictureHelper;

    public function getSupport()
    {
        $support_phone_number = DB::table('global_variables')->where('id', 5)->get('value')->first();
        return $this->successResponse($support_phone_number);
    }
    public function storePic(Request $request)
    {
       $name=$this->storePublicFile($request,'profile_picture','profiles');
       return response()->json([$name]);
    }
}
