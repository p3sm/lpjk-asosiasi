<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonalPendidikan extends Model
{
  protected $table = 'personal_pendidikan';

  protected $primaryKey = 'ID_Personal_Pendidikan';
  
  public function kabupaten()
  {
    return $this->belongsTo('App\Kabupaten', 'ID_Kabupaten');
  }
  
  public function jenjang()
  {
    return $this->belongsTo('App\Pendidikan', 'Jenjang');
  }
}
