<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $data = [
            'details' => [
                ...$user->only(['name', 'phone', 'email', 'university', 'metric_id', 'department', 'designation']),
                'avatar' => $user->profile_path,
            ],
        ];

        return Inertia::render('Dashboard/Profile/Index', $data);
    }

    /**
     * Update the user's profile avarat.
     */
    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if(Storage::disk('public')->exists('users/' . $user->profile)) {
            Storage::disk('public')->delete('users/' . $user->profile);
        }

        $photo = $request->file('photo');

        $extension = $photo->getClientOriginalExtension();
        $filename  = 'user-' . $user->id . '-' . time() . '.' . $extension;

        $request->file('photo')->storeAs('users', $filename, 'public');

        $user->update(['profile' => $filename]);

        return redirect()->route('dashboard.profile.index')
            ->with('success', 'Photo updated successfully');
    }

    /**
     * Update the user's profile information.
     */
    public function updateInfo(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|unique:users,phone,' . $user->id,
            'university' => 'nullable',
            'department' => 'nullable|required_with:university',
            'metric_id' => 'nullable|required_with:university',
            'designation' => 'nullable|required_with:university',
        ]);

        $user->update($data);

        return redirect()->back()->with('success', 'Information updated successfully');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update($request->only(['password']));

        return redirect()->back()->with('success', 'Password updated successfully');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
