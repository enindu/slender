<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AdminAccount extends Model
{
  public $timestamps = true;
  protected $table = "admin_accounts";

  /**
   * Has one role
   * 
   * @return HasOne
   */
  public function role(): HasOne
  {
    return $this->hasOne(Role::class, 'id', 'role_id');
  }
}
