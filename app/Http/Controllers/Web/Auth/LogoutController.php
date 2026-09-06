<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\RedirectResponse;

class LogoutController extends Controller
{
    public function __invoke(AuthServiceInterface $auth): RedirectResponse
    {
        $auth->logout();

        return redirect()->route('login');
    }
}
