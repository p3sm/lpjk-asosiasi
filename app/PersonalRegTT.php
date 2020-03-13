<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonalRegTT extends Model
{
  protected $table = 'personal_reg_tt';

  protected $primaryKey = 'ID_Registrasi_TK_Trampil';
    
  public function personal()
  {
    return $this->belongsTo('App\Personal', 'ID_Personal');
  }
    
  public function asosiasi()
  {
    return $this->belongsTo('App\Asosiasi', 'ID_Asosiasi_Profesi');
  }
    
  public function ustk()
  {
    return $this->belongsTo('App\Ustk', 'id_unit_sertifikasi');
  }
    
  public function kualifikasi()
  {
    return $this->belongsTo('App\Kualifikasi', 'ID_Kualifikasi');
  }
}
