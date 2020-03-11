<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ustk extends Model
{
  protected $table = 'master_ustk';

  protected $primaryKey = 'id_unit_sertifikasi';

  protected $casts = ['id_unit_sertifikasi' => 'string'];
}
