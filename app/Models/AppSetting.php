<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'openai_api_key',
        'youtube_api_key',
        'ai_model',
        'auto_capslock_titles',
        'description_style',
        'channel_name',
        'niche',
        'tone',
        'language',
        'default_cta',
    ];

    protected function casts(): array
    {
        return [
            'openai_api_key' => 'encrypted',
            'youtube_api_key' => 'encrypted',
            'auto_capslock_titles' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate(
            ['id' => 1],
            [
                'ai_model' => 'gpt-5-mini',
                'auto_capslock_titles' => true,
                'description_style' => 'long',
                'tone' => 'Professional, concise, helpful',
                'language' => 'English',
            ]
        );
    }

    public function hasApiKey(): bool
    {
        return filled($this->openai_api_key);
    }

    public function hasYouTubeApiKey(): bool
    {
        return filled($this->youtube_api_key);
    }
}
