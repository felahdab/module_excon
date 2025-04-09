<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;

use App\Models\User as BaseUser;

class User extends BaseUser
{
    public function unit()
    {
        return $this->belongsToMany(Unit::class, "excon_user_units");
    }
}
