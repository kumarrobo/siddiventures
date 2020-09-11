<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerifyBankAccount extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'verify_bank_accounts';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','mobile','otp_number','transfer_limit','is_verified','status','created_at'
    ];



    public function MasterBank() {
         return $this->belongsTo('App\MasterBank', 'master_bank_id', 'id' );
    }
    
   
}
