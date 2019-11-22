<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
  protected $table = 'personal';

  protected $primaryKey = 'ID_Personal';

  protected $casts = ['ID_Personal' => 'string'];
}
