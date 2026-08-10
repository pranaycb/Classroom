<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    /**
     * Render registration form
     */
    public function index()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        /**
         * Dispatch an event
         */
        event(new Registered($user));

        /**
         * After registration login user
         */
        Auth::login($user);

        return redirect()->route('dashboard.index');
    }
}
