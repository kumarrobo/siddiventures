<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WireAPIController extends Controller
{


   
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:user');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        return view('home');
    }


    
    /*****************************************WIRE API START HERE**********************/
    private function getMerchentKey(){
        return config('global.MONEY_MERCHANT_KEY');
    }

    private function getMerchentSalt(){
        return config('global.MONEY_SALT');
    }

    private function getAddContctAPIURL(){
        return config('global.ADD_CONTACT_API');
    }
    private function getAddBeneficiariesAPIURL(){
        return config('global.ADD_BENEFICIARIES_API');
    }
    private function getBeneficiariesAPIURL(){
        return config('global.ADD_BENEFICIARIES_API');
    }


    /**
    * Add New Beneficiery Account Number For Contact
    * @param  \Illuminate\Http\Request  $request
    * @return void    *
    */
    public function addWireBeneficiariesAPI(Request $request,$data){
         if ($request->isMethod('post')) {
            $contact_id         = $data['contact_id'];
            $beneficiary_type   = $data['beneficiary_type'];
            $beneficiary_name   = $data['beneficiary_name'];
            $account_number     = $data['account_number'];
            $ifsc               = $data['ifsc'];


            $merchentKey    = $this->getMerchentKey();
            $salt           = $this->getMerchentSalt();
            //SHA-512 hash of string in the format of “[key]|[name]|[email]|[phone]|[salt]”
            $keyStr             = $merchentKey.'|'.$contact_id.'|'.$beneficiary_name.'|'.$account_number.'|'.$ifsc.'||'.$salt; 
            $AuthorizationKey   = hash('sha512', $keyStr);
            $url                = $this->getAddBeneficiariesAPIURL();
             $data = array(
                'key'               => $merchentKey,
                'contact_id'        => $contact_id,
                'beneficiary_name'  => $beneficiary_name,
                'beneficiary_type'  => $beneficiary_type,
                'account_number'    => $account_number,
                'ifsc'              => $ifsc,
            );
            $result = $this->postCurlData($data,$url,$AuthorizationKey);
            return json_decode($result,true);
         }
    }






     /**
    * @param  \Illuminate\Http\Request  $request
    * @return void    *
    */
    public function getWireBeneficiariesAPI($data){
            $contact_id         = $data['contact_id'];
            $pageSize           = $data['pageSize'];
            $current            = $data['current'];
            

            $merchentKey    = $this->getMerchentKey();
            $salt           = $this->getMerchentSalt();
            //SHA-512 hash of string in the format of “[key]|[name]|[email]|[phone]|[salt]”
            $keyStr             = $merchentKey.'|'.'bene75dc42fe43be9d76e2ca84ee18ca'.'|'.$salt; 
            $AuthorizationKey   = hash('sha512', $keyStr);
            $url                = $this->getBeneficiariesAPIURL().'bene75dc42fe43be9d76e2ca84ee18ca'.'/';
             $data = array(
                'key'        => $merchentKey,
            );
            $result = $this->getCurlData($data,$url,$AuthorizationKey);
            return json_decode($result,true);
    }




    /**
    * @param  \Illuminate\Http\Request  $request
    * @return void    *
    */
    public function addWireContactAPI(Request $request){
         if ($request->isMethod('post')) {
            $mobile         = $request->get('mobile');
            $sender_name    = $request->get('sender_name');
            $email          = $request->get('email');

            $merchentKey    = $this->getMerchentKey();
            $salt           = $this->getMerchentSalt();
            //SHA-512 hash of string in the format of “[key]|[name]|[email]|[phone]|[salt]”
            $keyStr             = $merchentKey.'|'.$sender_name.'|'.$email.'|'.$mobile.'|'.$salt; 
            $AuthorizationKey   = hash('sha512', $keyStr);
            $url                = $this->getAddContctAPIURL();
             $data = array(
                'key'        => $merchentKey,
                'name'       => $sender_name,
                'email'      => $email,
                'phone'      => $mobile
            );
            $result = $this->postCurlData($data,$url,$AuthorizationKey);
            return $result;
         }
    }



     private function postCurlData($data,$url,$AuthorizationKey){
        $payload = json_encode($data); 
        $ch = curl_init($url);
        $headr = array();
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: '.$AuthorizationKey;
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($ch);
        // if ($result === false)
        // {
        //     // throw new Exception('Curl error: ' . curl_error($crl));
        //     print_r('Curl error: ' . curl_error($ch));
        // }
        curl_close($ch);
        return $result; 
    }




     private function getCurlData($data,$url,$AuthorizationKey){
        //$payload = json_encode($data); 
        $ch = curl_init($url);
        $data = http_build_query($data);
        $getUrl = $url."?".$data;
        $headr = array();
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: '.$AuthorizationKey;
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result; 
    }



    /*****************************************WIRE API ENDS HERE***********************/
}
