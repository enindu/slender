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

  /**
   * Has many files
   * 
   * @return HasMany
   */
  public function files(): HasMany
  {
    return $this->hasMany(File::class, 'section_id', 'id');
  }

  /**
   * Has many contents
   * 
   * @return HasMany
   */
  public function contents(): HasMany
  {
    return $this->hasMany(Content::class, 'section_id', 'id');
  }

  /**
   * Has many categories
   * 
   * @return HasMany
   */
  public function categories(): HasMany
  {
    return $this->hasMany(Category::class, 'section_id', 'id');
  }
}
