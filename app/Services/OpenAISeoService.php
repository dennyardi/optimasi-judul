<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAISeoService
{
    public function generate(AppSetting $settings, string $videoTitle, string $shortDescription): array
    {
        if (! $settings->hasApiKey()) {
            throw new RuntimeException('OpenAI API key belum disimpan di Settings.');
        }

        $response = Http::withToken($settings->openai_api_key)
            ->acceptJson()
            ->timeout(90)
            ->retry(2, 500)
            ->post(rtrim(config('services.openai.base_url'), '/').'/responses', [
                'model' => $settings->ai_model,
                'input' => $this->messages($settings, $videoTitle, $shortDescription),
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        'name' => 'youtube_seo_package',
                        'strict' => true,
                        'schema' => $this->schema(),
                    ],
                ],
            ]);

        try {
            $response->throw();
        } catch (RequestException $exception) {
            $message = Arr::get($exception->response?->json() ?? [], 'error.message', $exception->getMessage());
            throw new RuntimeException('OpenAI request gagal: '.$message, previous: $exception);
        }

        $payload = $response->json();
        $json = $this->extractText($payload);

        if (! is_string($json) || blank($json)) {
            throw new RuntimeException('OpenAI tidak mengembalikan JSON yang bisa dibaca.');
        }

        $data = json_decode($this->normalizeJson($json), true);

        if (! is_array($data)) {
            throw new RuntimeException('JSON OpenAI tidak valid.');
        }

        return [
            'titles' => array_values($data['titles'] ?? []),
            'title_scores' => array_values($data['title_scores'] ?? []),
            'description' => (string) ($data['description'] ?? ''),
            'tags' => array_slice(array_values($data['tags'] ?? []), 0, 10),
            'hashtags' => array_values($data['hashtags'] ?? []),
            'pin_comment' => (string) ($data['pin_comment'] ?? ''),
        ];
    }

    public function researchKeywords(AppSetting $settings, string $seedKeyword, ?array $youtubeData = null): array
    {
        if (! $settings->hasApiKey()) {
            throw new RuntimeException('OpenAI API key belum disimpan di Settings.');
        }

        $response = Http::withToken($settings->openai_api_key)
            ->acceptJson()
            ->timeout(90)
            ->retry(2, 500)
            ->post(rtrim(config('services.openai.base_url'), '/').'/responses', [
                'model' => $settings->ai_model,
                'input' => $this->keywordMessages($settings, $seedKeyword, $youtubeData),
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        'name' => 'youtube_keyword_research',
                        'strict' => true,
                        'schema' => $this->keywordSchema(),
                    ],
                ],
            ]);

        try {
            $response->throw();
        } catch (RequestException $exception) {
            $message = Arr::get($exception->response?->json() ?? [], 'error.message', $exception->getMessage());
            throw new RuntimeException('OpenAI request gagal: '.$message, previous: $exception);
        }

        $json = $this->extractText($response->json());

        if (! is_string($json) || blank($json)) {
            throw new RuntimeException('OpenAI tidak mengembalikan JSON keyword research yang bisa dibaca.');
        }

        $data = json_decode($this->normalizeJson($json), true);

        if (! is_array($data)) {
            throw new RuntimeException('JSON keyword research OpenAI tidak valid.');
        }

        return $data;
    }

    private function extractText(array $payload): ?string
    {
        $outputText = Arr::get($payload, 'output_text');

        if (is_string($outputText) && filled($outputText)) {
            return $outputText;
        }

        foreach (Arr::get($payload, 'output', []) as $item) {
            foreach (Arr::get($item, 'content', []) as $content) {
                $text = Arr::get($content, 'text');

                if (is_string($text) && filled($text)) {
                    return $text;
                }
            }
        }

        return null;
    }

    private function normalizeJson(string $json): string
    {
        $json = trim($json);

        if (str_starts_with($json, '```')) {
            $json = preg_replace('/^```(?:json)?\s*/i', '', $json) ?? $json;
            $json = preg_replace('/\s*```$/', '', $json) ?? $json;
        }

        return trim($json);
    }

    private function messages(AppSetting $settings, string $videoTitle, string $shortDescription): array
    {
        $context = [
            'channel_name' => $settings->channel_name ?: 'Not specified',
            'channel_niche' => $settings->niche ?: 'Not specified',
            'channel_tone' => $settings->tone ?: 'Professional, concise, helpful',
            'language' => $settings->language ?: 'English',
            'default_cta' => $settings->default_cta ?: 'Invite viewers to like, comment, subscribe, and watch the next relevant video.',
            'auto_capslock_titles' => $settings->auto_capslock_titles ? 'enabled' : 'disabled',
            'description_style' => $settings->description_style === 'short' ? 'simple short description' : 'long detailed description',
        ];

        $titleInstruction = $settings->auto_capslock_titles
            ? 'For SEO titles, use tasteful CAPSLOCK emphasis on 1 to 3 high-impact words or phrases only, never the whole title.'
            : 'For SEO titles, do not use intentional CAPSLOCK emphasis except proper names or standard capitalization.';

        $descriptionInstruction = $settings->description_style === 'short'
            ? 'SEO description must be simple and short: 2 to 4 concise sentences, one CTA, no long outline.'
            : 'SEO description must be long and detailed: 2 to 4 short paragraphs with natural keywords, context, viewer benefit, and CTA.';

        return [
            [
                'role' => 'system',
                'content' => [[
                    'type' => 'input_text',
                    'text' => 'You are an expert YouTube SEO strategist. Return only valid JSON that follows the requested schema. Optimize for search intent, click-through rate, natural readability, and the provided channel context. '.$titleInstruction.' '.$descriptionInstruction,
                ]],
            ],
            [
                'role' => 'user',
                'content' => [[
                    'type' => 'input_text',
                    'text' => "Channel context:\n".json_encode($context, JSON_PRETTY_PRINT)."\n\nVideo title: {$videoTitle}\n\nShort description:\n{$shortDescription}\n\nGenerate a complete YouTube SEO package with 5 distinct titles, title score analysis for each generated title, one SEO description, exactly 10 best meta tags ranked by relevance, hashtags, and one pinned comment. {$titleInstruction} {$descriptionInstruction} Title score analysis must judge CTR potential, keyword clarity, emotional hook, title length, capslock balance, and religious/santun tone safety.",
                ]],
            ],
        ];
    }

    private function keywordMessages(AppSetting $settings, string $seedKeyword, ?array $youtubeData = null): array
    {
        $context = [
            'channel_name' => $settings->channel_name ?: 'Not specified',
            'channel_niche' => $settings->niche ?: 'Not specified',
            'channel_tone' => $settings->tone ?: 'Professional, concise, helpful',
            'language' => $settings->language ?: 'English',
        ];

        return [
            [
                'role' => 'system',
                'content' => [[
                    'type' => 'input_text',
                    'text' => 'You are a YouTube keyword research strategist. Return only valid JSON. Provide practical keyword suggestions. If YouTube Data API context is provided, use it as real competitor evidence and label score analytics as AI-estimated from YouTube sample data. If no YouTube context is provided, clearly label analytics as AI-estimated, not platform-measured. Use channel context and Indonesian YouTube behavior when the language is Indonesian.',
                ]],
            ],
            [
                'role' => 'user',
                'content' => [[
                    'type' => 'input_text',
                    'text' => "Channel context:\n".json_encode($context, JSON_PRETTY_PRINT)."\n\nSeed keyword: {$seedKeyword}\n\nYouTube Data API competitor context:\n".json_encode($youtubeData, JSON_PRETTY_PRINT)."\n\nCreate YouTube keyword research for this seed keyword. Include keyword suggestions, estimated search demand, competition, opportunity score, intent, why it works, audience fit, recommended content angle, and 3 title ideas. Scores must be integers from 1 to 100. Keep suggestions specific, natural, and ready for YouTube SEO. When YouTube context is available, reflect real competitor view levels and engagement in the competition and opportunity reasoning.",
                ]],
            ],
        ];
    }

    private function schema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['titles', 'title_scores', 'description', 'tags', 'hashtags', 'pin_comment'],
            'properties' => [
                'titles' => [
                    'type' => 'array',
                    'minItems' => 5,
                    'maxItems' => 5,
                    'items' => [
                        'type' => 'string',
                        'description' => 'YouTube title with selective CAPSLOCK emphasis on 1 to 3 high-impact words or phrases, not the whole title.',
                    ],
                ],
                'title_scores' => [
                    'type' => 'array',
                    'minItems' => 5,
                    'maxItems' => 5,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => [
                            'title',
                            'overall_score',
                            'ctr_potential',
                            'keyword_clarity',
                            'emotional_hook',
                            'length_score',
                            'capslock_balance',
                            'tone_safety',
                            'recommendation',
                        ],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'overall_score' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
                            'ctr_potential' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
                            'keyword_clarity' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
                            'emotional_hook' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
                            'length_score' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
                            'capslock_balance' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
                            'tone_safety' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
                            'recommendation' => ['type' => 'string'],
                        ],
                    ],
                ],
                'description' => ['type' => 'string'],
                'tags' => [
                    'type' => 'array',
                    'minItems' => 10,
                    'maxItems' => 10,
                    'items' => ['type' => 'string'],
                ],
                'hashtags' => [
                    'type' => 'array',
                    'minItems' => 5,
                    'maxItems' => 12,
                    'items' => ['type' => 'string'],
                ],
                'pin_comment' => ['type' => 'string'],
            ],
        ];
    }

    private function keywordSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['summary', 'seed_keyword', 'disclaimer', 'keywords'],
            'properties' => [
                'summary' => ['type' => 'string'],
                'seed_keyword' => ['type' => 'string'],
                'disclaimer' => ['type' => 'string'],
                'keywords' => [
                    'type' => 'array',
                    'minItems' => 8,
                    'maxItems' => 12,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => [
                            'keyword',
                            'search_demand',
                            'competition',
                            'opportunity_score',
                            'intent',
                            'audience_fit',
                            'why_it_works',
                            'content_angle',
                            'title_ideas',
                        ],
                        'properties' => [
                            'keyword' => ['type' => 'string'],
                            'search_demand' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
                            'competition' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
                            'opportunity_score' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
                            'intent' => ['type' => 'string'],
                            'audience_fit' => ['type' => 'string'],
                            'why_it_works' => ['type' => 'string'],
                            'content_angle' => ['type' => 'string'],
                            'title_ideas' => [
                                'type' => 'array',
                                'minItems' => 3,
                                'maxItems' => 3,
                                'items' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
