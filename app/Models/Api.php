<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Api extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $table = "apis";
}
