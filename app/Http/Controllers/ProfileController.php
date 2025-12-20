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
        $user = $request->user();
        
        // Load relasi karyawan jika ada NIP
        if ($user->nip) {
            $user->load('karyawan');
        }
        
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        
        // Update email di tabel users
        $user->email = $validated['email'];
        
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        $user->save();
        
        // Update data karyawan jika user adalah karyawan (punya NIP)
        if ($user->nip && $user->karyawan) {
            $karyawanData = [];
            
            if (isset($validated['no_hp'])) {
                $karyawanData['no_hp'] = $validated['no_hp'];
            }
            if (isset($validated['tanggal_lahir'])) {
                $karyawanData['tanggal_lahir'] = $validated['tanggal_lahir'];
            }
            if (isset($validated['alergi'])) {
                $karyawanData['alergi'] = $validated['alergi'];
            }
            if (isset($validated['alamat'])) {
                $karyawanData['alamat'] = $validated['alamat'];
            }
            
            // Update email di tabel karyawan juga
            $karyawanData['email'] = $validated['email'];
            
            if (!empty($karyawanData)) {
                $user->karyawan()->update($karyawanData);
            }
        }
        
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
