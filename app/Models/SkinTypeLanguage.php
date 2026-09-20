<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkinTypeLanguage extends Model
{
    protected $table = 'skin_types_languages';

    protected $fillable = [
        'skin_type_id',
        'language_id',
        'name'
    ];

    public function skinType(): BelongsTo
    {
        return $this->belongsTo(SkinType::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
