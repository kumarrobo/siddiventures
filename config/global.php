<?php
$public_path        = public_path();           // /var/www/html/marketadmin/public
$files_path         = $public_path.'/files';
$base_path          = base_path();               // /var/www/html/marketadmin

$public_url         = env('APP_URL');    // http://marketadmin.localhost/

define('THEME_NAME','siddiventures');
define('THEME_PATH','themes/'.THEME_NAME);

return [

    /*
    * Theme files Path
    */
    'THEME_PATH'     =>  $public_url.'/public/'.THEME_PATH,
    'FILE_PATH'      =>  $public_url.'/public/storage/uploads/RO/',
    'SMS_USERNAME'	 =>	 'Siddhient',
    'SMS_KEY'	 	 =>	 '2f67b0dccfXX',

    
];

?>
