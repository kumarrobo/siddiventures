<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\SMSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;
use Log;
use Hash;
use DB;
use App\Helpers\Helper;
use App\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\VerifyMobileNumber;
use App\VerifyBankAccount;
use App\MasterBank;


class MoneyTransferController extends Controller
{
    //
    public $SMSController;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->SMSController = new SMSController();
        $this->middleware('auth:user');
    }



    private function generateOTPNow($mobile){
        //$otp = $this->getNewOTP($mobile);
        $otp = '123456';
        if($otp){
            $VerifyMobileNumber = new VerifyMobileNumber();
            $VerifyMobileNumber['user_id']          = $this->getUserId();
            $VerifyMobileNumber['mobile']           = $mobile;
            $VerifyMobileNumber['otp_number']       = $otp;
            $VerifyMobileNumber['is_verified']      = '0';
            $VerifyMobileNumber['transfer_limit']   = $this->getTransferLimitPerMonth();
            $VerifyMobileNumber['status']           = 1;
            $VerifyMobileNumber['created_at']       = $this->getNow();
            if($VerifyMobileNumber->save()){
                return true;
            }
        }
    }

    /**
    * @param  \Illuminate\Http\Request  $request
    * @return void
    * @throws \Illuminate\Validation\ValidationException
    */
    public function moneyTransfer(Request $request){
        $verifyOTP = false;
        $mobile    = '';
        if ($request->isMethod('post')) {
            $mobile      = $request->get('mobile'); 
            $sender_name = $request->get('sender_name'); 
            $email       = $request->get('email'); 
            $mobile_otp  = $request->get('mobile_otp'); 
            if($this->isMobileVerified($request)){
                     $mobileDateStr = Crypt::encryptString($mobile.'|'.date('Ymd'));
                     return redirect('user/bankaccountlist/'.$mobileDateStr)->with(['message'=>["OTP Verify Sucessfully."]]);
            }
            if($mobile_otp){
                 if($this->isValidOTP($request)){
                     $mobileDateStr = Crypt::encryptString($mobile.'|'.date('Ymd'));
                     return redirect('user/bankaccountlist/'.$mobileDateStr)->with(['message'=>["OTP Verify Sucessfully."]]);
                }else{
                    Session::flash('error', ["Invalid OTP, Please try again."]);   
                }
            }else if($this->generateOTPNow($mobile)){
                $verifyOTP = true;
                Session::flash('message', "OTP has been Sent, please verify!!");
            }else{
                 Session::flash('error', ["Somthing went wrong."]);
            }
        }
        return view('user.moneyTransfer',array(
                    'verifyOTP' =>  $verifyOTP,
                    'mobile'    =>  $mobile
            ));
    }


    //Chck Of this Mobile Number is alredy verified
    private function isMobileVerified(Request $request){
        $mobile = $request->get('mobile');
        $verifyMobileNumberObj = VerifyMobileNumber::where('user_id','=',$this->getUserId())
                ->where('mobile','=',$mobile)->where('is_verified','=',1)->first();
        if($verifyMobileNumberObj){
            return true;
        }else{
            return false;
        }
    }






    private function isValidOTP(Request $request){
            $mobile      = $request->get('mobile'); 
            $sender_name = $request->get('sender_name'); 
            $email       = $request->get('email'); 
            $mobile_otp  = $request->get('mobile_otp'); 
            $verifyMobileNumberObj = VerifyMobileNumber::where('user_id','=',$this->getUserId())
                ->where('mobile','=',$mobile)->where('is_verified','=',0)->first();
            if($verifyMobileNumberObj){
                //Check is this OTP is valid or not
                $verifyMobileNumberObj = VerifyMobileNumber::where('user_id','=',$this->getUserId())
                ->where('mobile','=',$mobile)->where('is_verified','=',0)->where('otp_number','=',$mobile_otp)->first();
                if($verifyMobileNumberObj){
                    $verifyMobileNumberObj->is_verified =1;
                    if($verifyMobileNumberObj->save()){
                        Session::flash('message', "Mobile Number is Verified Now !!");
                        return true;
                    }else{
                        Session::flash('error', ["Somthing Went wrong, Please try again."]);
                        return false;
                    }
                }else{
                    Session::flash('error', ["Invalid OTP, Please try again."]);
                    return false;
                }
            }else{
                return false;
            }

    }



    //Register All Bank Account List with Indivisual Mobile Number
    public function bankAccountList(Request $request,$mdstr){
        try{
            $mdstr  = Crypt::decryptString($mdstr);
            $strArr = explode("|",$mdstr);
            if(is_array($strArr)){
                $mobile = $strArr[0];
                $tday   = $strArr[1];
                if($tday == date('Ymd')){
                    $VerifyMobileNumber =VerifyMobileNumber::where('user_id','=',$this->getUserId())->where('mobile','=',$mobile)->first();
                    $senderName         =  $VerifyMobileNumber['sender_name'];
                    $address            =  $VerifyMobileNumber['address'];
                    $verifyMID          =  $VerifyMobileNumber['id'];
                    $bankAccountList    = VerifyBankAccount::with('MasterBank')->where('user_id','=',$this->getUserId())
                    ->where('verify_mobile_number_id','=',$verifyMID)
                    ->get();
                    $bankList   =  $bankAccountList;
                    //dd($bankList);
                }else{
                     Session::flash('error', ["No Records Found"]);
                     return redirect('user/moneytransfer/')->with(['error'=>['Sorry ! Invalid Url.']]);
                }
            }
        } catch (DecryptException $e) {
            Session::flash('error', ["No Records Found"]);
            return redirect('user/moneytransfer/')->with(['error'=>['Sorry ! Invalid Url.']]);
        }
        return view('user.bankAccountList',array(
            'mobileNumber'=> $mobile,
            'senderName'  => $senderName,
            'address'     => $address,
            'monthlyLimit'=> '25000',
            'utilized'    => '10000',
            'balance'     => '15000',
            'bankList'    => $bankList,
            'id'          => Crypt::encryptString($verifyMID)
        ));
    }





    public function addAccountNumber(Request $request,$id){
        try{
             $ids  = Crypt::decryptString($id);
             $VerifyMobileNumber =VerifyMobileNumber::find($ids);
             $bankList = MasterBank::where('status','=',1)->get();

        } catch (DecryptException $e) {
            Session::flash('error', ["No Records Found"]);
            return redirect('user/moneytransfer/')->with(['error'=>['Sorry ! Invalid Url.']]);
        }
        return view('user.addBankAccount',array(
            'id'          => $id,
            'bankList'    => $bankList
        ));
    }




    public function addAccountRequest(Request $request){
         if ($request->isMethod('post')) {
            $account_no     = $request->get('account_no');
            $master_bank_id = $request->get('master_bank_id');
            $IFSCCode       = $request->get('IFSCCode');
            $hiddenid       = $request->get('hiddenid');
            //echo "<pre>";
            $merchentKey = config('global.MERCHANT_KEY');
            $salt        = config('global.SALT');
            $keyStr      = $merchentKey.'|'.$account_no.'|'.$IFSCCode.'|'.$salt; 
            $hash = hash('sha512', $keyStr);
             $data = array(
                'key'        => $hash,
                'account_no' => $account_no,
                'ifsc'       => $IFSCCode 
            );
            $result = $this->postCurlData($data);
            $result =array(
                            "success"=>true, 
                            "status"=>true, 
                            "message"=>"Authentication Failed.", 
                            "wire_allowed"=>true
                        );
            return response()->json($result);
         }
    }



    private function postCurlData($data){
        $payload = json_encode($data);
        // Prepare new cURL resource
        $ch = curl_init('https://wire.easebuzz.in/api/v1/beneficiaries/bank_account/verify/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
         
        // Set HTTP Header for POST request 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
        );
         
        // Submit the POST request
        $result = curl_exec($ch);
         
        // Close cURL session handle
        curl_close($ch);
        return $result; 
    }


    //  /**
    //  * Validate the user login request.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return void
    //  *
    //  * @throws \Illuminate\Validation\ValidationException
    //  */
    // protected function validateForm(array $data)
    // {
    //     return Validator::make($data, [
    //         'mobile_otp'        => ['required'],
    //     ],[
    //         'request_amount.required'       => 'Min 100.00 ruppes request amount required..', // custom message
    //         'request_name.required'         => 'Enter Requested by name', // custom message
    //         'payment_mode.required'         => 'Choose payemnt mode', // custom message
    //         'email_address.required'        => 'Email address required', // custom message
    //         'mobile.required'               => 'Mobile number required', // custom message
    //         'remarks3.required'             => 'Please Enter remarks' // custom message
    //        ]
    //    );
       
    // }




}
