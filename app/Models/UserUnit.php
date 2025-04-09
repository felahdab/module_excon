<?php

namespace Modules\Excon\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;

use Carbon\Carbon;

use App\Models\User;
use Modules\Excon\Traits\HasTablePrefix;
use Modules\Excon\Models\Unit;

class UserUnit extends Model
{
    use HasTablePrefix;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "user_id",
        "unit_id",
        "data",
    ];

    protected $casts = [
        "data" => "json"
    ];


    public function unit()
    {
        return $this->belongsTo(Unit::class);   
    }

    public function user()
    {
        return $this->belongsTo(User::class);   
    }
}
