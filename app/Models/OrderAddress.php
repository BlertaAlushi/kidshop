<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderAddress extends Model
{
    protected $fillable = [
        "name",
        "phone",
        "address",
        "zip",
        "city",
        "country",
        "email"
    ];

    public function orders(): HasMany{
        return $this->hasMany(Order::class);
    }
}
