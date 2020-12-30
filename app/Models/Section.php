<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
  use SoftDeletes;
  
  public $timestamps = true;
  protected $table = "sections";

  /**
   * Has many images
   * 
   * @return HasMany
   */
  public function images(): HasMany
  {
    return $this->hasMany(Image::class, 'section_id', 'id');
  }
}
