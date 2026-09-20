<?php

namespace App\Models;

use App\Services\LanguageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'price',
        'currency',
        'stock_quantity',
        'mark_id'
    ];

    public function mark(): BelongsTo
    {
        return $this->belongsTo(Mark::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductLanguage::class);
    }

    public function translation()
    {
        return $this->hasOne(ProductLanguage::class)
            ->where('language_id', LanguageService::getCurrentLanguageId());
    }

    public function bodyParts(): BelongsToMany
    {
        return $this->belongsToMany(BodyPart::class, 'product_body_part', 'product_id', 'body_part_id');
    }

    public function productTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProductType::class, 'product_product_type', 'product_id', 'product_type_id');
    }

    public function skinTypes(): BelongsToMany
    {
        return $this->belongsToMany(SkinType::class, 'product_skin_type', 'product_id', 'skin_type_id');
    }

    public function skinConcerns(): BelongsToMany
    {
        return $this->belongsToMany(SkinConcern::class, 'product_skin_concern', 'product_id', 'skin_concern_id');
    }

    public function extras(): BelongsToMany
    {
        return $this->belongsToMany(Extra::class, 'product_extra', 'product_id', 'extra_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class,)
            ->using(OrderProduct::class)
            ->withPivot('quantity','unit_price')
            ->withTimestamps();
    }

    public function getImageAttribute($value)
    {
        return $value
            ? asset('storage/' . $value)
            : null;
    }

    public function getCurrencyAttribute($value){
        if($value=="EUR"){
            return "€";
        }
        return $value;
    }
}
