<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodyPartLanguage extends Model
{
    protected $table = 'body_parts_languages';

    protected $fillable = [
        'body_part_id',
        'language_id',
        'name'
    ];

    public function bodyPart(): BelongsTo
    {
        return $this->belongsTo(BodyPart::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
