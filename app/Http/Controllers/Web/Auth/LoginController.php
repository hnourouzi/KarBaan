<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, AuthServiceInterface $auth): RedirectResponse
    {
        $auth->attempt($request->toDto());

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
