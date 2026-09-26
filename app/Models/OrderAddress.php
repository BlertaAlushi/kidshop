<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id',
        'type',
        'first_name',
        'last_name',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // named "countryDetails" (not "country") since a relation sharing the
    // "country" column name would be shadowed by the raw attribute value
    public function countryDetails(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country', 'iso_2');
    }
}
