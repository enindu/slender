<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Slider extends Model
{
  public $timestamps = true;
  protected $table = "sliders";

  /**
   * Has one type
   * 
   * @return HasOne
   */
  public function type(): HasOne
  {
    return $this->hasOne(Type::class, 'id', 'type_id');
  }
}
