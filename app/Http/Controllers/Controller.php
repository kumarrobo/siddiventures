<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function getNewOTP($mobile){
    	return "123456";
    }


    //Get Create Date
    public function getNow(){
    	return date("Y-m-d H:i:s");
    }



    //Get Create Date
    public function getFormatDate($date){
    	return date("Y-m-d",strtotime($date));
    }


    //Get Auth User Id
    public function getAuthUserID(){
        return Auth::user()->id;
    }
}
