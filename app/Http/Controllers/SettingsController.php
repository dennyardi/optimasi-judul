<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\AppSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', [
            'settings' => AppSetting::current(),
            'models' => UpdateSettingsRequest::MODELS,
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $settings = AppSetting::current();
        $validated = $request->validated();

        foreach (['openai_api_key', 'youtube_api_key'] as $secretKey) {
            if (blank($validated[$secretKey] ?? null)) {
                unset($validated[$secretKey]);
            }
        }

        $validated['auto_capslock_titles'] = $request->boolean('auto_capslock_titles');

        $settings->fill($validated)->save();

        return redirect()
            ->route('settings.edit')
            ->with('toast', ['type' => 'success', 'message' => 'Settings berhasil disimpan.']);
    }
}
