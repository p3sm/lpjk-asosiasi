<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
  protected $table = 'master_provinsi';

  protected $primaryKey = 'id_provinsi';

  protected $casts = ['id_provinsi' => 'string'];
}
