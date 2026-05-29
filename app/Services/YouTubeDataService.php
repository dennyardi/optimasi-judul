<?php

namespace App\Services;

use App\Models\AppSetting;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class YouTubeDataService
{
    private const RANGE_MONTHS = [
        '1_month' => 1,
        '3_months' => 3,
        '6_months' => 6,
        '1_year' => 12,
    ];

    public function analyzeKeyword(AppSetting $settings, string $keyword): ?array
    {
        if (! $settings->hasYouTubeApiKey()) {
            return null;
        }

        $search = $this->request('search', [
            'part' => 'snippet',
            'q' => $keyword,
            'type' => 'video',
            'maxResults' => 10,
            'order' => 'relevance',
            'regionCode' => 'ID',
            'relevanceLanguage' => 'id',
            'safeSearch' => 'none',
            'key' => $settings->youtube_api_key,
        ]);

        $items = $search['items'] ?? [];
        $videoIds = collect($items)
            ->map(fn (array $item) => Arr::get($item, 'id.videoId'))
            ->filter()
            ->values()
            ->all();

        if ($videoIds === []) {
            return [
                'source' => 'youtube_data_api',
                'keyword' => $keyword,
                'total_results' => (int) Arr::get($search, 'pageInfo.totalResults', 0),
                'videos' => [],
                'summary' => [
                    'average_views' => 0,
                    'highest_views' => 0,
                    'average_engagement' => 0,
                    'sample_size' => 0,
                ],
            ];
        }

        $stats = $this->request('videos', [
            'part' => 'snippet,statistics',
            'id' => implode(',', $videoIds),
            'key' => $settings->youtube_api_key,
        ]);

        $videos = collect($stats['items'] ?? [])->map(function (array $video): array {
            $views = (int) Arr::get($video, 'statistics.viewCount', 0);
            $likes = (int) Arr::get($video, 'statistics.likeCount', 0);
            $comments = (int) Arr::get($video, 'statistics.commentCount', 0);

            return [
                'video_id' => (string) Arr::get($video, 'id'),
                'title' => (string) Arr::get($video, 'snippet.title'),
                'channel_title' => (string) Arr::get($video, 'snippet.channelTitle'),
                'published_at' => (string) Arr::get($video, 'snippet.publishedAt'),
                'views' => $views,
                'likes' => $likes,
                'comments' => $comments,
                'engagement_rate' => $views > 0 ? round((($likes + $comments) / $views) * 100, 2) : 0,
                'url' => 'https://www.youtube.com/watch?v='.Arr::get($video, 'id'),
            ];
        })->values();

        return [
            'source' => 'youtube_data_api',
            'keyword' => $keyword,
            'total_results' => (int) Arr::get($search, 'pageInfo.totalResults', 0),
            'videos' => $videos->all(),
            'summary' => [
                'average_views' => (int) round($videos->avg('views') ?? 0),
                'highest_views' => (int) ($videos->max('views') ?? 0),
                'average_engagement' => round($videos->avg('engagement_rate') ?? 0, 2),
                'sample_size' => $videos->count(),
            ],
        ];
    }

    public function analyzeCompetitorChannel(AppSetting $settings, string $channelInput, string $range): array
    {
        if (! $settings->hasYouTubeApiKey()) {
            throw new RuntimeException('YouTube Data API key belum disimpan di Settings.');
        }

        $months = self::RANGE_MONTHS[$range] ?? 3;
        $publishedAfter = CarbonImmutable::now('Asia/Jakarta')->subMonths($months);
        $channel = $this->resolveChannel($settings, $channelInput);
        $videos = $this->getRecentUploads($settings, $channel['uploads_playlist_id'], $publishedAfter);

        return [
            'range' => $range,
            'range_label' => $this->rangeLabel($range),
            'published_after' => $publishedAfter->toDateString(),
            'channel' => $channel,
            'summary' => $this->summarizeVideos($videos, $months),
            'upload_hours' => $this->rankedCounts($videos->pluck('upload_hour'))->take(8)->values()->all(),
            'upload_days' => $this->rankedCounts($videos->pluck('upload_day'))->values()->all(),
            'title_keywords' => $this->extractTitleKeywords($videos),
            'top_videos' => $videos->sortByDesc('views')->take(8)->values()->all(),
            'recent_videos' => $videos->sortByDesc('published_at')->take(10)->values()->all(),
            'insights' => $this->buildInsights($videos, $months),
        ];
    }

    private function resolveChannel(AppSetting $settings, string $input): array
    {
        $channelId = $this->extractChannelId($input);
        $handle = $this->extractHandle($input);

        if ($channelId) {
            $channel = $this->channelByQuery($settings, ['id' => $channelId]);
        } elseif ($handle) {
            $channel = $this->channelByQuery($settings, ['forHandle' => $handle]);

            if (blank(Arr::get($channel, 'items.0')) && str_starts_with($handle, '@')) {
                $channel = $this->channelByQuery($settings, ['forHandle' => ltrim($handle, '@')]);
            }
        } else {
            $search = $this->request('search', [
                'part' => 'snippet',
                'q' => $input,
                'type' => 'channel',
                'maxResults' => 1,
                'key' => $settings->youtube_api_key,
            ]);

            $foundChannelId = Arr::get($search, 'items.0.snippet.channelId');

            if (! $foundChannelId) {
                throw new RuntimeException('Channel YouTube tidak ditemukan. Coba gunakan URL channel atau handle @channel.');
            }

            $channel = $this->channelByQuery($settings, ['id' => $foundChannelId]);
        }

        $item = Arr::get($channel, 'items.0');

        if (! $item) {
            throw new RuntimeException('Channel YouTube tidak ditemukan atau API key tidak punya akses.');
        }

        $uploadsPlaylistId = Arr::get($item, 'contentDetails.relatedPlaylists.uploads');

        if (! $uploadsPlaylistId) {
            throw new RuntimeException('Playlist upload channel tidak ditemukan.');
        }

        return [
            'id' => (string) Arr::get($item, 'id'),
            'title' => (string) Arr::get($item, 'snippet.title'),
            'description' => (string) Arr::get($item, 'snippet.description'),
            'thumbnail' => (string) (Arr::get($item, 'snippet.thumbnails.medium.url') ?: Arr::get($item, 'snippet.thumbnails.default.url')),
            'uploads_playlist_id' => (string) $uploadsPlaylistId,
            'subscriber_count' => (int) Arr::get($item, 'statistics.subscriberCount', 0),
            'video_count' => (int) Arr::get($item, 'statistics.videoCount', 0),
            'view_count' => (int) Arr::get($item, 'statistics.viewCount', 0),
            'url' => 'https://www.youtube.com/channel/'.Arr::get($item, 'id'),
        ];
    }

    private function channelByQuery(AppSetting $settings, array $query): array
    {
        return $this->request('channels', [
            ...$query,
            'part' => 'snippet,contentDetails,statistics',
            'key' => $settings->youtube_api_key,
        ]);
    }

    private function getRecentUploads(AppSetting $settings, string $playlistId, CarbonImmutable $publishedAfter): Collection
    {
        $items = collect();
        $pageToken = null;

        for ($page = 0; $page < 10; $page++) {
            $response = $this->request('playlistItems', array_filter([
                'part' => 'snippet,contentDetails',
                'playlistId' => $playlistId,
                'maxResults' => 50,
                'pageToken' => $pageToken,
                'key' => $settings->youtube_api_key,
            ]));

            $pageItems = collect($response['items'] ?? []);
            $items = $items->merge($pageItems);

            $oldestOnPage = $pageItems
                ->map(fn (array $item) => CarbonImmutable::parse((string) Arr::get($item, 'contentDetails.videoPublishedAt', Arr::get($item, 'snippet.publishedAt')), 'UTC'))
                ->filter()
                ->min();

            $pageToken = Arr::get($response, 'nextPageToken');

            if (! $pageToken || ($oldestOnPage && $oldestOnPage->lessThan($publishedAfter->setTimezone('UTC')))) {
                break;
            }
        }

        $recent = $items
            ->filter(function (array $item) use ($publishedAfter): bool {
                $publishedAt = CarbonImmutable::parse((string) Arr::get($item, 'contentDetails.videoPublishedAt', Arr::get($item, 'snippet.publishedAt')), 'UTC');

                return $publishedAt->greaterThanOrEqualTo($publishedAfter->setTimezone('UTC'));
            })
            ->values();

        $videoIds = $recent
            ->map(fn (array $item) => Arr::get($item, 'contentDetails.videoId'))
            ->filter()
            ->values();

        if ($videoIds->isEmpty()) {
            return collect();
        }

        $statsItems = collect();

        foreach ($videoIds->chunk(50) as $chunk) {
            $stats = $this->request('videos', [
                'part' => 'snippet,statistics,contentDetails',
                'id' => $chunk->implode(','),
                'key' => $settings->youtube_api_key,
            ]);

            $statsItems = $statsItems->merge($stats['items'] ?? []);
        }

        return $statsItems->map(function (array $video): array {
            $publishedAtUtc = CarbonImmutable::parse((string) Arr::get($video, 'snippet.publishedAt'), 'UTC');
            $publishedAtJakarta = $publishedAtUtc->setTimezone('Asia/Jakarta');
            $views = (int) Arr::get($video, 'statistics.viewCount', 0);
            $likes = (int) Arr::get($video, 'statistics.likeCount', 0);
            $comments = (int) Arr::get($video, 'statistics.commentCount', 0);

            return [
                'video_id' => (string) Arr::get($video, 'id'),
                'title' => (string) Arr::get($video, 'snippet.title'),
                'published_at' => $publishedAtJakarta->toDateTimeString(),
                'upload_hour' => $publishedAtJakarta->format('H:00'),
                'upload_day' => $publishedAtJakarta->format('l'),
                'views' => $views,
                'likes' => $likes,
                'comments' => $comments,
                'engagement_rate' => $views > 0 ? round((($likes + $comments) / $views) * 100, 2) : 0,
                'url' => 'https://www.youtube.com/watch?v='.Arr::get($video, 'id'),
            ];
        })->values();
    }

    private function summarizeVideos(Collection $videos, int $months): array
    {
        $count = $videos->count();

        return [
            'uploaded_videos' => $count,
            'uploads_per_month' => $months > 0 ? round($count / $months, 1) : $count,
            'uploads_per_week' => $months > 0 ? round($count / ($months * 4.345), 1) : $count,
            'average_views' => (int) round($videos->avg('views') ?? 0),
            'highest_views' => (int) ($videos->max('views') ?? 0),
            'average_engagement' => round($videos->avg('engagement_rate') ?? 0, 2),
            'total_views' => (int) $videos->sum('views'),
            'total_likes' => (int) $videos->sum('likes'),
            'total_comments' => (int) $videos->sum('comments'),
        ];
    }

    private function rankedCounts(Collection $values): Collection
    {
        return $values
            ->filter()
            ->countBy()
            ->map(fn (int $count, string $label) => ['label' => $label, 'count' => $count])
            ->sortByDesc('count')
            ->values();
    }

    private function extractTitleKeywords(Collection $videos): array
    {
        $stopWords = collect([
            'yang', 'dan', 'di', 'ke', 'dari', 'ini', 'itu', 'untuk', 'dengan', 'dalam', 'paling', 'full',
            'terbaru', 'gus', 'iqdam', 'official', 'live', 'video', 'shorts', 'part', 'eps', 'episode',
            'the', 'a', 'an', 'of', 'to', 'in', 'on', 'for', 'is', 'are',
        ]);

        return $videos
            ->flatMap(function (array $video) use ($stopWords): array {
                $title = Str::of($video['title'])
                    ->lower()
                    ->replaceMatches('/[^a-z0-9\s@#]/i', ' ')
                    ->squish()
                    ->value();

                return collect(explode(' ', $title))
                    ->filter(fn (string $word) => strlen($word) >= 3 && ! $stopWords->contains($word))
                    ->values()
                    ->all();
            })
            ->countBy()
            ->sortDesc()
            ->take(20)
            ->map(fn (int $count, string $keyword) => ['keyword' => $keyword, 'count' => $count])
            ->values()
            ->all();
    }

    private function buildInsights(Collection $videos, int $months): array
    {
        if ($videos->isEmpty()) {
            return [
                'Belum ada video dalam range waktu ini, coba pilih range lebih panjang.',
            ];
        }

        $topHour = $this->rankedCounts($videos->pluck('upload_hour'))->first();
        $topDay = $this->rankedCounts($videos->pluck('upload_day'))->first();
        $topVideo = $videos->sortByDesc('views')->first();
        $uploadsPerWeek = $months > 0 ? round($videos->count() / ($months * 4.345), 1) : $videos->count();

        return array_values(array_filter([
            $topHour ? 'Jam upload paling sering: '.$topHour['label'].' WIB sebanyak '.$topHour['count'].' video.' : null,
            $topDay ? 'Hari upload paling sering: '.$topDay['label'].' sebanyak '.$topDay['count'].' video.' : null,
            'Rata-rata frekuensi upload: '.$uploadsPerWeek.' video per minggu.',
            $topVideo ? 'Video performa tertinggi: "'.$topVideo['title'].'" dengan '.number_format($topVideo['views']).' views.' : null,
            'Bandingkan keyword judul yang sering muncul dengan keyword channel kamu untuk menemukan angle yang belum ramai.',
        ]));
    }

    private function extractChannelId(string $input): ?string
    {
        if (preg_match('/(UC[a-zA-Z0-9_-]{20,})/', $input, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function extractHandle(string $input): ?string
    {
        if (preg_match('/@([a-zA-Z0-9._-]+)/', $input, $matches)) {
            return '@'.$matches[1];
        }

        return null;
    }

    private function rangeLabel(string $range): string
    {
        return match ($range) {
            '1_month' => '1 bulan terakhir',
            '3_months' => '3 bulan terakhir',
            '6_months' => '6 bulan terakhir',
            '1_year' => '1 tahun terakhir',
            default => '3 bulan terakhir',
        };
    }

    private function request(string $endpoint, array $query): array
    {
        $response = Http::acceptJson()
            ->timeout(30)
            ->retry(2, 300)
            ->get("https://www.googleapis.com/youtube/v3/{$endpoint}", $query);

        try {
            $response->throw();
        } catch (RequestException $exception) {
            $message = Arr::get($exception->response?->json() ?? [], 'error.message', $exception->getMessage());
            throw new RuntimeException('YouTube Data API gagal: '.$message, previous: $exception);
        }

        return $response->json();
    }
}
