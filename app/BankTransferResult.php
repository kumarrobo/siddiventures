<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class BankTransferResult extends Model
{

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'user_id', 
            'result_json', 
            'result_encrypt', 
    ];



    public function User() {
         return $this->belongsTo('App\User', 'user_id', 'id' )->getCountry('Country');
    }



}
