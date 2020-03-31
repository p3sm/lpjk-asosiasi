<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AsosiasiSign extends Model
{
  protected $table = 'master_asosiasi_sign';
    
  public function asosiasi()
  {
    return $this->belongsTo('App\Asosiasi', 'asosiasi_id');
  }
}
