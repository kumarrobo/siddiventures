<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class PaymentWalletTransaction extends Model
{

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_wallet_id', 
        'debit_amount',
        'credit_amount',
        'transaction_number',
        'transaction_date',
        'user_id',
        'status',
        'remarks',
        'created_at'
    ];


    public function PaymentWallet() {
         return $this->belongsTo('App\PaymentWallet', 'payment_wallet_id', 'id' );
    }



    public function User() {
         return $this->belongsTo('App\User', 'user_id', 'id' );
    }


   



}
