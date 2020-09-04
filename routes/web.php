<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Auth\User\LoginController@showLoginForm')->name('home');


Route::prefix('admin')->namespace('Auth\Admin')->group(function(){

Route::get('login', 'LoginController@showLoginForm')->name('admin.login');
Route::post('login', 'LoginController@login');
Route::post('logout', 'LoginController@logout')->name('admin.logout');

//Reset Password
Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('admin.password.reset');
Route::post('password/reset', 'ResetPasswordController@reset')->name('admin.password.update');


Route::get('password/confirm', 'Auth\ConfirmPasswordController@showConfirmForm')->name('admin.password.confirm');
Route::post('password/confirm', 'Auth\ConfirmPasswordController@confirm');

Route::get('email/verify', 'Auth\VerificationController@show')->name('admin.verification.notice');
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('admin.verification.verify');
Route::post('email/resend', 'Auth\VerificationController@resend')->name('admin.verification.resend');
});


Route::prefix('admin')->namespace('Admin')->group(function(){
	Route::get('dashboard', 'DashboardController@index');
});




/***********************RO Route Start Here*********************************************/


Route::prefix('RO')->namespace('Auth\Ro')->group(function(){

Route::get('login', 'LoginController@showLoginForm')->name('ro.login');
Route::get('verifyOTP/{id}/{password}', 'LoginController@showOTPLoginForm')->name('ro.verifyOTP');
Route::post('verifyotp', 'LoginController@verifyOTPAndLogin')->name('ro.verifyotp');

Route::post('login', 'LoginController@login');
Route::post('logout', 'LoginController@logout')->name('ro.logout');

//Reset Password
Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('ro.password.request');
Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('ro.password.email');
Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('ro.password.reset');
Route::post('password/reset', 'ResetPasswordController@reset')->name('ro.password.update');


Route::get('password/confirm', 'Auth\ConfirmPasswordController@showConfirmForm')->name('ro.password.confirm');
Route::post('password/confirm', 'Auth\ConfirmPasswordController@confirm');

Route::get('email/verify', 'Auth\VerificationController@show')->name('ro.verification.notice');
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('ro.verification.verify');
Route::post('email/resend', 'Auth\VerificationController@resend')->name('ro.verification.resend');
});


Route::prefix('RO')->namespace('RO')->group(function(){
	Route::get('dashboard', 'DashboardController@index')->name('dashboard');
});


/***********************RO Route Ends Here*********************************************/


Route::prefix('user')->namespace('User')->group(function(){
	Route::get('dashboard', 			'DashboardController@index')->name('dashboard');
	
	Route::get('addretailer', 			'DashboardController@addretailer')->name('addretailer');
	Route::any('allretailerlist', 		'DashboardController@allROList')->name('allretailerlist');
	Route::post('addretailer', 			'DashboardController@postAddRetailer')->name('addretailer');
	Route::any('retaileraddress/{id}', 	'DashboardController@retaileraddress')->name('retaileraddress');
	Route::any('retailercompany/{id}', 	'DashboardController@retailerCompanyProof')->name('retailercompany');
	Route::any('roprofile/{id}', 		'DashboardController@retailerProfile')->name('roprofile');
	Route::any('rocompanyprofile/{id}', 'DashboardController@ROCompanyProfile')->name('rocompanyprofile');
	
	Route::any('personaldetails/{id}',	'DashboardController@retailerPersonalDetails')->name('personaldetails');
	Route::any('documentproof/{id}', 	'DashboardController@retailerDocumentProof')->name('documentproof');
	Route::any('viewrodetails/{id}', 	'DashboardController@viewrodetails')->name('viewrodetails');
	
	//Generate Balance Request By Distributor
	Route::any('balancerequest', 		'WalletController@newBalanceRequest')->name('balancerequest');
	Route::any('allbalancerequest', 	'WalletController@allbalancerequest')->name('allbalancerequest');
	Route::any('pushbalance', 			'WalletController@pushBalanceRequest')->name('pushbalance');
	Route::any('pushbalancenow/{id}/{tday}', 'WalletController@TransferBalanceToUser')->name('pushbalancenow');
	Route::any('transfertouserwallet/{id}/{tday}','WalletController@TransferBalanceToUserWallet')->name('transfertouserwallet');
	Route::any('verifytransfer/{id}/{tday}','WalletController@verifyOTPForBalanceTransfer')->name('verifytransfer');
	Route::any('verifyotp','SMSController@isValidOTP')->name('verifyotp');
	Route::any('txncreditsuccess/{id}/{lastid}','WalletController@balanceTransferSuccessfully')->name('txncreditsuccess');
	Route::any('tatrechargeesybuz/{id}','WalletController@tatkalWalletRechargeEaseBuzz')->name('tatrechargeesybuz');
	Route::any('tatrechargeesybuz','WalletController@tatkalWalletRechargeEaseBuzz')->name('tatrechargeesybuz');
	Route::any('confirmrecharge','WalletController@confirmRechargeTatkalWalletRechargeEaseBuzz')->name('confirmrecharge');
	Route::post('confirmationorder','WalletController@confirmationOrderPage')->name('confirmationorder');
	Route::any('rechargesuccess','WalletController@walletRechargeSuccess')->name('rechargesuccess');
	Route::any('rechargefailed','WalletController@walletRechargeFailed')->name('rechargefailed');
	Route::any('walletcredited/{id}','WalletController@walletCredited')->name('walletcredited');
	
	
});



Route::any('rechargesuccess','User\WalletController@walletRechargeSuccess')->name('rechargesuccess');
Route::any('rechargefailed','User\WalletController@walletRechargeFailed')->name('rechargefailed');
Route::any('walletcredited/{id}','User\WalletController@walletCredited')->name('walletcredited');


Route::get('verifyOTP/{id}/{password}', 'Auth\User\LoginController@showOTPLoginForm')->name('verifyOTP');
Route::post('verifyotp', 				'Auth\User\LoginController@verifyOTPAndLogin')->name('user.verifyotp');

Route::get('register', 					'Auth\User\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 				'Auth\User\RegisterController@register')->name('register');
Route::any('uploaddocument/{id}', 'Auth\User\RegisterController@registerUploadDocument')->name('uploaddocument');

Route::get('login', 					'Auth\User\LoginController@showLoginForm')->name('login');
Route::post('login', 					'Auth\User\LoginController@login');
Route::post('logout', 					'Auth\User\LoginController@logout')->name('logout');


//Reset Password
Route::get('password/reset', 'Auth\User\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\User\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\User\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\User\ResetPasswordController@reset')->name('password.update');


Route::get('password/confirm', 'Auth\User\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('password/confirm', 'Auth\User\ConfirmPasswordController@confirm');

Route::get('email/verify', 'Auth\User\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'Auth\User\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'Auth\User\VerificationController@resend')->name('verification.resend');

