<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SliderType extends Model
{
  public $timestamps = true;
  protected $table = "slider_types";
}
