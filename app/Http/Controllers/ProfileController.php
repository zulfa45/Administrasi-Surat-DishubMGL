<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            try {
                // Delete old avatar if exists and not corrupted ('0')
                if ($user->avatar && $user->avatar !== '0' && \Illuminate\Support\Facades\Storage::disk('s3')->exists($user->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('s3')->delete($user->avatar);
                }
                
                $path = $request->file('avatar')->store('avatars', 's3');
                if ($path) {
                    $user->avatar = $path;
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Profile avatar upload failed: ' . $e->getMessage());
                return back()->withInput()->withErrors(['avatar' => 'Gagal mengunggah foto profil ke storage: ' . $e->getMessage()]);
            }
        } elseif ($request->file('avatar') && ! $request->file('avatar')->isValid()) {
            return back()->withInput()->withErrors(['avatar' => 'Gagal mengunggah foto: ' . $request->file('avatar')->getErrorMessage()]);
        }
        unset($validated['avatar']);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
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
