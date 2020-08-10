<?php
namespace App\Helpers;
 
use Illuminate\Support\Facades\DB;
use Auth;

class Helper {
	
    /**
     * @return string
     */
    public static function getRouteName() {
        return \Request::route()->getName();         
    }


    





    /**
     * @param  string
     * @return string
     */
    public static function getWalletBalance() {

        if(Auth::guard('user')->check()){
            return number_format(25000,2);   
        }

        if(Auth::guard('ro')->check()){
            return number_format(15000,2);   
        }
    
              
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






    



    }