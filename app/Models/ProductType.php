<?php

namespace App\Models;

use App\Services\LanguageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductType extends Model
{
    protected $fillable =[
        "name",
        "slug"
    ];

    public function translations(): HasMany{
        return $this->hasMany(ProductTypeLanguage::class);
    }
    public function translation()
    {
        return $this->hasOne(ProductTypeLanguage::class)
            ->where('language_id', LanguageService::getCurrentLanguageId());
    }

    public function products(): BelongsToMany{
        return $this->belongsToMany(Product::class);
    }
}
