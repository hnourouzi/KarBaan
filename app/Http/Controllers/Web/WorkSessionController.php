<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkSession\EndWorkSessionRequest;
use App\Http\Requests\WorkSession\StartWorkSessionRequest;
use App\Models\WorkSession;
use App\Services\Contracts\WorkSessionServiceInterface;
use Illuminate\Http\RedirectResponse;

class WorkSessionController extends Controller
{
    public function __construct(private WorkSessionServiceInterface $sessions) {}

    public function store(StartWorkSessionRequest $request): RedirectResponse
    {
        $this->sessions->start($request->toDto()->plan, now());

        return back()->with('success', 'جلسه کاری جدید شروع شد.');
    }

    public function end(EndWorkSessionRequest $request, WorkSession $workSession): RedirectResponse
    {
        $this->sessions->end($workSession, now());

        return back()->with('success', 'جلسه کاری بسته شد.');
    }
}
