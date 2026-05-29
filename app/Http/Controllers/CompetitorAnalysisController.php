<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompetitorAnalysisRequest;
use App\Models\AppSetting;
use App\Services\YouTubeDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CompetitorAnalysisController extends Controller
{
    public function index(): View
    {
        return view('competitors.index', [
            'settings' => AppSetting::current(),
            'analysis' => session('competitor_analysis'),
            'channelInput' => session('channel_input'),
            'range' => session('range', '3_months'),
        ]);
    }

    public function store(CompetitorAnalysisRequest $request, YouTubeDataService $youTube): RedirectResponse
    {
        $settings = AppSetting::current();
        $validated = $request->validated();

        try {
            $analysis = $youTube->analyzeCompetitorChannel(
                $settings,
                $validated['channel_input'],
                $validated['range']
            );
        } catch (RuntimeException $exception) {
            Log::warning('Competitor analysis failed', [
                'message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('toast', ['type' => 'error', 'message' => $exception->getMessage()]);
        }

        return redirect()
            ->route('competitors.index')
            ->with('channel_input', $validated['channel_input'])
            ->with('range', $validated['range'])
            ->with('competitor_analysis', $analysis)
            ->with('toast', ['type' => 'success', 'message' => 'Competitor analysis berhasil dibuat.']);
    }
}
