<?php

namespace App\Http\Controllers;

use App\Models\Tendik;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function profile(Request $request) {
        $user = auth()->user();

        if ($user->hasRole('Guru') || $user->hasRole('Wali Kelas')) {
            $data = Guru::where('id_user', $user->id)->first();
        } else if ($user->hasRole('Admin') || $user->hasRole('Super Admin')) {
            $data = Tendik::where('id_user', $user->id)->first();
        } else if ($user->hasRole('Siswa')) {
            $data = Siswa::where('id_user', $user->id)->first();
        }

        if (isset($data)) {
            return view('user.profile', ['data' => $data]);
        } else {
            return view('user.profile');
        }
    }

    public function update_picture(Request $request) {
        $user = auth()->user();
        $request->validate([
            'image' => 'required|image|mimes:png,jpeg,jpg|max:5120', // Maximum size 5 MB
        ]);
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
    
            $imageData = file_get_contents($image->getRealPath());
    
            User::where('id', $user->id)->update(['picture' => $imageData]);
        }
    
        return redirect()->route('profile')->with('success', 'Gambar berhasil diperbarui!');
    }

    public function update_password(Request $request) {
        $new_password = $request->input('new_password');

        $account = User::findOrFail(auth()->user()->id);
        $account->password = Hash::make($new_password);
        $account->save();

        Auth::logout(); // Logout pengguna dari session auth
        return redirect()->route('login');
    }
}
