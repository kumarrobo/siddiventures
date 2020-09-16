<?php

namespace App\Http\Controllers\RO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RO\SMSController;
use App\Http\Controllers\WireAPIController;
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
use App\VerifyBeneficiariesBankAccount;
use App\VerifyMobileBeneficiariesBankAccount;

class MoneyTransferController extends Controller
{
    //
    public $SMSController;
    public $WireAPI;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->SMSController = new SMSController();
        $this->WireAPI       = new WireAPIController();
        $this->middleware('auth:ro');
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
                //Session::flash('message', "Mobile Number is Verified Now !!");
                $mobileDateStr = Crypt::encryptString($mobile.'|'.date('Ymd'));
                return redirect('RO/bankaccountlist/'.$mobileDateStr)->with(['message'=>["OTP Verify Sucessfully."]]);
            }
            if($mobile_otp){
                 if($this->isValidOTP($request)){
                     $mobileDateStr = Crypt::encryptString($mobile.'|'.date('Ymd'));
                     return redirect('RO/bankaccountlist/'.$mobileDateStr)->with(['message'=>["OTP Verify Sucessfully."]]);
                }else{
                    $verifyOTP = true;
                    Session::flash('error', ["Invalid OTP, Please try again."]);   
                }
            }else if($this->generateOTPNow($mobile)){
                $verifyOTP = true;
                Session::flash('message', "New OTP has been Sent, please verify!!");
            }else{
                 Session::flash('error', ["Somthing went wrong."]);
            }
        }
        return view('RO.moneyTransfer',array(
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
            $address     = $request->get('address'); 
            $verifyMobileNumberObj = VerifyMobileNumber::where('user_id','=',$this->getUserId())
                ->where('mobile','=',$mobile)->where('is_verified','=',0)->first();
            if($verifyMobileNumberObj){
                //Check is this OTP is valid or not
                $verifyMobileNumberObj = VerifyMobileNumber::where('user_id','=',$this->getUserId())
                ->where('mobile','=',$mobile)->where('is_verified','=',0)->where('otp_number','=',$mobile_otp)->first();
                if($verifyMobileNumberObj){
                    //Call the wire API for Add Contact Details and get the response, Once response will
                    //Success, then move ahead.
                    $result = $this->WireAPI->addWireContactAPI($request);
                    $resultArr = json_decode($result,true);
                    if($resultArr['success']){
                        $wireApiContactID   =   $resultArr['data']['contact']['id'];
                        $apiStatus          =   $resultArr['data']['contact']['status'];
                        $verifyMobileNumberObj->sender_name     =   $sender_name;
                        $verifyMobileNumberObj->email_address   =   $email;
                        $verifyMobileNumberObj->address         =   $address;
                        $verifyMobileNumberObj->is_verified     =   1;
                        $verifyMobileNumberObj->wireApiContactID=   $wireApiContactID;
                        $verifyMobileNumberObj->apiStatus       =   $apiStatus;
                        //dd($verifyMobileNumberObj);
                        if($verifyMobileNumberObj->save()){
                            //dd($result);
                            Session::flash('message', "Mobile Number is Verified Now !!");
                            return true;
                        }else{
                            Session::flash('error', ["Somthing Went wrong, Please try again."]);
                            return false;
                        }
                    }else{
                        Session::flash('error', ["Somthing went wrong, Please try again."]);
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
                    $mobileTransactionBalanceAmount = Helper::getMonthlyBalanceAmount($verifyMID);
                    $monthlyLimit   = Helper::getUserMonthlyBalance();
                    $utilized       = $monthlyLimit -  $mobileTransactionBalanceAmount;

                    //dd($mobileTransactionBalance);
                }else{
                     Session::flash('error', ["No Records Found"]);
                     return redirect('RO/moneytransfer/')->with(['error'=>['Sorry ! Invalid Url.']]);
                }
            }
        } catch (DecryptException $e) {
            Session::flash('error', ["No Records Found"]);
            return redirect('RO/moneytransfer/')->with(['error'=>['Sorry ! Invalid Url.']]);
        }
        return view('RO.bankAccountList',array(
            'mobileNumber'=> $mobile,
            'senderName'  => $senderName,
            'address'     => $address,
            'monthlyLimit'=> $monthlyLimit,
            'utilized'    => $utilized,
            'balance'     => $mobileTransactionBalanceAmount,
            'bankList'    => $bankList,
            'id'          => Crypt::encryptString($verifyMID)
        ));
    }





    public function addAccountNumber(Request $request,$id){
        try{
             $ids  = Crypt::decryptString($id);
             $VerifyMobileNumber =VerifyMobileNumber::find($ids);
             $bankList = MasterBank::where('status','=',1)->get();
             if ($request->isMethod('post')) {
                $account_no     = $request->get('account_no');
                $master_bank_id = $request->get('master_bank_id');
                $IFSCCode       = $request->get('IFSCCode');
                $hiddenid       = $request->get('hiddenid');
                $contact_id     = $VerifyMobileNumber['wireApiContactID'];
                $beneficiary_type = 'bank_account';
                $beneficiary_name = $VerifyMobileNumber['sender_name'];
               
                $data = array();
                $data['contact_id']         =   $contact_id;
                $data['beneficiary_type']   =   $beneficiary_type;
                $data['beneficiary_name']   =   $beneficiary_name;
                $data['account_number']     =   $account_no;
                $data['ifsc']               =   $IFSCCode;
                $result = $this->WireAPI->addWireBeneficiariesAPI($request, $data);
                dd($result);
                if($resultArr['success']){
                    $vbbaccount     = new VerifyBeneficiariesBankAccount();
                    //$vbbaccount[''] =    
                }else{
                    //Get the Beneficiaries Details
                    $data   = array();
                    $data['contact_id'] = $VerifyMobileNumber['wireApiContactID'];
                    $data['pageSize']   = 10;
                    $data['current']    = 1;
                    $result = $this->WireAPI->getWireBeneficiariesAPI($data);
                    dd($result);
                }
             }
             //dd($VerifyMobileNumber);
        } catch (DecryptException $e) {
            Session::flash('error', ["No Records Found"]);
            return redirect('RO/moneytransfer/')->with(['error'=>['Sorry ! Invalid Url.']]);
        }
        return view('RO.addBankAccount',array(
            'id'          => $id,
            'bankList'    => $bankList,
            'VerifyMobileNumber'=>$VerifyMobileNumber
        ));
    }




    public function addAccountRequest(Request $request){
         $result =array("success"=>false,"message"=>"Error.");
         if ($request->isMethod('post')) {
            $account_no         = $request->get('account_no');
            $master_bank_id     = $request->get('master_bank_id');
            $IFSCCode           = $request->get('IFSCCode');
            $hiddenid           = $request->get('id');
            $ids                = Crypt::decryptString($hiddenid);
            $VerifyMobileNumber = VerifyMobileNumber::find($ids);
            $contact_id         = $VerifyMobileNumber['wireApiContactID'];
            $beneficiary_type   = 'bank_account';
            $beneficiary_name   = $VerifyMobileNumber['sender_name'];

            //Check If Account Number is already Valid
            $res = VerifyBeneficiariesBankAccount::where('account_number','=',$account_no)
            ->where('account_ifsc','=',$IFSCCode)
            ->first();
            if(!empty($res)){
                //Check Id this Account Number is already present
                if($this->isAccountNumberPresent($request) == false){
                    $VMBBAObj = new VerifyMobileBeneficiariesBankAccount();
                    $lastId   = $res['id'];
                    $VMBBAObj['verify_beneficiaries_bank_account_id']   = $lastId;
                    $VMBBAObj['user_id']                                = $this->getUserId();
                    $VMBBAObj['verify_mobile_number_id']                = $VerifyMobileNumber['id'];
                    $VMBBAObj['status']                                 = 1;
                    $VMBBAObj['recipient_number']                       = NULL;
                    $VMBBAObj['created_at']                             = $this->getNow();
                    if($VMBBAObj->save()){
                        $result =array("success"=>true,"message"=>"Beneficiaries Account added successfully.");
                    }else{
                        $result =array("success"=>false,"message"=>"Beneficiaries Account not added, somthing went wrong.");
                        
                    }    
                }else{
                    $result =array("success"=>false,"message"=>"Beneficiaries Account already present.");
                }
            }else{

                $data = array();
                $data['contact_id']         =   $contact_id;
                $data['beneficiary_type']   =   $beneficiary_type;
                $data['beneficiary_name']   =   $beneficiary_name;
                $data['account_number']     =   $account_no;
                $data['ifsc']               =   $IFSCCode;
                $result = $this->WireAPI->addWireBeneficiariesAPI($request, $data);
                if($result['success']){
                    if($this->saveBeneficiary($result, $VerifyMobileNumber)){
                        $result =array("success"=>true,"message"=>"Beneficiaries Account Added Successfully.");
                    }else{
                        $result =array("success"=>false,"message"=>"Somthing went wrong.");
                    }
                }
            }
            return response()->json($result);
         }
    }

    //Check Account Number Added or Not
    private function isAccountNumberPresent(Request $request){
        return true;
    }



    //Save Beneficiary For Contact User
    private function saveBeneficiary($result, $VerifyMobileNumber){
        if(!empty($result)){
            $contact_id         =   $result['data']['beneficiary']['contact']['id'];
            $beneficiary_id     =   $result['data']['beneficiary']['id'];
            $beneficiary_type   =   $result['data']['beneficiary']['beneficiary_type'];
            $bank_name          =   $result['data']['beneficiary']['bank_name'];
            $account_name       =   $result['data']['beneficiary']['account_name'];
            $account_number     =   $result['data']['beneficiary']['account_number'];
            $account_ifsc       =   $result['data']['beneficiary']['account_ifsc'];
            $upi_handle         =   $result['data']['beneficiary']['upi_handle'];
            $is_active          =   $result['data']['beneficiary']['is_active'];
            $is_primary         =   $result['data']['beneficiary']['is_primary'];

            $vbbaccount     = new VerifyBeneficiariesBankAccount();
            $vbbaccount['contact_id']       =   $contact_id;
            $vbbaccount['beneficiary_id']   =   $beneficiary_id;
            $vbbaccount['beneficiary_type'] =   $beneficiary_type;
            $vbbaccount['bank_name']        =   $bank_name;
            $vbbaccount['account_name']     =   $account_name;
            $vbbaccount['account_number']   =   $account_number;
            $vbbaccount['account_ifsc']     =   $account_ifsc;
            $vbbaccount['upi_handle']       =   $upi_handle;
            $vbbaccount['is_active']        =   $is_active;
            $vbbaccount['is_primary']       =   $is_primary;
            $vbbaccount['created_at']       =   $this->getNow();
            $lastId = $vbbaccount->save();
            if($lastId>0){
                $VMBBAObj = new VerifyMobileBeneficiariesBankAccount();
                $VMBBAObj['verify_beneficiaries_bank_account_id']   = $lastId;
                $VMBBAObj['user_id']                                = $this->getUserId();
                $VMBBAObj['verify_mobile_number_id']                = $VerifyMobileNumber['id'];
                $VMBBAObj['status']                                 = 1;
                $VMBBAObj['recipient_number']                       = NULL;
                $VMBBAObj['created_at']                             = $this->getNow();
                if($VMBBAObj->save()){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }


    


}
