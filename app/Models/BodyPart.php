<?php

namespace App\Models;

use App\Services\LanguageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BodyPart extends Model
{
    use HasFactory;

    protected $fillable =[
      "name",
      "slug"
    ];

    public function translations(): HasMany{
        return $this->hasMany(BodyPartLanguage::class);
    }

    public function translation()
    {
        return $this->hasOne(BodyPartLanguage::class)
            ->where('language_id', LanguageService::getCurrentLanguageId());
    }

    public function products(): BelongsToMany{
        return $this->belongsToMany(Product::class);
    }

}
