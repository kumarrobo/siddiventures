<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Auth;
use App\PaymentWallet;
use GuzzleHttp\Client;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * @param  string
     * @return string
     */
    public function getAmount($number) {
      
            return 'Rs '.number_format($number,2);   
              
    }


    /**
     * @param  string
     * @return string
     */
    public  function getWalletBalance() {

        if(Auth::guard('user')->check()){
            $user_id = Auth::user()->id;
            $PaymentWallet = PaymentWallet::where('user_id','=',$user_id)->first();
            if(!empty($PaymentWallet)){
                return $PaymentWallet['total_balance'];
            }else{
                return '0.00';    
            }
        }

        if(Auth::guard('ro')->check()){
            return number_format(15000,2);   
        }
    
              
    }


    public function getNewOTP($mobile){
        $randomid       = mt_rand(123456,999999); 
        $username       = "Siddhient";
        $key            = "2f67b0dccfXX";
        $mobileNumber   = $mobile;
        $message        = "New OTP for login for Siddi Venture is ".$randomid. ". Please do not share it with anyone.";
        $senderId       = "INFOTP";
        //return $randomid; 
        $url ="http://mobicomm.dove-sms.com//submitsms.jsp?user=".$username."&key=".$key."&mobile=+91".$mobileNumber."&message=".$message."&senderid=".$senderId."&accusage=1"; 
        if($this->sendSMS($url)){
            return $randomid; 
        }
    }


    private function sendSMS($url){

        $client = new Client();
        $res = $client->request('GET', $url);
        $res->getStatusCode();
        $res->getHeader('content-type');
        $res->getBody();
        if($res->getStatusCode() == 200){
            return true;
        }
    }


    //Get Create Date
    public function getNow(){
    	return date("Y-m-d H:i:s");
    }



    //Get Create Date
    public function getTodayDate(){
        return date("Y-m-d");
    }



    //Get Create Date
    public function getFormatDate($date){
    	return date("Y-m-d",strtotime($date));
    }



     //Get Create Date
    public function getDisplayDate($date){
        return date("d-m-Y",strtotime($date));
    }


    //Get Auth User Id
    public function getAuthUserID(){
        return Auth::user()->id;
    }


    //Get Random Transaction Number 
    public function getTransactionNumber(){
        return time().mt_rand(123456,999999);   
    }




    /**
     * Get the Wallet Payment Id of the user
     * Pramas as user Id of the Distributor OR RO
     * @param integer
     * @return integer
     */
    public  function getPaymentWalletDetails($user_id){
        $paymentWalletDetails = array();
        if($user_id>0){
            $paymentWalletDetails = PaymentWallet::where('status','=',1)->where('user_id','=',$user_id)->first();
            return $paymentWalletDetails;
        }else{
            return $paymentWalletDetails;
        }
    }






    /**
     * Get the Wallet Payment Id of the user
     * Pramas as user Id of the Distributor OR RO
     * @param integer
     * @return integer
     */
    public  function isValidPaymentWallet($user_id){
        $paymentWalletDetails = array();
        if($user_id>0){
            if (PaymentWallet::where('status','=',1)->where('user_id','=',$user_id)->count()) {
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }




   /**
     * Get the Wallet Payment Id of the user
     * Pramas as user Id of the Distributor OR RO
     * @param integer
     * @return integer
     */
    public  function getUserId(){
        return Auth::user()->id;
    }

   





}
