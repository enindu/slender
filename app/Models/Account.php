<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $table = "accounts";

    public function role(): HasOne
    {
        return $this->hasOne(Role::class, "id", "role_id");
    }
}
