<?php
namespace App\Helpers;
 
use Illuminate\Support\Facades\DB;
use Auth;
use App\MasterBank;
use App\DocumentType;
use App\Country;
use App\State;
use App\City;
use App\PaymentWallet;

class Helper {
	
    /**
     * @return string
     */
    public static function getRouteName() {
        return \Request::route()->getName();         
    }






    /**
     * @return string
     */
    public static function getAllBankName(){
        $masterBank = new MasterBank();
        $bankName   = $masterBank->getAllBank();
        $str = "";
        foreach ($bankName as $key => $value) {
            $str.= '<option value="'.$key.'">'.$value.'</option>';
        }
        return $str;
    }





    /**
     * @return string
     */
    public static function getPaymentMode($type=1){
        $paymentMode = array(
            '1'=>'Cash In Bank',
            '2'=>'CDM',
            '3'=>'NEFT/IMPS/RTGS/FT',
        );

        $str = "";
        foreach ($paymentMode as $key => $value) {
            if($type==$key){ 
              $selected='selected';
              $str.= '<option value="'.$key.'" selected="'.$selected.'">'.$value.'</option>';
            }else{
              $str.= '<option value="'.$key.'">'.$value.'</option>';
            }
            
        }
        return $str;
    }




     /**
     * @return string
     */
    public static function getAESBankName($type=1){
        $paymentMode = array(
            '1'=>'Axis Bank',
            '2'=>'HDFC Bank',
            '3'=>'ICICI Bank',
            '4'=>'DBS Bank',
        );

        $str = "";
        foreach ($paymentMode as $key => $value) {
           if($type==$key){ 
              $selected='selected';
              $str.= '<option value="'.$key.'" selected="'.$selected.'">'.$value.'</option>';
            }else{
              $str.= '<option value="'.$key.'">'.$value.'</option>';
            }
        }
        return $str;
    }





    /**
     * @param  string
     * @return string
     */
    public static function getWalletBalance() {

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



    /**
     * @param  string
     * @return string
     */
    public static function getAmount($number) {
      
            return 'Rs '.number_format($number,2);   
              
    }



    /**
     * @param  string
     * @return string
     */
    public static function isActiveMenu($rountName) {
        
        if(self::getRouteName() == $rountName){
            return 'active';
        }         
    }




    /**
     * @param  string
     * @return string
     */
    public static function getLogo() {
        $str ='<table>
              <tr>
                <td>
                    <a href="'.route('home').'" title="'.env('APP_NAME').'">
                      <img src="'.config("global.THEME_PATH").'/images/logo.png" alt="Quickai" width="65" style="vertical-align:middle" />
                    </a>
                </td>
                <td>
                  <a href="'.route('home').'" title="'.env('APP_NAME').'" style="color:#000">
                  <span style="font-size: 20px;display: block;">SiddhiVentures</span>
                  <small>Recharge & Bill Payment App</small>
                  </a>
                </td>
              </tr>
            </table>';
        return $str;         
    }



    /**
     * @param Id of the payment mode type
     * Get Payment Mode Type name
     */
    public static function getPaymentModeName($id){
      if($id==1){ return 'Cash In Bank';}
      if($id==2){ return 'CDM';}
      if($id==3){ return 'NEFT/IMPS/RTGS';}
    }




    /**
     * @param Id of the Company Bank Name
     * Get Payment Mode Type name
     */
    public static function getCompanyBankName($id){
         $paymentMode = array(
            '1'=>'Axis Bank',
            '2'=>'HDFC Bank',
            '3'=>'ICICI Bank',
            '4'=>'DBS Bank',
        );
      if($id!=''){
        return $paymentMode[$id];
      }
    }





    /**
     * @param Id of the Company Bank Name
     * Get Payment Mode Type name
     */
    public static function getDateFormate($date){
         return date('d M, Y',strtotime($date));
    }




    /**
     * @param Id Of Document Type
     */
    public static function getDocumentProofType($id){
        $documentType = DocumentType::find($id);
        return $documentType['type_name'];
    }






    /**
     * @return string
     */
    public static function getServiceTypeList($type=1){
        $paymentMode = array(
            '1'=>'Servcice Type-1',
            '2'=>'Servcice Type-2',
            '3'=>'Servcice Type-3',
        );

        if($type>0){
            return $paymentMode[$type];
        }
    }


    /**
     * @return string
     */
    public static function getZoneTypeList($type=1){
        $paymentMode = array(
            '1'=>'Zone Type-1',
            '2'=>'Zone Type-2',
            '3'=>'Zone Type-3',
        );

        if($type>0){
            return $paymentMode[$type];
        }
    }

    

    /**
     * @return string
     */
    public static function getCompanyTypeList($type=1){

        $paymentMode = array(
            '1'=>'Comapny Type-1',
            '2'=>'Comapny Type-2',
            '3'=>'Comapny Type-3',
        );
        if($type>0){
            return $paymentMode[$type];
        }
    }



    /**
     * @return string
     */
    public static function getCountryName($id){
        if($id==1){
            return 'India';
        }
        if($id>1){
            $country = Country::find($id);
            return $country;
        }
    }



    /**
     * @return string
     */
    public static function getStateName($id){
      
        if($id>0){
            $obj = State::find($id);
            return $obj['state_name'];
        }
    }



    /**
     * @return string
     */
    public static function getCityName($id){
      
        if($id>0){
            $obj = City::find($id);
            return $obj['city_name'];
        }
    }






    /**
     * Get the Wallet Payment Id of the user
     * Pramas as user Id of the Distributor OR RO
     * @param integer
     * @return integer
     */
    public static function getPaymentWalletDetails($user_id){
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
    public static function isValidPaymentWallet($user_id){
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
     * Get Agent Name
     * Pramas as user array
     * @param array
     * @return string
     */
    public static function getUserProfileName($userArr){
        $userName = "";
        if(!empty($userArr)){
            return $userArr['name'].'-'.$userArr['AgentCode'];
        }
    }



    

    



    }