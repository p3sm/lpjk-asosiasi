<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asosiasi extends Model
{
  protected $table = 'master_asosiasi';

  protected $primaryKey = 'id_asosiasi';

  protected $casts = ['id_asosiasi' => 'string'];
}
