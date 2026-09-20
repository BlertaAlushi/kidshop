<?php

namespace App\Models;

use App\Services\LanguageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkinType extends Model
{
    protected $fillable =[
        "name",
        "slug"
    ];

    public function translations(): HasMany{
        return $this->hasMany(SkinTypeLanguage::class);
    }

    public function translation()
    {
        return $this->hasOne(SkinTypeLanguage::class)
            ->where('language_id', LanguageService::getCurrentLanguageId());
    }

    public function products(): BelongsToMany{
        return $this->belongsToMany(Product::class);
    }
}
