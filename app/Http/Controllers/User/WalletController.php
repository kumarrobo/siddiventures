<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use Log;
use Hash;
use Auth;
use App\User;
use App\State;
use App\City;
use App\UserDetail;
use App\DocumentType;
use App\DsWalletBalanceRequest;

class WalletController extends Controller
{
    //


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
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
            'cdm_branch_name'       => ['required'],
            'remarks2'              => ['required'],
            
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


    
   

}
