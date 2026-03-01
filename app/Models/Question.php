<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['survey_id','question_text','type','options','is_required','sort_order'];
    protected $casts = ['options' => 'array', 'is_required'=>'boolean'];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}
