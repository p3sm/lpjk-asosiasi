<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserAsosiasi extends Authenticatable
{
    protected $table =  'user_asosiasi';
    
    public $timestamps = false;
    
    public function detail()
    {
      return $this->belongsTo('App\Asosiasi', 'asosiasi_id');
    }
    
    public function provinsi()
    {
      return $this->belongsTo('App\Provinsi', 'provinsi_id');
    }
}
