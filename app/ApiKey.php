<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
  protected $connection = 'mysql';
  protected $table = 'api_key';
  protected $primaryKey = 'id';
  
  public $timestamps = false;
}
