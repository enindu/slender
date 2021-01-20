<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
  use SoftDeletes;

  public $timestamps = true;
  protected $table = "categories";

  /**
   * Has many subcategories
   * 
   * @return HasMany
   */
  public function subcategories(): HasMany
  {
    return $this->hasMany(Subcategory::class, 'category_id', 'id');
  }
}
