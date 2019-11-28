<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'user_profile';
    
    public function user()
    {
      return $this->belongsTo('App\User', 'user_id');
    }
}
