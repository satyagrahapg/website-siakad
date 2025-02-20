<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input login
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Temukan user berdasarkan username
        $user = User::where('username', $request->username)->first();

        // Check apakah user ada dan password sesuai (tanpa hashing)
        if ($user && Hash::check($request->password, $user->password)) {
            // Log in user secara manual ke dalam session Auth
            Auth::login($user);
            $user_roles = Auth::user()->getRoleNames()->toArray();
            if (count($user_roles) != 1) {
                return redirect()->route('role');
            } else {
                session(['active_role' => $user_roles[0]]);
                return redirect()->route('home');
            }
        }

        // Jika username atau password salah
        return back()->withErrors(['invalid_credentials' => 'Login gagal, mohon periksa kembali!']);
    }

    //Menampilkan Halaman Pilih Role
    public function select_role()
    {        
        $user_roles = Auth::user()->getRoleNames()->toArray();
        $role_order = ['Super Admin', 'Admin', 'Guru', 'Wali Kelas'];
        $role_order_map = array_flip($role_order);
        usort($user_roles, function ($a, $b) use ($role_order_map) {
            return $role_order_map[$a] - $role_order_map[$b];
        });
        if (count($user_roles) != 1) { //Kalo dia punya lebih dari 1 role maka muncul halaman pilih role
            return view('auth.role', [
                'roles' => $user_roles,
            ]);
        } else {
            session(['active_role' => $user_roles[0]]); //Kalo dia role cuman 1 langsung masuk sesuai rolenya
            return redirect()->route('home');
        }
    }

    public function set_role(Request $request)
    {
        $user_roles = Auth::user()->getRoleNames()->toArray();
        if (in_array($request->role, $user_roles)) {
            session(['active_role' => $request->role]);
            return redirect()->route('home');
        } else return redirect()->route('role');
    }

    //Keluar dari akun
    public function logout()
    {
        Auth::logout(); // Logout pengguna dari session auth
        session()->flush();
        return redirect()->route('login');
    }
}
