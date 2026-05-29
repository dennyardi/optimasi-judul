<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Generation;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $settings = AppSetting::current();
        $latest = Generation::query()->latest()->first();

        return view('dashboard.index', [
            'settings' => $settings,
            'latest' => $latest,
            'totalGenerations' => Generation::query()->count(),
            'apiReady' => $settings->hasApiKey(),
        ]);
    }
}
