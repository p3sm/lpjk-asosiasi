<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
  protected $table = 'personal';

  protected $primaryKey = 'ID_Personal';

  protected $casts = ['ID_Personal' => 'string'];
  
  public function pendidikan()
  {
    return $this->hasMany('App\PersonalPendidikan', 'ID_Personal');
  }
  
  public function proyek()
  {
    return $this->hasMany('App\PersonalProyek', 'id_personal');
  }
  
  public function kabupaten()
  {
    return $this->belongsTo('App\Kabupaten', 'ID_Kabupaten_Alamat');
  }
  
  public function provinsi()
  {
    return $this->belongsTo('App\Provinsi', 'ID_Propinsi');
  }
}
