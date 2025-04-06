<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
    
        // Get the authenticated user
        $user = $request->user();
    
        // Check for different roles and redirect accordingly
        if ($user->usertype === 'admin') {
            return redirect('admin/adminDashboard');
        } elseif ($user->usertype === 'boss') { // Assuming you have a 'role' column in your users table
            return redirect()->route('boss.dashboard'); // Redirect to Boss 1 dashboard
        } elseif($user->usertype === 'section')
        {
            return redirect()->route('section.dashboard');
        }
    
        // Default redirect for users
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');

        // $user = auth()->user()->documents()->create(#data);

    }
}
