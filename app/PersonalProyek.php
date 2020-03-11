<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonalProyek extends Model
{
  protected $table = 'personal_proyek';

  protected $primaryKey = 'id_personal_proyek';
  
  public function lokasi()
  {
    return $this->belongsTo('App\Provinsi', 'Lokasi');
  }
}
