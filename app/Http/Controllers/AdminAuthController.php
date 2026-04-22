<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuthController extends Controller
{
    public function create(Request $request): Response|\Illuminate\Http\RedirectResponse
    {
        if ($request->session()->get(config('admin.session_key'))) {
            return to_route('admin.dashboard');
        }

        return Inertia::render('Admin/Login');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $passwordHash = $this->passwordHash();

        if (! $passwordHash || ! Hash::check($validated['password'], $passwordHash)) {
            throw ValidationException::withMessages([
                'password' => 'The password is incorrect.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put(config('admin.session_key'), true);

        return to_route('admin.dashboard')->with('success', 'Signed in successfully.');
    }

    public function destroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->session()->forget(config('admin.session_key'));
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('admin.login')->with('success', 'Signed out.');
    }

    public static function resolvedPasswordHash(): ?string
    {
        return Setting::getValue(
            config('admin.password_setting_key'),
            config('admin.password_hash'),
        );
    }

    private function passwordHash(): ?string
    {
        return static::resolvedPasswordHash();
    }
}
