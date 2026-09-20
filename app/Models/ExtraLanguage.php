<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraLanguage extends Model
{
    protected $table = 'extras_languages';

    protected $fillable = [
        'extra_id',
        'language_id',
        'name'
    ];

    public function extra(): BelongsTo
    {
        return $this->belongsTo(Extra::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
