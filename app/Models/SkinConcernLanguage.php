<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkinConcernLanguage extends Model
{
    protected $table = 'skin_concerns_languages';

    protected $fillable = [
        'skin_concern_id',
        'language_id',
        'name'
    ];

    public function skinConcern(): BelongsTo
    {
        return $this->belongsTo(SkinConcern::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
