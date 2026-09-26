<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $primaryKey = 'iso_2';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'iso_2',
        'country',
        'delivery_fee',
    ];

    // appended so the admin data table (which keys rows off "id") can
    // edit/delete rows the same way it does for auto-incrementing models
    protected $appends = [
        'id',
    ];

    protected function casts(): array
    {
        return [
            'delivery_fee' => 'decimal:2',
        ];
    }

    public function getIdAttribute(): string
    {
        return $this->iso_2;
    }

    public function orderAddresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class, 'country', 'iso_2');
    }
}
