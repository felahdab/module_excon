<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;

use App\Models\User as BaseUser;

class User extends BaseUser
{
    public function units()
    {
        return $this->belongsToMany(Unit::class, "excon_user_units");
    }

    public function sides()
    {
        return $this->belongsToMany(Side::class, "excon_user_sides");
    }

    public function getSideAttribute(): ?Side
    {
        $unit = $this->unit;
        if ($unit)
        {
            return $unit->side;
        }

        $side = $this->sides?->first();

        return $side;
    }

    public function getUnitAttribute()
    {
        return $this->units?->first();
    }

    public static function fromBaseUser(BaseUser $user)
    {
        $excon_user = new static();
        $excon_user->forceFill($user->toArray());
        return $excon_user;
    }
}
