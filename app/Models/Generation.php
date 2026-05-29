<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    protected $fillable = [
        'video_title',
        'short_description',
        'generated_titles',
        'generated_title_scores',
        'generated_description',
        'generated_tags',
        'generated_hashtags',
        'generated_pin_comment',
        'ai_model',
    ];

    protected function casts(): array
    {
        return [
            'generated_titles' => 'array',
            'generated_title_scores' => 'array',
            'generated_tags' => 'array',
            'generated_hashtags' => 'array',
        ];
    }
}
