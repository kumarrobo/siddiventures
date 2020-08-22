<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\SMSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use Log;
use Hash;
use Auth;
use DB;
use App\User;
use App\State;
use App\City;
use App\UserDetail;
use App\DocumentType;
use App\DsWalletBalanceRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\PaymentWalletTransaction;
use App\PaymentWallet;


class WalletController extends Controller
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


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function newBalanceRequest(Request $request)
    {
        //dd("ok");
        $paymentMode = '';
        if ($request->isMethod('post')) {
            $validator = $this->validatorWallet($request->all());
            if($validator->fails()) {
                $error=$validator->errors()->all();
                Session::flash('error', $error);
                foreach($request->all() as $k=>$value){
                    Session::flash($k, $request->get($k));
                }
                return redirect()->back()->withErrors($validator)->withInput();
             }else{

                 //If Payment Mode is Cash In Bank
                 if($request->get('paymentMode') == 1){
                    $paymentMode = 1;

                    $validator = $this->validatorCashInBank($request->all()); 
                     if($validator->fails()) {
                        $error=$validator->errors()->all();
                        Session::flash('paymentMode', '1');
                        Session::flash('error', $error);
                        foreach($request->all() as $k=>$value){
                            Session::flash($k, $request->get($k));
                        }
                        return redirect()->back()->withErrors($validator)->withInput();
                     }else{
                        //dd($request->all());
                         $walletrequest = $this->saveNewBalanceRequest($request->all());
                         if($walletrequest!= null){
                             $id = $walletrequest['id'];
                             return redirect()->back()->withErrors($validator)
                            ->with('message', 'Wallet Balance Requested Successfully!!. Your Request ID: '.$id);
                        }else{
                             return redirect()->back()
                            ->with('message', 'Somthing went wrong, Please try again later!!');
                        }
                     }
                 }
                 //Cash in Bank Ends Hrere




                 //If Payment Mode is Cash In Bank
                 if($request->get('paymentMode') == 2){
                    $paymentMode = 2;

                    $validator = $this->validatorCashInMachine($request->all()); 
                     if($validator->fails()) {
                        $error=$validator->errors()->all();
                        Session::flash('paymentMode', '2');
                        Session::flash('error', $error);
                        foreach($request->all() as $k=>$value){
                            Session::flash($k, $request->get($k));
                        }
                        return redirect()->back()->withErrors($validator)->withInput();
                     }else{
                        //dd($request->all());
                         $walletrequest = $this->saveNewBalanceRequestForCDM($request->all());
                         if($walletrequest!= null){
                             $id = $walletrequest['id'];
                             return redirect()->back()->withErrors($validator)
                            ->with('message', 'Wallet Balance Requested Successfully!!. Your Request ID: '.$id);
                        }else{
                             return redirect()->back()
                            ->with('message', 'Somthing went wrong, Please try again later!!');
                        }
                     }
                 }
                 //Cash in Bank Ends Hrere


                    //If Payment Mode is Cash In Bank
                 if($request->get('paymentMode') == 3){
                    $paymentMode = 3;

                    $validator = $this->validatorCashNEFT($request->all()); 
                     if($validator->fails()) {
                        $error=$validator->errors()->all();
                        Session::flash('paymentMode', '3');
                        Session::flash('error', $error);
                        foreach($request->all() as $k=>$value){
                            Session::flash($k, $request->get($k));
                        }
                        return redirect()->back()->withErrors($validator)->withInput();
                     }else{
                        //dd($request->all());
                         $walletrequest = $this->saveNewBalanceRequestForNEFT($request->all());
                         if($walletrequest!= null){
                             $id = $walletrequest['id'];
                             return redirect()->back()->withErrors($validator)
                            ->with('message', 'Wallet Balance Requested Successfully!!. Your Request ID: '.$id);
                        }else{
                             return redirect()->back()
                            ->with('message', 'Somthing went wrong, Please try again later!!');
                        }
                     }
                 }
                 //Cash in Bank Ends Hrere


                 
             }

        }
        return view('user.DSDashboard',['paymentMode'=>$paymentMode]);
    }




    //Save Balance Request For Machine
    private function saveNewBalanceRequestForNEFT($data){
              $user_id = Auth::user()->id; 
              return DsWalletBalanceRequest::create([
                    'user_id'               => $user_id,
                    'requested_amount'      => $data['request_amount'],
                    'payment_date'          => $this->getFormatDate($data['transfer_date']),
                    'wallet_balance_in'     => $data['request_in'],
                    'payment_mode_type_id'  => $data['paymentMode'],
                    'company_bank_id'       => $data['neft_aes_bank_name'],
                    'neft_transfer_date'    => $this->getFormatDate($data['neft_transfer_date']),
                    'depositer_name'        => $data['neft_sender_name'],
                    'sender_account_number' => $data['sender_account_number'],
                    'neft_via_bank_id'      => $data['neft_bank_name'],
                    'transaction_number'    => $data['transaction_number'],
                    'remarks'               => $data['remarks3'],
                    'created_at'            => $this->getNow()
            ]);

    }



  //Save Balance Request For Machine
    private function saveNewBalanceRequestForCDM($data){
              $user_id = Auth::user()->id; 
              return DsWalletBalanceRequest::create([
                    'user_id'               => $user_id,
                    'requested_amount'      => $data['request_amount'],
                    'payment_date'          => $this->getFormatDate($data['transfer_date']),
                    'wallet_balance_in'     => $data['request_in'],
                    'payment_mode_type_id'  => $data['paymentMode'],

                    'company_bank_id'       => $data['aes_bank_name'],
                    'sender_mobile_number'  => $data['depositer_mobile_number'],
                    'bank_branch_code'      => $data['cdm_branch_name'],
                    'remarks'               => $data['remarks2'],
                    'created_at'            => $this->getNow()
            ]);

    }




    //Save Balance Request For Cash In Bank
    private function saveNewBalanceRequest($data){
              $user_id = Auth::user()->id; 
              return DsWalletBalanceRequest::create([
                    'user_id'               => $user_id,
                    'requested_amount'      => $data['request_amount'],
                    'payment_date'          => $this->getFormatDate($data['payment_date']),
                    'wallet_balance_in'     => $data['request_in'],
                    'payment_mode_type_id'  => $data['paymentMode'],
                    'depositer_name'        => $data['depositer_name'],
                    'company_bank_id'       => $data['aes_bank_name'],
                    'bank_location'         => $data['depositing_location'],
                    'bank_branch_code'      => $data['depositor_branch_code'],
                    'remarks'               => $data['remarks1'],
                    'created_at'            => $this->getNow()
            ]);

    }



    
    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatorCashInBank(array $data)
    {
        return Validator::make($data, [
            'depositer_name'        => ['required'],
            'aes_bank_name'         => ['required'],
            'depositing_location'   => ['required'],
            
            
        ],[
            'depositer_name.required'   => 'Please enter depositor name.', // custom message
            'aes_bank_name.required'    => 'Choose AES deposite bank name', // custom message
            'depositing_location.required'=> 'Please Enter depositing location deails', // custom message
           ]
       );
       
    }



    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatorCashInMachine(array $data)
    {
        return Validator::make($data, [
            'transfer_date'          => ['required'],
            'aes_bank_name'          => ['required'],
            'depositer_mobile_number'=> ['required'],
            'cdm_branch_name'        => ['required'],
            'remarks2'               => ['required'],
            
        ],[
            'transfer_date.required'    => 'Please choose transfer date.', // custom message
            'aes_bank_name.required'    => 'Choose AES deposite bank name', // custom message
            'depositer_mobile_number.required'=> 'Please Enter depositer mobile number', // custom message
            'cdm_branch_name.required'=> 'Please Enter branch name', // custom message
            'remarks2.required'=> 'Please Enter remarks' // custom message
           ]
       );
       
    }



     /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatorCashNEFT(array $data)
    {
        return Validator::make($data, [
            'neft_transfer_date'     => ['required'],
            'neft_sender_name'       => ['required'],
            'sender_account_number'  => ['required'],
            'neft_bank_name'         => ['required'],
            'neft_aes_bank_name'     => ['required'],
            'transaction_number'     => ['required'],
            'remarks3'               => ['required'],
            
        ],[
            'neft_transfer_date.required'   => 'Please choose transfer date.', // custom message
            'neft_bank_name.required'   => 'Choose NEFT/RTGS/FT/IMPS Via Bank Name', // custom message
            'neft_aes_bank_name.required'   => 'Choose NEFT/RTGS/FT/IMPS Sent to Bank Name', // custom message
            'neft_sender_name.required'     => 'Please enter sender name', // custom message
            'sender_account_number.required'=> 'Please enter sender account number', // custom message
            'transaction_number.required'=> 'Please enter transaction number', // custom message
            'remarks3.required'=> 'Please Enter remarks' // custom message
           ]
       );
       
    }




     /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatorWallet(array $data)
    {
        return Validator::make($data, [
            'request_amount'=> ['required'],
            'payment_date'  => ['required'],
            'paymentMode'   => ['required'],
            
        ],[
            'request_amount.required'=> 'Request Amount Required', // custom message
            'payment_date.required'=> 'Payment Date Required', // custom message
            'paymentMode.required'=> 'Payment Mode  Required' // custom message
           ]
       );
    }



      /**
     * All Balacne  request generated by the DS.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function allBalanceRequest(Request $request)
    {
        $user_id = Auth::user()->id;
        $allRequest = DsWalletBalanceRequest::where('user_id','=',$user_id)->paginate(15);
        //dd($allRequest);
        return view('user.allBalanceRequest',['allRequest'=>$allRequest]);
    }





    /**
     * All Balacne  request generated by the DS.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function pushBalanceRequest(Request $request)
    {
        $userDetails = array();

        if ($request->isMethod('post')) {
             $validator = $this->validatorPushBalance($request->all()); 
             if($validator->fails()) {
                $error=$validator->errors()->all();
                Session::flash('error', $error);
                foreach($request->all() as $k=>$value){
                    Session::flash($k, $request->get($k));
                }
                return redirect()->back()->withErrors($validator)->withInput();
             }else{

                $agent_id = $request->get('agent_id');
                $mobile = $request->get('mobile');

                $authMobile = Auth::user()->mobile;
                $authId     = Auth::user()->id;

                if($authMobile == $mobile){
                     Session::flash('error', ["You can not transfer balance yourself."]);
                     return redirect()->back()->withErrors($validator)->withInput();
                }

                if($agent_id == $authId){
                     Session::flash('error', ["You can not transfer balance yourself."]);
                     return redirect()->back()->withErrors($validator)->withInput();
                }


                if($agent_id!= null){
                    $userDetails = User::with('UserDetail')->where('id','=',$agent_id)->get();

                }
                if($mobile!= null){
                    $userDetails = User::with('UserDetail')->where('mobile','=',$mobile)->get();
                }

                if($userDetails->count() == 0){
                    Session::flash('error', ["No Records Found"]);
                    return redirect()->back()->withErrors($validator)->withInput();
                }          
                //dd($userDetails);  
             }
            
        }
        return view('user.pushBalanceRequest',['ROList'=>$userDetails]);
    }





    /**
     * All Balacne  request generated by the DS.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function TransferBalanceToUser(Request $request,$id,$tday)
    {
        $userDetails =  array();
        try {
            $userId = Crypt::decryptString($id);
            $tdayStr   = Crypt::decryptString($tday);
            //Get Agent Details
            
            if($tdayStr == date('Ymd')){
                $userDetails = User::with('UserDetail','PaymentWallet')->find($userId);

            }else{
                Session::flash('error', ["Somthing went wrong, please try after sometime."]);
                return redirect('user/pushbalance')->with(['error'=>['Sorry ! Somthing went wrong, please try after sometime.']]);    
            }
        } catch (DecryptException $e) {
            Session::flash('error', ["No Records Found"]);
            return redirect('user/pushbalance')->with(['error'=>['Sorry ! Somthing went wrong OR Invalid Url.']]);
        }
        return view('user.TransferBalanceToDSAndRo',[
                    'userDetails'=>$userDetails,
                    'id'=>$id,
                    'tday'=>$tday
        ]);
    }





     /**
     * Transfer Balance to User Wallet From Distributor 
     * Balance From Distributor To Distributor Or RO 
     * Balance Validation and Transaction happen here 
     * @param  \Illuminate\Http\Request  $request
     * @return  array
     */
    public function TransferBalanceToUserWallet(Request $request,$id,$tday)
    {
        
        $userDetails =  array();
        try {
            $userId         = Crypt::decryptString($id);
            $tdayDate       = Crypt::decryptString($tday);
            $amountRequest  = $request->get('amount'); 
            $remarks        = $request->get('remarks'); 
            $otp            = $request->get('OTP');
            //Verify OTP Here and then return

            //Get Agent Details
            if($tdayDate == date('Ymd')){
                $amount = Crypt::decryptString($amountRequest);
                //get Balance of the User
                $totalBalance = $this->getWalletBalance();
                if($totalBalance>=$amount){

                    //Get the Details of the DS OR RO
                    $userDetails = User::with('UserDetail','PaymentWallet')->find($userId);
                    $user_id     = $userDetails['id'];

                    //Push the Requested Amount to User Wallets
                    $requestData  = $request->all();
                    $lastBalacePush = $this->pushCreditRequestedBalanceAmount($user_id,$amount,$requestData);
                    if($lastBalacePush){
                    $lastId = $lastBalacePush['id'];
                    $enId   = Crypt::encryptString($lastId);
                    return redirect('user/txncreditsuccess/'.$id.'/'.$enId)->with(['success'=>["Balance transfer successfully."]]);
                    }else{
                        Session::flash('error', ["Sorry, Somthing Went Wrong, Please Try After Sometime."]);
                        return redirect('user/pushbalancenow/'.$id.'/'.$tday)->with(['error'=>["Sorry, Somthing Went Wrong, Please Try After Sometime."]]);    
                    }

                }else{
                    Session::flash('error', ["Sorry, you don't have sufficient balance"]);
                    return redirect('user/pushbalancenow/'.$id.'/'.$tday)->with(['error'=>["Sorry, you don't have sufficient balance"]]);
                }

            }
        } catch (DecryptException $e) {
            Session::flash('error', ["No Records Found"]);
            return redirect('user/pushbalancenow/'.$id.'/'.$tday)->with(['error'=>['Sorry ! Invalid Url.']]);
        }
        return view('user.TransferBalanceToDSAndRo',[
                    'userDetails'=>$userDetails
        ]);
    }



     /**
     * Validate  for the Push the Balance for agent id OR Mobile Number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatorPushBalance(array $data)
    {
        return Validator::make($data, [
            'agent_id'=> ['required_without:mobile'],
            'mobile'  => ['required_without:agent_id'],
            
        ],[
            'agent_id.required_without:mobile'=> 'Please Enter Agent ID OR Mobile Number', // custom message
            'mobile.required_without.agent_id'=> 'Please Enter Agent ID OR Mobile Number', // custom message
           ]
       );
    }





    /**
    * @param integher
    * @return void
    * @param user_id, requested_amount
    * Distributor and RO as user_id.
    **/
    private function pushCreditRequestedBalanceAmount($user_id,$amount,$requestData){
            $userId         = $user_id;
            $creditAmount   = $amount;
            $remarks        = $requestData['remarks'];
            $newWalletTransaction  = array();          
            // Start transaction!
            DB::beginTransaction();
            try {
                //Call From Main Controller
                if($this->isValidPaymentWallet($userId)){
                    //Get the Wallet Ddetails
                    //Call From Main Controller
                    $getPaymentWalletDetails = $this->getPaymentWalletDetails($userId);
                    // Validate, then create if valid
                    $payment_wallet_id  = $getPaymentWalletDetails['id'];
                    $debit_amount       = '0.00';
                    $credit_amount      = $creditAmount;
                    $transaction_number = $this->getTransactionNumber();
                    $transaction_date   = $this->getNow();
                    $creditByUserId     = Auth::user()->id;
                    $status             = 'Success';
                    $remarks            = $remarks;
                    $created_at         = $this->getTodayDate();
                    
                    $newWalletTransaction = PaymentWalletTransaction::create( 
                        [
                            'payment_wallet_id' => $payment_wallet_id,
                            'debit_amount'      => $debit_amount,
                            'credit_amount'     => $credit_amount,
                            'transaction_number'=> $transaction_number,
                            'transaction_date'  => $transaction_date,
                            'user_id'           => $creditByUserId,
                            'status'            => $status,
                            'remarks'           => $remarks,
                            'created_at'        => $created_at
                        ] );
                    if($newWalletTransaction){
                        $lastPaymentWalletTransactionId = $newWalletTransaction['id'];
                        //After Created PaymentWalletTransaction, Push this balance to Payment
                        // Wallet and update the new balance into that table
                        $this->creditIntoPaymentWallet($userId,$credit_amount,$lastPaymentWalletTransactionId);

                        //Debit From the Wallet of the User
                        $paymentDebitDetails = $this->getPaymentWalletDetails($creditByUserId);
                        // Validate, then create if valid
                        $payment_wallet_id  = $paymentDebitDetails['id'];
                        $debit_amount       = $credit_amount;
                        $credit_amount      = '0.00';
                        $transaction_number = $this->getTransactionNumber();
                        $transaction_date   = $this->getNow();
                        $debitByUserId      = Auth::user()->id;
                        $status             = 'Success';
                        $remarks            = $remarks;
                        $created_at         = $this->getTodayDate();
                        
                        $debitWalletTransaction = PaymentWalletTransaction::create( 
                            [
                                'payment_wallet_id' => $payment_wallet_id,
                                'debit_amount'      => $debit_amount,
                                'credit_amount'     => $credit_amount,
                                'transaction_number'=> $transaction_number,
                                'transaction_date'  => $transaction_date,
                                'user_id'           => $debitByUserId,
                                'status'            => $status,
                                'remarks'           => $remarks,
                                'created_at'        => $created_at
                            ] );
                        if($debitWalletTransaction!=null){
                            $lastPaymentWalletTransactionId = $debitWalletTransaction['id']; 
                            //Deduct the balance from the Wallet  from the user
                            $this->debitFromPaymentWallet($debit_amount,$lastPaymentWalletTransactionId);
                        }
                    }else{
                        return false;
                    }
                }
            } catch(ValidationException $e){
                // Rollback and then redirect
                // back to form with errors
                DB::rollback();
                return false;
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
            $paymentWalletDetails = $this->getPaymentWalletDetails($user_id);
            $id =  $paymentWalletDetails['id'];
            $paymentWalletArr = PaymentWallet::with('User')->find($id);
            
            $mobile = $paymentWalletArr['User']['mobile'];
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
            $paymentWalletDetails = $this->getPaymentWalletDetails($userId);
            $id =  $paymentWalletDetails['id'];
            $paymentWalletArr = PaymentWallet::find($id);
            //Update New amount
            $paymentWalletArr['total_balance'] =  $paymentWalletArr['total_balance'] - $amount;
            if($paymentWalletArr->save()){
                //Send SMS For Deduct Balance
                $this->SMSController->sendDeductSMS($mobile,$amount,$userId,$lastPaymentWalletTransactionId);
                return true;
            }else{
                return false;
            }
        }
        
    }



    /**
    * Method After Transfer the Balance Successfully
    * @param id
    * @param lastid
    * Both id and lastid are encrypted
    * @param  \Illuminate\Http\Request  $request
    * @return void
    * @throws \Illuminate\Validation\ValidationException
    */
    public function balanceTransferSuccessfully(Request $request, $id, $lastid){
        $userDetails = array();
        try {
            $userId                             = Crypt::decryptString($id);
            $lastPaymentWalletTransactionId     = Crypt::decryptString($lastid);

            //Get Last Paymnet Wallet Transaction
            $PaymentWalletTransactionArr = PaymentWalletTransaction::with('PaymentWallet')->find($lastPaymentWalletTransactionId);

            //Get the User Details
            $DSROUserId = $PaymentWalletTransactionArr['PaymentWallet']['user_id'];
            $userArr = User::find($DSROUserId);
            //dd($userArr);
            //dd($PaymentWalletTransactionArr);
        }catch (DecryptException $e) {
            Session::flash('error', ["No Records Found"]);
            return redirect('user/pushbalancenow/'.$id.'/'.$tday)->with(['error'=>['Sorry ! Invalid Url.']]);
        }

        return view('user.balanceTransferSuccessfully',[
                    'userDetails'=>$PaymentWalletTransactionArr,
                    'userArr'=>$userArr
        ]);
    }







    /**
    * Method For Verify OTP
    * @param id
    * @param tday
    * Both id and tday are encrypted
    * @param  \Illuminate\Http\Request  $request
    * @return void
    */
    public function verifyOTPForBalanceTransfer(Request $request, $id, $tday,$amount=null,$remarks=null){
        $userDetails = array();
        $data        = array();

        try {
            $userId         = Crypt::decryptString($id); 
            $tdayDate       = Crypt::decryptString($tday);
            $userDetails    = User::find($userId);
            $requestData    = $request->all();
            $mobile         = Auth::user()->mobile;
            $amount         = $requestData['amount'];
            $remarks        = $requestData['remarks'];

            $enAmountString = Crypt::encryptString($amount);

            //Generate OTP and Save
            $OTPDetails     = $this->SMSController->getNewSMSOTP(Auth::user()->id, $mobile);
            $OTPNumber      = $OTPDetails['OTP'];
            $message ="Your One Time OTP for requesting balance transfer is ".$OTPNumber.'. Please do not share with anyone.';
            //Send Messsage
            if($this->SMSController->sendSMSForVerifyBalanceTransfer($mobile,$message)){
                 Session::flash('message', "One Time Password Sent, Verify OTP Now !!");
            }
            $data['otp']    = '';
            $data['mobile'] = $mobile;
            $mobileNumber   = substr($mobile,0,1).'XXXXXX'.substr($mobile,-2);

        }catch (DecryptException $e) {
            Session::flash('error', ["No Records Found"]);
            return redirect('user/pushbalancenow/'.$id.'/'.$tday)->with(['error'=>['Sorry ! Invalid Url.']]);
        }
        return view('user.verifyOTPForBalanceTransfer',[
                    'userDetails'=> $userDetails,
                    'requestData'=> $requestData,
                    'id'         => $id,
                    'tday'       => $tday,
                    'mobile'     => $mobileNumber,
                    'amount'     => $enAmountString,
                    'remarks'    => $remarks
        ]);
    }






   

}
