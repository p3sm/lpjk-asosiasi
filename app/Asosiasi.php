<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asosiasi extends Model
{
  protected $table = 'master_asosiasi';

  protected $primaryKey = 'id_asosiasi';

  protected $casts = ['id_asosiasi' => 'string'];
    
  public function detail()
  {
    return $this->hasMany('App\AsosiasiDetail', 'asosiasi_id');
  }
    
  public function sign()
  {
    return $this->hasMany('App\AsosiasiSign', 'asosiasi_id');
  }

  public function verifikatorSign() {
    return $this->sign()->where('type', "verifikator");
  }

  public function databaseSign() {
    return $this->sign()->where('type', "database");
  }
}
