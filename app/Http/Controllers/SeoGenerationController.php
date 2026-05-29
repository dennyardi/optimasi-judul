<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateSeoRequest;
use App\Models\AppSetting;
use App\Models\Generation;
use App\Services\OpenAISeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SeoGenerationController extends Controller
{
    public function create(): View
    {
        return view('seo.create', [
            'settings' => AppSetting::current(),
            'generation' => session('generation_id')
                ? Generation::query()->find(session('generation_id'))
                : null,
        ]);
    }

    public function store(GenerateSeoRequest $request, OpenAISeoService $openAI): RedirectResponse
    {
        $settings = AppSetting::current();
        $validated = $request->validated();

        try {
            $result = $openAI->generate(
                $settings,
                $validated['video_title'],
                $validated['short_description']
            );
        } catch (RuntimeException $exception) {
            Log::warning('SEO generation failed', [
                'message' => $exception->getMessage(),
                'model' => $settings->ai_model,
            ]);

            return back()
                ->withInput()
                ->with('toast', ['type' => 'error', 'message' => $exception->getMessage()]);
        }

        $generation = Generation::query()->create([
            'video_title' => $validated['video_title'],
            'short_description' => $validated['short_description'],
            'generated_titles' => $result['titles'],
            'generated_title_scores' => $result['title_scores'],
            'generated_description' => $result['description'],
            'generated_tags' => $result['tags'],
            'generated_hashtags' => $result['hashtags'],
            'generated_pin_comment' => $result['pin_comment'],
            'ai_model' => $settings->ai_model,
        ]);

        return redirect()
            ->route('seo.create')
            ->with('generation_id', $generation->id)
            ->with('toast', ['type' => 'success', 'message' => 'SEO package berhasil digenerate.']);
    }
}
