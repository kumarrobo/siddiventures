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
use App\BankAccountTransaction;
use App\PaymentWalletTransaction;
use App\DsWalletBalanceRequest;
use App\PaymentWallet;
use App\VerifiedMobileMonthlyTransaction;
use App\BankTransferResult;

class MoneyTransferController extends Controller
{
    //
    public $SMSController;
    public $WireAPI;
    public $verify_mobile_beneficiaries_bank_account_id;
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
        $otp = $this->getNewOTP($mobile);
        //$otp = '123456';
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
                return redirect('RO/bankaccountlist/'.$mobileDateStr);
                //->with(['message'=>"OTP Verify Sucessfully."]);
            }
            if($mobile_otp){
                 if($this->isValidOTP($request)){
                     $mobileDateStr = Crypt::encryptString($mobile.'|'.date('Ymd'));
                     return redirect('RO/bankaccountlist/'.$mobileDateStr)->with(['message'=>"OTP Verify Sucessfully."]);
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



     /**
    * @param  \Illuminate\Http\Request  $request
    * @return void
    * @throws \Illuminate\Validation\ValidationException
    */
    public function deleteBankAccount(Request $request,$id){
        $details        = VerifyMobileBeneficiariesBankAccount::with('VerifyBeneficiariesBankAccount')->find($id);
        $bank_name      = $details['VerifyBeneficiariesBankAccount']['bank_name'];
        $account_number = $details['VerifyBeneficiariesBankAccount']['account_number'];
        $account_name = $details['VerifyBeneficiariesBankAccount']['account_name'];

        //dd($details);
        $verifyMobileNumberObj = VerifyMobileBeneficiariesBankAccount::where('user_id','=',$this->getUserId())
                ->where('id','=',$id)->delete();
                //dd($verifyMobileNumberObj);
        if($verifyMobileNumberObj){
            Session::flash('message', "Benificiery <b>".$account_name."</b> Bank <b>".$bank_name."</b> with Account No <b>".$account_number."</b>  deleted !!");
            return redirect()->back()->withInput();
        }else{
            Session::flash('message', "Bank Account not deleted !!");
            return redirect()->back()->withInput();
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
                    $VerifyMobileNumber =VerifyMobileNumber::with('VerifyMobileBeneficiariesBankAccount')->where('user_id','=',$this->getUserId())
                    ->where('mobile','=',$mobile)
                    ->first();
                    //dd($VerifyMobileNumber);
                    $VerifyMobileBeneficiariesBankAccountIdArr = [];
                    if(!empty($VerifyMobileNumber)){
                        if(!empty($VerifyMobileNumber['VerifyMobileBeneficiariesBankAccount'])){
                            foreach($VerifyMobileNumber['VerifyMobileBeneficiariesBankAccount'] as $item){
                                $VerifyMobileBeneficiariesBankAccountIdArr[] = $item['verify_beneficiaries_bank_account_id'];           
                            }
                        }
                    }

                    $senderName         =  $VerifyMobileNumber['sender_name'];
                    $address            =  $VerifyMobileNumber['address'];
                    $verifyMID          =  $VerifyMobileNumber['id'];
                    $bankAccountList    = VerifyBankAccount::with('MasterBank')->where('user_id','=',$this->getUserId())
                    ->where('verify_mobile_number_id','=',$verifyMID)
                    ->get();


                    $bankAccountList = VerifyMobileBeneficiariesBankAccount::with('VerifyBeneficiariesBankAccount')
                    ->where('user_id','=',$this->getUserId())
                    ->where('verify_mobile_number_id','=',$verifyMID)
                    ->paginate($this->getPageItem());
                    $bankList   =  $bankAccountList;
                    //dd($bankList);
                    
                    $mobileTransactionBalanceAmount = Helper::getMonthlyBalanceAmount($verifyMID);
                    $monthlyLimit   = Helper::getUserMonthlyBalance();
                    $utilized       = $monthlyLimit -  $mobileTransactionBalanceAmount;


                    //Get All Transaction List Of this Mobile
                    $payment_wallet_id = $this->getPaymentWalletID($this->getUserId());
                    $mobileTxn = PaymentWalletTransaction::with('VerifyBeneficiariesBankAccount')
                                ->where('payment_wallet_id', '=', $payment_wallet_id)
                                ->where('user_id','=',$this->getUserId())
                                ->whereIn('verify_mobile_beneficiaries_bank_account_id', $VerifyMobileBeneficiariesBankAccountIdArr)
                                ->orderBy('id','DESC')
                                ->paginate($this->getPageItem());
                    //dd($mobileTxn);
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
        $bankMasterList = MasterBank::where('status','=',1)->get();

        //List Of Charges Of Money Transfer
        $MoneyTransferCharge  = $this->getMoneyTransferCharge();
        return view('RO.bankAccountList',array(
            'mobileNumber'=> $mobile,
            'senderName'  => $senderName,
            'address'     => $address,
            'monthlyLimit'=> $monthlyLimit,
            'utilized'    => $utilized,
            'balance'     => $mobileTransactionBalanceAmount,
            'bankList'    => $bankList,
            'id'          => Crypt::encryptString($verifyMID),
            'MoneyTransferCharge' =>$MoneyTransferCharge,
            'AllMobileTxn'=>$mobileTxn,
            'bankMasterList'=>$bankMasterList
        ));
    }





    public function addAccountNumber(Request $request,$id){
        try{
             $walletBalance         = Helper::getWalletBalance();
             $verificationCharge    = Helper::getBankAccountVerificationCharge();
             $isvalid               = false;
             if($walletBalance > $verificationCharge){
                $isvalid            = true;
             }
             //dd($isvalid);
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
                //dd($result);
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
                    //dd($result);
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
            'VerifyMobileNumber'=>$VerifyMobileNumber,
            'isvalidVerification'=>$isvalid
        ));
    }


  



    /**
    * @param  \Illuminate\Http\Request  $request
    * Request parameters with Bank Account Number, IFSC Code, 
    * @return void
    * @throws \Illuminate\Validation\ValidationException
    */
    public function isValidAccountNumber(Request $request){
           $result["success"] = false;
           $result["message"] = "Error";
           if ($request->isMethod('post')) {
               $account_no         = $request->get('account_no');
               $master_bank_id     = $request->get('master_bank_id');
               $IFSCCode           = $request->get('IFSCCode');

               $data               = array();
               $data['account_no'] = $account_no;
               $data['ifsc']       = $IFSCCode;
               $registredRes       = $this->getRegistredBeneficeriesAccount($request); 
               if(!empty($registredRes)){
                    $resultArr        = $registredRes;
                    //$this->addAccountRequest($request);
                    $result["success"] = true;
                    $result["message"] = "Account Number is valid";
                    $result["is_valid"] = true;
                    $result["is_api"]   = false;
                    $result['data']= $resultArr;
               }else{
                    $resultArr = $this->WireAPI->verifyBankAccountAPI($request, $data);
                    $result["success"] = true;
                    $result["message"] = "Account Number is verified";
                    $result["is_valid"] = true;
                    $result["is_api"]   = true;
                    $result['data']= $resultArr;
                    //$this->addAccountRequest($request);
               }
           }

           return response()->json($result);
    }


    //get Account Number if already added into system
    private function getRegistredBeneficeriesAccount(Request $request){
            $account_no         = $request->get('account_no');
            $IFSCCode           = $request->get('IFSCCode');
            $res = VerifyBeneficiariesBankAccount::where('account_number','=',$account_no)
            ->where('account_ifsc','=',$IFSCCode)
            ->first();
            return $res;
   }



    public function addAccountRequest(Request $request){
         $result =array("success"=>false,"message"=>"Error.");
         if ($request->isMethod('post')) {
            $account_no         = $request->get('account_no');
            $master_bank_id     = $request->get('master_bank_id');
            $IFSCCode           = $request->get('IFSCCode');
            $hiddenid           = $request->get('id');
            $beneficiary_mob_no = $request->get('beneficiary_mob_no');
           
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
                //Hidden Is must be passed in parameters
                if($this->isAccountNumberPresent($request) == false){
                    $VMBBAObj = new VerifyMobileBeneficiariesBankAccount();
                    $lastId   = $res['id'];
                    $VMBBAObj['verify_beneficiaries_bank_account_id']   = $lastId;
                    $VMBBAObj['user_id']                                = $this->getUserId();
                    $VMBBAObj['verify_mobile_number_id']                = $VerifyMobileNumber['id'];
                    $VMBBAObj['status']                                 = 1;
                    $VMBBAObj['recipient_number']                       = $beneficiary_mob_no;
                    $VMBBAObj['created_at']                             = $this->getNow();
                    if($VMBBAObj->save()){
                        //Start Payment Trasnfer 1 RS for testing and 
                        $paymentTransfer = $this->WireAPI->quickMoneyTransferInitiateAPI($request, $lastId);
                        $paymentTransferArr = json_decode($paymentTransfer,true);
                        
                        if($paymentTransferArr['success']){
                            $verify_mobile_number_id = $VerifyMobileNumber['id'];
                            $verify_mobile_beneficiaries_bank_account_id = $VMBBAObj->id;
                            $this->paymentInitiate($paymentTransferArr, $verify_mobile_number_id,$verify_mobile_beneficiaries_bank_account_id);
                        }
                        $redirectUrl    = url('RO/bankaccountlist/'.$hiddenid.'|'.date('Ymd'));
                        $result =array("success"=>true,"message"=>"Beneficiaries Account added successfully.","redirect"=>true,'redirectUrl'=>$redirectUrl);
                    }else{
                        $result =array("success"=>false,"message"=>"Beneficiaries Account not added, somthing went wrong.","redirect"=>false);
                        
                    }    
                }else{
                    //$this->WireAPI->quickMoneyTransferInitiateAPI($request, $res['id']);
                    $redirectUrl    = url('RO/bankaccountlist/'.$hiddenid.'|'.date('Ymd'));
                    $result =array("success"=>false,"message"=>"Beneficiaries Account already present.","redirect"=>true,'redirectUrl'=>$redirectUrl);
                }
            }else{

                $data = array();
                $data['contact_id']         =   $contact_id;
                $data['beneficiary_type']   =   $beneficiary_type;
                $data['beneficiary_name']   =   $beneficiary_name;
                $data['account_number']     =   $account_no;
                $data['ifsc']               =   $IFSCCode;
                $result = $this->WireAPI->addWireBeneficiariesAPI($request, $data);
                //print_r($result);
                if($result['success']){
                    if($this->saveBeneficiary($result, $VerifyMobileNumber)){
                    $redirectUrl    = url('bankaccountlist/'.$hiddenid.'|'.date('Ymd'));
                    $result =array("success"=>true,"message"=>"Beneficiaries Account Added Successfully.","redirect"=>true,'redirectUrl'=>$redirectUrl);
                    }else{
                        $result =array("success"=>false,"message"=>"Somthing went wrong.","redirect"=>false);
                    }
                }
            }
            return response()->json($result);
         }
    }

    //Check Account Number Added or Not
    private function isAccountNumberPresent(Request $request){
        $account_no         = $request->get('account_no');
        $master_bank_id     = $request->get('master_bank_id');
        $IFSCCode           = $request->get('IFSCCode');
        $hiddenid           = $request->get('id');
        $hiddenid           = $request->get('id');
        $ids                = Crypt::decryptString($hiddenid);
        $VerifyMobileNumber = VerifyMobileNumber::find($ids);
        $verify_mobile_number_id = $VerifyMobileNumber['id'];

        $res  = VerifyBeneficiariesBankAccount::where('account_number','=',$account_no)
            ->where('account_ifsc','=',$IFSCCode)
            ->first();
        if(!empty($res)){
            $verify_beneficiaries_bank_account_id   =  $res['id'];
            $verify_mobile_number_id                =  $verify_mobile_number_id;
            $user_id                                =  $this->getUserId();
            $verMobileBBankAccountRes = VerifyMobileBeneficiariesBankAccount::where('user_id','=',$user_id)
            ->where('verify_mobile_number_id','=',$verify_mobile_number_id)
            ->where('verify_beneficiaries_bank_account_id','=',$verify_beneficiaries_bank_account_id)
            ->first();
            if(!empty($verMobileBBankAccountRes)){
                return true;
            }else{
                return false;
            }
        }  
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
                $VMBBAObj['verify_beneficiaries_bank_account_id']   = $vbbaccount->id;
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



    //First Payment Initiated After Adding New Account
    public function paymentInitiate($paymentTransferArray, $verify_mobile_number_id, $verify_mobile_beneficiaries_bank_account_id){

        if ($paymentTransferArray['success']) {
            $user_id    = $this->getUserId();
            $paymentTransferArr = $paymentTransferArray['data']['transfer_request'];

            $amount     = $paymentTransferArr['amount'];
            $narration  = $paymentTransferArr['narration'];
            $bankTxnObj = new BankAccountTransaction();
            $bankTxnObj['user_id']                  =   $this->getUserId();
            $bankTxnObj['status']                   =   $paymentTransferArr['status'];
            $bankTxnObj['verify_mobile_number_id']  =   $verify_mobile_number_id;
            $bankTxnObj['verify_mobile_beneficiaries_bank_account_id']  =   $verify_mobile_beneficiaries_bank_account_id;
            $bankTxnObj['unique_request_number']    =   $paymentTransferArr['unique_request_number'];
            $bankTxnObj['transfer_request_id']      =   $paymentTransferArr['id'];
            $bankTxnObj['failure_reason']           =   $paymentTransferArr['failure_reason'];
            $bankTxnObj['beneficiary_id']           =   $paymentTransferArr['beneficiary_id'];
            $bankTxnObj['transaction_date']         =   $paymentTransferArr['created_at'];
            $bankTxnObj['unique_transaction_reference']  =   $paymentTransferArr['unique_transaction_reference'];
            $bankTxnObj['payment_mode']             =   $paymentTransferArr['payment_mode'];
            $bankTxnObj['amount']                   =   $paymentTransferArr['amount'];
            $bankTxnObj['currency']                 =   $paymentTransferArr['currency'];
            $bankTxnObj['narration']                =   $paymentTransferArr['narration'];
            $bankTxnObj['beneficiary_bank_name']    =   $paymentTransferArr['beneficiary_bank_name'];
            $bankTxnObj['beneficiary_account_name'] =   $paymentTransferArr['beneficiary_account_name'];
            $bankTxnObj['beneficiary_account_number']=  $paymentTransferArr['beneficiary_account_number'];
            $bankTxnObj['beneficiary_account_ifsc']  =  $paymentTransferArr['beneficiary_account_ifsc'];
            $bankTxnObj['beneficiary_upi_handle']    =  $paymentTransferArr['beneficiary_upi_handle'];
            $bankTxnObj['service_charge']            =  $paymentTransferArr['service_charge'];
            $bankTxnObj['gst_amount']                =  $paymentTransferArr['gst_amount'];
            $bankTxnObj['service_charge_with_gst']   =  $paymentTransferArr['service_charge_with_gst'];
            $bankTxnObj['queue_on_low_balance']      =  $paymentTransferArr['queue_on_low_balance'];
            $bankTxnObj['udf5']                      =  $paymentTransferArr['udf5'];
            $bankTxnObj['udf4']                      =  $paymentTransferArr['udf4'];
            $bankTxnObj['udf3']                      =  $paymentTransferArr['udf3'];
            $bankTxnObj['udf2']                      =  $paymentTransferArr['udf2'];
            $bankTxnObj['udf1']                      =  $paymentTransferArr['udf1'];
            $bankTxnObj['created_at']                =  $this->getNow();

            if($bankTxnObj->save()){

                //Update Payment Wallet Transactions For User
                $this->pushDebitRequestedBalanceAmount($user_id, $amount, $narration, $verify_mobile_beneficiaries_bank_account_id);
                $this->pushDebitIntoVerifiedMobileMonthlyTransactions($verify_mobile_number_id,$amount);
                
            }
        }
    }





    /**
    * @param integher
    * @return void
    * @param user_id, requested_amount
    * Distributor and RO as user_id.
    **/
    private function pushDebitIntoVerifiedMobileMonthlyTransactions($verify_mobile_number_id,$amount){
        $monthlyTxnObj  = new VerifiedMobileMonthlyTransaction();
        $monthlyTxnObj['user_id']                   = $this->getUserId();
        $monthlyTxnObj['verify_mobile_number_id']   = $verify_mobile_number_id;
        $monthlyTxnObj['month']                     = strtoupper(date('M'));
        $monthlyTxnObj['year']                      = date('Y');
        $monthlyTxnObj['used']                      = $amount;
        $monthlyTxnObj['transaction_status']        = 1;
        $monthlyTxnObj['created_at']                = $this->getNow();
        if($monthlyTxnObj->save()){
            return true;
        }
    }



     /**
    * @param integher
    * @return void
    * @param user_id, requested_amount
    * Distributor and RO as user_id.
    **/
    private function pushDebitRequestedBalanceAmount($user_id,$amount,$requestData, $verify_mobile_beneficiaries_bank_account_id){
            $userId         = $user_id;
            $creditAmount   = "0.00";
            $debitAmount    = $amount;
            $remarks        = $requestData;
            $newWalletTransaction  = array();          
            // Start transaction!
            DB::beginTransaction();
            try {
                //Call From Main Controller
                if($this->isValidPaymentWallet($userId)){

                        //Debit From the Wallet of the User
                        $paymentDebitDetails = $this->getPaymentWalletDetails($userId);
                        // Validate, then create if valid
                        $payment_wallet_id  = $paymentDebitDetails['id'];
                        $debit_amount       = $debitAmount;
                        $credit_amount      = $creditAmount;
                        $transaction_number = $this->getTransactionNumber();
                        $transaction_date   = $this->getNow();
                        $debitByUserId      = Auth::user()->id;
                        $status             = 'Success';
                        $remarks            = $remarks;
                        $created_at         = $this->getTodayDate();
                        $transferAmt        = $this->getTransferCharge($amount);
                        $debitWalletTransaction = PaymentWalletTransaction::create( 
                            [
                                
                                'payment_wallet_id'         => $payment_wallet_id,
                                'debit_amount'              => $debit_amount,
                                'credit_amount'             => $credit_amount,
                                'transaction_number'        => $transaction_number,
                                'transaction_date'          => $transaction_date,
                                'transfer_charge'           => $this->getTransferCharge($amount),
                                'user_id'                   => $debitByUserId,
                                'status'                    => $status,
                                'remarks'                   => $remarks,
                                'verify_mobile_beneficiaries_bank_account_id' => $verify_mobile_beneficiaries_bank_account_id,
                                'created_at'                => $created_at

                            ] );

                        //
                        if($debitWalletTransaction!=null){
                            $lastPaymentWalletTransactionId = $debitWalletTransaction['id']; 
                            //Deduct the balance from the Wallet  from the user
                            $debiteAmountWithTransferAmt = $debit_amount + $transferAmt;
                            $this->debitFromPaymentWallet($debiteAmountWithTransferAmt,$lastPaymentWalletTransactionId);

                        }
                   
                }
            
            } catch(\Exception $e){
                DB::rollback();
                throw $e;
            }
            // If we reach here, then
            // data is valid and working.
            // Commit the queries!
            DB::commit();
            return $newWalletTransaction;
    }





    /**
    * @param user_id, as id of Distributor OR Retailers
    * @param credit_amount as Amount to be added into wallet
    * @return boolean , True on sucess, False of Failed
    */
    private function creditIntoPaymentWallet($user_id,$amount,$lastPaymentWalletTransactionId){
        $userId     = $user_id; 
        $newAmount  = $amount;
        if($this->isValidPaymentWallet($user_id)){
            $paymentWalletDetails   = $this->getPaymentWalletDetails($user_id);
            $id                     =  $paymentWalletDetails['id'];
            $paymentWalletArr       = PaymentWallet::with('User')->find($id);
            
            $mobile                 = $paymentWalletArr['User']['mobile'];
            //Update New amount
            $paymentWalletArr['total_balance'] =  $paymentWalletArr['total_balance'] + $amount;
            if($paymentWalletArr->save()){
                $this->SMSController->sendCreditSMS($mobile,$amount,$userId,$lastPaymentWalletTransactionId);
                return true;
            }else{
                return false;
            }
        }
        
    }


     /**
    * @param user_id, as id of Distributor OR Retailers
    * @param credit_amount as Amount to be added into wallet
    * @return boolean , True on sucess, False of Failed
    */
    private function debitFromPaymentWallet($amount,$lastPaymentWalletTransactionId){
        $userId     = Auth::user()->id;
        $mobile     = Auth::user()->mobile;
        $newAmount  = $amount;
        if($this->isValidPaymentWallet($userId)){
            $paymentWalletDetails   =  $this->getPaymentWalletDetails($userId);
            $id                     =  $paymentWalletDetails['id'];
            $paymentWalletArr       =  PaymentWallet::find($id);
            //Update New amount
            $paymentWalletArr['total_balance'] =  $paymentWalletArr['total_balance'] - $amount;
            $newBalance = $paymentWalletArr['total_balance'];
            if($paymentWalletArr->save()){
                //Send SMS For Deduct Balance
                if($this->updatePaymentWalletTransactionBalance($newBalance, $lastPaymentWalletTransactionId)){
                    $this->SMSController->sendDeductSMS($mobile,$amount,$userId,$lastPaymentWalletTransactionId);
                    return true;
                }
            }else{
                return false;
            }
        }
        
    }






    public function transferMoney(Request $request,$id){
        try {
            //dd('dasd');
            //$chargesArr             = $this->getTransferChargeWithamount();
            //dd($chargesArr);
            //$verify_mobile_beneficiaries_bank_account_id 
            $verifyBeneficiariesId = Crypt::decryptString($id); 
            $result     =   VerifyMobileBeneficiariesBankAccount::with('VerifybeneficiariesBankAccount')->find($verifyBeneficiariesId);
            //dd($result);
            $verify_mobile_number_id = $result['verify_mobile_number_id'];
            $verifyMobile            = VerifyMobileNumber::find($verify_mobile_number_id);

            $mobileTransactionBalanceAmount = Helper::getMonthlyBalanceAmount($verify_mobile_number_id);
            $monthlyLimit   = Helper::getUserMonthlyBalance();
            $utilized       = $monthlyLimit -  $mobileTransactionBalanceAmount;

        } catch (DecryptException $e) {
            Session::flash('error', ["Somthing Went Wring, Please try again later"]);
            
        }
        return view('RO.TransferMoneyToBankAccount',array(
            'id'                            => $id,
            'result'                        => $result,
            'verifyMobile'                  => $verifyMobile,
            'mobileTransactionBalanceAmount'=> $mobileTransactionBalanceAmount,
            'monthlyLimit'                  => $monthlyLimit,
            'utilized'                      => $utilized,
            'verify_mobile_number_id'       => $verify_mobile_number_id
            
        ));

    }


    public function confirmTransferMoney(Request $request,$id){
       // dd($request->all());
        if($request->method() == 'GET'){
            return redirect('RO/transfermoney/'.$id)->with('message', "Somthing Went Wrong.!!");
        }
        if($request->method() == 'POST'){

            $amount         = $request->get('amount');
            $remarks        = $request->get('remarks');
            $payment_mode   = $request->get('payment_mode');

            //Check Wallet Balance 
            $walletBalance = Helper::getWalletBalance();
            if($walletBalance < $amount){
                $message = LOW_WALLET_BALANCE_MESASGE;
                return redirect('RO/transfermoney/'.$id)->with('message', $message);
            }
            //Check is PG is Active or not
            // Money Transfer Charge is enabled
            $charges = $this->getTransferCharge($amount);
            if($charges == 0){
                $message = MONEY_TRANSFER_NOT_ENABLED;
                return redirect('RO/transfermoney/'.$id)->with('message', $message);
            }

            try {
                //Get Charges Details
                $chargesArr             = $this->getTransferChargeWithamount($amount);

                //$verify_mobile_beneficiaries_bank_account_id 
                $verifyBeneficiariesId  = Crypt::decryptString($id);
                $result                 = VerifyMobileBeneficiariesBankAccount::with('VerifybeneficiariesBankAccount')->find($verifyBeneficiariesId);
                //dd($result);
                $verify_mobile_number_id        = $result['verify_mobile_number_id'];
                $verifyMobile                   = VerifyMobileNumber::find($verify_mobile_number_id);

                $mobileTransactionBalanceAmount = Helper::getMonthlyBalanceAmount($verify_mobile_number_id);
                $monthlyLimit                   = Helper::getUserMonthlyBalance();
                $utilized                       = $monthlyLimit -  $mobileTransactionBalanceAmount;
                //dd($verifyMobile);

                //Encrypt the post string
                $postStr = array(
                                    'mobile'        =>  $verifyMobile,
                                    'balance'       =>  $mobileTransactionBalanceAmount,
                                    'r_mobile'      =>  $verifyMobile['mobile'],
                                    'account_name'  =>  $result['VerifybeneficiariesBankAccount']['account_name'],
                                    'account_number'=>  $result['VerifybeneficiariesBankAccount']['account_number'],
                                    'bank_name'     =>  $result['VerifybeneficiariesBankAccount']['bank_name'],
                                    'account_ifsc'  =>  $result['VerifybeneficiariesBankAccount']['account_ifsc'],
                                    'amount'        =>  $amount,
                                    'fee'           =>  $chargesArr['charge'],
                                    'type'          =>  $chargesArr['type'],
                                    'remarks'       =>  $remarks,
                                    'payment_mode'  =>  $payment_mode,
                                    'beneficiaries_bank_account_id'=>$result['id'],
                                    'verify_mobile_number_id'=>$result['verify_mobile_number_id'],
                                    'verify_mobile_beneficiaries_bank_account_id'=>$result['id'],
                                    'utilized'      => $utilized,
                                    'monthlyLimit'  => $monthlyLimit,
                );
                $enPostStr =  Crypt::encryptString(json_encode($postStr));
            } catch (DecryptException $e) {
                Session::flash('error', ["Somthing Went Wring, Please try again later"]);
            }
        }
        //dd($chargesArr);

        return view('RO.ConfiromTransferMoneyToBankAccount',array(
            'id'                            => $id,
            'result'                        => $result,
            'verifyMobile'                  => $verifyMobile,
            'mobileTransactionBalanceAmount'=> $mobileTransactionBalanceAmount,
            'monthlyLimit'                  => $monthlyLimit,
            'utilized'                      => $utilized,
            'verify_mobile_number_id'       => $verify_mobile_number_id,
            'payment_mode'                  => $payment_mode,
            'remarks'                       => $remarks,
            'amount'                        => $amount,
            'chargeAmount'                  => $chargesArr['charge'],
            'chargeType'                    => $chargesArr['type'],
            'enPostStr'                     => $enPostStr
        ));

    }


    /**
     * Split Large amount into small amount
     * Default Amount Per Transaction For Transfer
     * @return Array
     * First Index Of Array is count for Transaction
     * Second Index Of Array is used for calculating Amount
     * e.g Amount is Rs. 12, and Default amount 5, then 12/5=2.4
     * Fraction Array will wil array('2','4');
     */
    public function getAmountFractionArray($amount){
            $defaultAmountPerTransaction    = $this->getAmountTransferPerTransaction();
            $fraction    = (float) ($amount / $defaultAmountPerTransaction);
            //dd($fraction);
            $fractionArr = explode('.', $fraction);
            //dd($fractionArr);
            $amountArr   = array();
            if(is_array($fractionArr)){
                if(isset($fractionArr[0])){
                    if($fractionArr[0] > 0){
                        for($i=1;$i<=$fractionArr[0];$i++){
                            $amountArr[] = $defaultAmountPerTransaction;
                        }
                     }
                }
                //Calculate Amount Less than  Default amount
                if(isset($fractionArr[1])){
                    $amountArr[] = ($fractionArr[1]/10) * $defaultAmountPerTransaction;
                }

            }

            return $amountArr;
    } 


    /**
    * @param  \Illuminate\Http\Request  $request
    * Request parameters with Bank Account Number, IFSC Code, 
    * @return void
    * @throws \Illuminate\Validation\ValidationException
    */
    public function transferMoneyAPIAction(Request $request){
            $validator               = array();
            $walletBalance           = Helper::getWalletBalance(); 
            $mobile                  = $request->get('mobile');
            $balance                 = $request->get('balance');
            $r_mobile                = $request->get('r_mobile');
            $account_name            = $request->get('account_name');
            $account_number          = $request->get('account_number');
            $bank_name               = $request->get('bank_name');
            $account_ifsc            = $request->get('account_ifsc');
            $amount                  = $request->get('amount');
            $fee                     = $request->get('fee');
            $remarks                 = $request->get('remarks');
            $payment_mode            = $request->get('payment_mode');
            $verifyBeneficiariesId   = $request->get('beneficiaries_bank_account_id');
            $verify_mobile_number_id = $request->get('verify_mobile_number_id');
            $verify_mobile_beneficiaries_bank_account_id = $request->get('verify_mobile_beneficiaries_bank_account_id');
            //dd($request->all());
             //Check Wallet Balance 
            $walletBalance = Helper::getWalletBalance();
            $moneytransferCharge = $this->getTransferChargeWithamount($amount);
            if($walletBalance < ($amount + $moneytransferCharge['charge'])){
                $message = LOW_WALLET_BALANCE_MESASGE;
                Session::flash('message', $message);
                return redirect()->back()->withInput();
            }
            //Check is PG is Active or not
            // Money Transfer Charge is enabled
            $charges = $this->getTransferCharge($amount);
            if($charges == 0){
                $message = MONEY_TRANSFER_NOT_ENABLED;
                Session::flash('message', $message);
                return redirect()->back()->withInput();
            }

            $transfermoneyArr = $this->getAmountFractionArray($amount);
            //dd($transfermoneyArr);
            //Verify Balance
            if($walletBalance >= $amount){ 
            $result     =   VerifyMobileBeneficiariesBankAccount::with('VerifybeneficiariesBankAccount')->find($verifyBeneficiariesId);
            if(!empty($result)){
                $verify_beneficiaries_bank_account_id =$result['VerifybeneficiariesBankAccount']['id']; 
                //dd($transfermoneyArr);
                //Call Api for Transfer Amount From Wallet to Bank Account
                foreach($transfermoneyArr as $amountItem){
                    $walletBalance  = Helper::getWalletBalance(); 
                    //dd($walletBalance);
                    //Add Money Transfer Charge Amount with Tramsfer Amount
                    $moneytransferCharge = $this->getTransferChargeWithamount($amountItem);
                    if($walletBalance >= ($amountItem + $moneytransferCharge['charge'])){
                    $charges = $moneytransferCharge['charge'];
                    $request->merge(['amount'=>$amountItem]); 
                    //dd($transfermoneyArr);
                
                    $paymentTransfer        = $this->WireAPI->moneyTransferInitiateAPI($request, $verify_beneficiaries_bank_account_id);
                    $paymentTransferArr     = json_decode($paymentTransfer,true);
                    //dd($paymentTransferArr);
                    if($paymentTransferArr['success']){
                        $verify_mobile_number_id    = $verify_mobile_number_id;
                        $verify_mobile_beneficiaries_bank_account_id = $verify_beneficiaries_bank_account_id;
                        $res    =$this->paymentTransfered($paymentTransferArr, $verify_mobile_number_id,$verify_mobile_beneficiaries_bank_account_id);
                        if($res){
                                //Update Payment Wallet Transactions For User
                                $user_id    = $this->getUserId();
                                $verifyMobileResult = VerifyMobileNumber::find($verify_mobile_number_id);
                                $this->pushDebitRequestedBalanceAmount($user_id, $amountItem, $remarks, $verify_mobile_beneficiaries_bank_account_id);
                               // $this->pushDebitRequestedBalanceAmount($user_id, $charges, $remarks, $verify_mobile_beneficiaries_bank_account_id);

                                $this->pushDebitIntoVerifiedMobileMonthlyTransactions($verify_mobile_number_id,$amount);
                                //$this->pushDebitIntoVerifiedMobileMonthlyTransactions($verify_mobile_number_id,$charges);
                                $midStr    = $verifyMobileResult['mobile'].'|'.date('Ymd'); 
                                $enMidStr  = Crypt::encryptString($midStr);
                                    $transferResponseArr[] = array(
                                                                'success'   =>  'True',
                                                                'Code'      =>   200,
                                                                'status'    =>  'Success',
                                                                'amount'    =>  $amountItem,
                                                                'ref_no'    =>  NULL,
                                                                'message'   =>  'Transfer Successfull.',
                                                                'remarks'   =>  $remarks,
                                                                'response'  =>  $paymentTransferArr
                                );
                                //return redirect('RO/bankaccountlist/'.$enMidStr)->with('message', "Money Transffred Successfully!!");
                            }else{
                                    $transferResponseArr[] = array(
                                                                'success'       =>  'False',
                                                                'Code'          =>   201,
                                                                'status'        =>  'Failed',
                                                                'amount'        =>  $amountItem,
                                                                'reference_no'  =>  NULL,
                                                                'remarks'       =>  $remarks,
                                                                'message'       =>  LOW_WALLET_BALANCE_MESASGE
                                                            );
                            }

                        }else{
                                    $transferResponseArr[]  = array(
                                                                'success'       =>  'False',
                                                                'Code'          =>   202,
                                                                'status'        =>  'Failed',
                                                                'amount'        =>  $amountItem,
                                                                'reference_no'  =>  NULL,
                                                                'remarks'       =>  $remarks,
                                                                'message'       =>  LOW_WALLET_BALANCE_MESASGE
                                                            );

                            }

                    }else{
                                    $transferResponseArr[]  = array(
                                                                'success'       =>  'False',
                                                                'Code'          =>   203,
                                                                'status'        =>  'Failed',
                                                                'amount'        =>  $amountItem,
                                                                'reference_no'  =>  NULL,
                                                                'remarks'       =>  $remarks,
                                                                'message'       =>  LOW_WALLET_BALANCE_MESASGE
                                                            );

                        }
                    }
                    //dd($transferResponseArr);
                    
                    $id   = $this->saveBankTransferResult($transferResponseArr);
                    $enid = Crypt::encryptString($id);
                    return redirect('RO/transfermoneyresult/'.$enid);

                }
            }

        
    }


    //Save Bank Transfer Result
    public function saveBankTransferResult($transferResponseArr){
        $resultJson = json_encode($transferResponseArr);
        $resultJsonStr  = Crypt::encryptString($resultJson);
        $data['user_id']        = $this->getUserId();
        $data['result_json']    = $resultJson;
        $data['result_encrypt'] = $resultJsonStr;
        $data['created_at']     = $this->getNow();
        return BankTransferResult::create($data)->id;
    } 



    /**
    * @param  \Illuminate\Http\Request  $request
    * Request parameters with Bank Account Number, IFSC Code, 
    * @return void
    * @throws \Illuminate\Validation\ValidationException
    */
    public function transferedMoneyResult(Request $request,$result){
        //echo $result; die;
        //$enMidStr  = Session::get('postStr');
        $enMidStrJson  = Crypt::decryptString($result);

        $res        = BankTransferResult::find($enMidStrJson);
        $resultText = $res['result_json'];
        $enMidStr   = json_decode($resultText,true);
        //echo "<pre>";
        //print_r($enMidStr); die;
        //Get Sender and Reciver Details
        $recipentDetails = [];
        $totalAmount     = [];
        foreach($enMidStr as $item){
            if($item['status'] == 'Success'){
                $recipentDetails = array(
                    'unique_request_number'=>$item['response']['data']['transfer_request']['unique_request_number'],
                    'BankName'=>$item['response']['data']['transfer_request']['beneficiary_bank_name'],
                    'IFSCCode'=>$item['response']['data']['transfer_request']['beneficiary_account_ifsc'],
                    'Name'=>$item['response']['data']['transfer_request']['beneficiary_account_name'],
                    'AccountNumber'=>$item['response']['data']['transfer_request']['beneficiary_account_number'],
                );
                $totalAmount[] = $item['amount'];
            }
        }

        //Get Details Of Sender
        if(!empty($recipentDetails)){
        $senderRes = BankAccountTransaction::with('VerifyMobileNumber')
        ->where('unique_request_number','=',$recipentDetails['unique_request_number'])
        ->first();
            $senderData = $senderRes['VerifyMobileNumber'];
            $sender_name = $senderData['sender_name'];
            $sender_mobile = $senderData['mobile'];
        }else{
            $senderData      = '';
            $sender_name     = '';
            $sender_mobile   = '';
        }
        return view('RO.TransferedMoneySuccess',array(
            'responseArr'    => $enMidStr,
            'recipentDetails'=> $recipentDetails,
            'totalAmount'    => number_format(array_sum($totalAmount),2),
            'sender_name'    => $sender_name,
            'sender_mobile'  => $sender_mobile
        ));
        //dd($enMidStr);
    }



    /**
    * @param  \Illuminate\Http\Request  $request
    * Request parameters with Bank Account Number, IFSC Code, 
    * @return void
    * @throws \Illuminate\Validation\ValidationException
    */
    public function old_transferMoneyAPIAction(Request $request){
            if($request->method() == 'POST'){
                 $id = $request->get('id'); 
                 try{
                    $enPostStr      = $request->get('enPostStr');
                    $decyrptPostStr = Crypt::decryptString($enPostStr);
                    $postArr        = json_decode($decyrptPostStr,true);

                    $amount           = $postArr['amount']; 
                    $request->merge(['amount'=>$amount]); 
                    $transfermoneyArr = $this->getAmountFractionArray($amount);

                    //Set All Variable Of Request
                    $validator               = array();
                    $walletBalance           = Helper::getWalletBalance(); 
                    $mobile                  = $request->get('mobile');
                    $balance                 = $request->get('balance');
                    $r_mobile                = $request->get('r_mobile');
                    $account_name            = $request->get('account_name');
                    $account_number          = $request->get('account_number');
                    $bank_name               = $request->get('bank_name');
                    $account_ifsc            = $request->get('account_ifsc');
                    $amount                  = $request->get('amount');
                    $fee                     = $request->get('fee');
                    $remarks                 = $request->get('remarks');
                    $payment_mode            = $request->get('payment_mode');
                    $verifyBeneficiariesId   = $request->get('beneficiaries_bank_account_id');
                    $verify_mobile_number_id = $request->get('verify_mobile_number_id');
                    $verify_mobile_beneficiaries_bank_account_id = $request->get('verify_mobile_beneficiaries_bank_account_id');
                    $errorArr = array();
                    $transferResponseArr = array();

                    $result = VerifyMobileBeneficiariesBankAccount::with('VerifybeneficiariesBankAccount')->find($verifyBeneficiariesId);
                    $verify_beneficiaries_bank_account_id =$result['VerifybeneficiariesBankAccount']['id']; 
                    
                    //dd($request->all());
                    //dd($transfermoneyArr);
                    //dd($postArr);
                    //Verify Balance
                    if($walletBalance > $amount){ 
                    
                    // $contact_id             =   $result['VerifybeneficiariesBankAccount']['contact_id'];
                    // $beneficiary_id         =   $result['VerifybeneficiariesBankAccount']['beneficiary_id'];
                    // $verifyMobile           =   VerifyMobileNumber::find($verify_mobile_number_id);
                    //dd($request->all());
                    $paymentTransfer        = $this->WireAPI->moneyTransferInitiateAPI($request, $verify_beneficiaries_bank_account_id);
                    //dd($paymentTransfer);
                    $paymentTransferArr     = json_decode($paymentTransfer,true);
                    if($paymentTransferArr['success']){
                        $verify_mobile_number_id    = $verify_mobile_number_id;
                        $verify_mobile_beneficiaries_bank_account_id = $verify_beneficiaries_bank_account_id;
                        $res =$this->paymentTransfered($paymentTransferArr, $verify_mobile_number_id,$verify_mobile_beneficiaries_bank_account_id);
                        if($res){
                            //Update Payment Wallet Transactions For User
                            $user_id    = $this->getUserId();
                            $verifyMobileResult = VerifyMobileNumber::find($verify_mobile_number_id);
                            $this->pushDebitRequestedBalanceAmount($user_id, $amount, $remarks, $verify_mobile_beneficiaries_bank_account_id);
                            $this->pushDebitIntoVerifiedMobileMonthlyTransactions($verify_mobile_number_id,$amount);
                            $midStr    = $verifyMobileResult['mobile'].'|'.date('Ymd'); 
                            $enMidStr  = Crypt::encryptString($midStr);
                            return redirect('RO/bankaccountlist/'.$enMidStr)->with('message', "Money Transffred Successfully!!");
                        }
                    }
                }else{
                    Session::flash('message', "You don't have sufficent balance !!");
                    return redirect()->back()->withInput();
                }
                }catch (DecryptException $e) {
                    $message = "Somthing Went Wring, Please try again later";
                    return redirect('RO/confirmtransfermoney/'.$id)->with('message', $message);
                }
            }

            
           
            
          
    }


    /*
    if(!empty($result)){
                            //Call Api for Transfer Amount From Wallet to Bank Account
                            foreach($transfermoneyArr as $amountItem){
                                $walletBalance  = Helper::getWalletBalance(); 
                                //Add Money Transfer Charge Amount with Tramsfer Amount
                                $moneytransferCharge = $this->getTransferChargeWithamount($amountItem);
                                if($walletBalance > ($amountItem + $moneytransferCharge['charge'])){
                                    $request->merge(['amount'=>$amountItem]); 
                                    $paymentTransfer    = $this->WireAPI->moneyTransferInitiateAPI($request, $verify_beneficiaries_bank_account_id);
                                    //dd($paymentTransfer);
                                    $paymentTransferArr = json_decode($paymentTransfer,true);
                                    $res   = $this->paymentTransfered($paymentTransferArr, $verify_mobile_number_id,$verify_mobile_beneficiaries_bank_account_id);
                                    if($res == true){ 
                                        $user_id    = $this->getUserId();
                                        $verifyMobileResult = VerifyMobileNumber::find($verify_mobile_number_id);
                                        $this->pushDebitRequestedBalanceAmount($user_id, $amount, $remarks, $verify_mobile_beneficiaries_bank_account_id);
                                        $this->pushDebitIntoVerifiedMobileMonthlyTransactions($verify_mobile_number_id,$amount);
                                        $midStr    = $verifyMobileResult['mobile'].'|'.date('Ymd'); 
                                        $enMidStr  = Crypt::encryptString($midStr);
                                        $transferResponseArr[] = array(
                                                                        'success'   =>  true,
                                                                        'status'    =>  'Success',
                                                                        'amount'    =>  $amountItem,
                                                                        'ref_no'    =>  NULL,
                                                                        'message'   =>  'Transfer Successfull.',
                                                                        'response'  =>  $paymentTransferArr
                                        );
                                    }

                                }else{
                                        $transferResponseArr[] = array(
                                                                    'success'       =>  false,
                                                                    'status'        =>  'Failed',
                                                                    'amount'        =>  $amountItem,
                                                                    'reference_no'  =>  NULL,
                                                                    'message'       =>  LOW_WALLET_BALANCE_MESASGE
                                                                );
                                }
                            }
                            //dd($transferResponseArr);
                            //dd($transfermoneyArr);
                            //dd($postArr);

                        }else{
                            dd("Benificiery Account Validation Failed");
                        }
                        */


    //First Payment Initiated After Adding New Account
    public function paymentTransfered($paymentTransferArray, $verify_mobile_number_id, $verify_mobile_beneficiaries_bank_account_id){

        if ($paymentTransferArray['success']) {
            $user_id    = $this->getUserId();
            $paymentTransferArr = $paymentTransferArray['data']['transfer_request'];

            $amount     = $paymentTransferArr['amount'];
            $narration  = $paymentTransferArr['narration'];
            $bankTxnObj = new BankAccountTransaction();
            $bankTxnObj['user_id']                  =   $this->getUserId();
            $bankTxnObj['status']                   =   $paymentTransferArr['status'];
            $bankTxnObj['verify_mobile_number_id']  =   $verify_mobile_number_id;
            $bankTxnObj['verify_mobile_beneficiaries_bank_account_id']  =   $verify_mobile_beneficiaries_bank_account_id;
            $bankTxnObj['unique_request_number']    =   $paymentTransferArr['unique_request_number'];
            $bankTxnObj['transfer_request_id']      =   $paymentTransferArr['id'];
            $bankTxnObj['failure_reason']           =   $paymentTransferArr['failure_reason'];
            $bankTxnObj['beneficiary_id']           =   $paymentTransferArr['beneficiary_id'];
            $bankTxnObj['transaction_date']         =   $paymentTransferArr['created_at'];
            $bankTxnObj['unique_transaction_reference']  =   $paymentTransferArr['unique_transaction_reference'];
            $bankTxnObj['payment_mode']             =   $paymentTransferArr['payment_mode'];
            $bankTxnObj['amount']                   =   $paymentTransferArr['amount'];
            $bankTxnObj['currency']                 =   $paymentTransferArr['currency'];
            $bankTxnObj['narration']                =   $paymentTransferArr['narration'];
            $bankTxnObj['beneficiary_bank_name']    =   $paymentTransferArr['beneficiary_bank_name'];
            $bankTxnObj['beneficiary_account_name'] =   $paymentTransferArr['beneficiary_account_name'];
            $bankTxnObj['beneficiary_account_number']=  $paymentTransferArr['beneficiary_account_number'];
            $bankTxnObj['beneficiary_account_ifsc']  =  $paymentTransferArr['beneficiary_account_ifsc'];
            $bankTxnObj['beneficiary_upi_handle']    =  $paymentTransferArr['beneficiary_upi_handle'];
            $bankTxnObj['service_charge']            =  $paymentTransferArr['service_charge'];
            $bankTxnObj['gst_amount']                =  $paymentTransferArr['gst_amount'];
            $bankTxnObj['service_charge_with_gst']   =  $paymentTransferArr['service_charge_with_gst'];
            $bankTxnObj['queue_on_low_balance']      =  $paymentTransferArr['queue_on_low_balance'];
            $bankTxnObj['udf5']                      =  $paymentTransferArr['udf5'];
            $bankTxnObj['udf4']                      =  $paymentTransferArr['udf4'];
            $bankTxnObj['udf3']                      =  $paymentTransferArr['udf3'];
            $bankTxnObj['udf2']                      =  $paymentTransferArr['udf2'];
            $bankTxnObj['udf1']                      =  $paymentTransferArr['udf1'];
            $bankTxnObj['created_at']                =  $this->getNow();

            if($bankTxnObj->save()){
                return true;
            }
        }
    }
    







    /**
    * @param  \Illuminate\Http\Request  $request
    * Request parameters with Bank Account Number, IFSC Code, 
    * @return void
    * @throws \Illuminate\Validation\ValidationException
    */
     public function addNewRecipenetForMobile(Request $request){
         $result =array("success"=>false,"message"=>"Error.");

         if ($request->isMethod('post')) {
            $name               = $request->get('name');
            $mobile             = $request->get('mobile');            
            $typeBtn            = $request->get('typeBtn');
            
            //addonly = verify
            $account_no         = $request->get('account_no');
            $master_bank_id     = $request->get('master_bank_id');
            $IFSCCode           = $request->get('ifsccodeStr');
            $hiddenid           = $request->get('verify_mobile_id');
            $beneficiary_mob_no = $request->get('mobileNumber');
            $user_id            = Auth::user()->id;

            //Find Is this Account Number Added Previously into Esebuzz
            $isPresent = VerifyBeneficiariesBankAccount::where('account_number','=',$account_no)
            ->where('account_ifsc','=',$IFSCCode)
            ->where('contact_id','!=',NULL)
            ->where('beneficiary_id','!=',NULL)
            ->first();
            if(!empty($isPresent)){
                 $VerifyMobileNumber = Crypt::decryptString($hiddenid);
                 //Check this Account Number is already present into his account
                 $isPresentAccount = VerifyMobileBeneficiariesBankAccount::where('verify_beneficiaries_bank_account_id','=',$isPresent['id'])
                 ->where('verify_mobile_number_id','=',$VerifyMobileNumber)
                 ->where('user_id','=',$this->getUserId())
                 ->first();
                 if(empty($isPresentAccount)){
                    $VMBBAObj = new VerifyMobileBeneficiariesBankAccount();
                    $VMBBAObj['verify_beneficiaries_bank_account_id']   = $isPresent['id'];
                    $VMBBAObj['user_id']                                = $this->getUserId();
                    $VMBBAObj['verify_mobile_number_id']                = $VerifyMobileNumber;
                    $VMBBAObj['status']                                 = 1;
                    $VMBBAObj['recipient_number']                       = $beneficiary_mob_no;
                    $VMBBAObj['created_at']                             = $this->getNow();
                    if($VMBBAObj->save()){
                        $result =array("success"=>true,"message"=>"Recipent Added Successfully.");
                    }else{
                        $result =array("success"=>false,"message"=>"Recipent Not Added Successfully.");
                    }
                }else{
                    $result =array("success"=>false,"message"=>"Recipent Bank Account Already present.");
                }
            }else{

                $verifyRecipient = array(
                        'contact_id'        =>  NULL,
                        'beneficiary_id'    =>  NULL,
                        'beneficiary_type'  =>  'bank_account',
                        'recipient_number'  =>  $mobile,
                        'bank_name'         =>  $master_bank_id,
                        'account_name'      =>  $name,
                        'account_number'    =>  $account_no,
                        'account_ifsc'      =>  $IFSCCode,
                        'upi_handle'        =>  '',
                        'is_active'         =>  0,
                        'is_primary'        =>  0,
                        'status'            =>  0,
                        'created_at'        =>  $this->getNow()
                );
                $lastId = VerifyBeneficiariesBankAccount::create($verifyRecipient)->id;
                if($lastId){
                    $VerifyMobileNumber = Crypt::decryptString($hiddenid);
                    $VMBBAObj = new VerifyMobileBeneficiariesBankAccount();
                    $VMBBAObj['verify_beneficiaries_bank_account_id']   = $lastId;
                    $VMBBAObj['user_id']                                = $this->getUserId();
                    $VMBBAObj['verify_mobile_number_id']                = $VerifyMobileNumber;
                    $VMBBAObj['status']                                 = 1;
                    $VMBBAObj['recipient_number']                       = $beneficiary_mob_no;
                    $VMBBAObj['created_at']                             = $this->getNow();
                    if($VMBBAObj->save()){
                        $result =array("success"=>true,"message"=>"Recipent Added Successfully.");
                    }else{
                        $result =array("success"=>false,"message"=>"Recipent Not Added Successfully.");
                    }
                }else{
                    $result =array("success"=>false,"message"=>"Somthing went wrong.","redirect"=>false);
                }
            }
            return response()->json($result);
         }
    }

}
