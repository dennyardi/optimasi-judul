<?php

namespace App\Http\Controllers;

use App\Models\Generation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HistoryController extends Controller
{
    public function index(): View
    {
        return view('history.index', [
            'generations' => Generation::query()->latest()->paginate(10),
        ]);
    }

    public function show(Generation $generation): View
    {
        return view('history.show', [
            'generation' => $generation,
        ]);
    }

    public function destroy(Generation $generation): RedirectResponse
    {
        $generation->delete();

        return redirect()
            ->route('history.index')
            ->with('toast', ['type' => 'success', 'message' => 'History berhasil dihapus.']);
    }
}
