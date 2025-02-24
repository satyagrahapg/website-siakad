<?php

namespace App\Http\Controllers;

use App\Models\Tendik;
use App\Models\Pendidik;
use App\Models\Siswa;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AccountController extends Controller
{
    //MENAMPILKAN HALAMAN AKUN ~ SUPERADMIN
    public function index(Request $request){
        $accounts = User::all();

        return view('account.index', compact('accounts'));
    }    

    //MEMPERBARUI DATA AKUN
    public function update(Request $request, $id) {
        $account = User::findOrFail($id);
        // Update the user's basic info
        $account->name = $request->input('name');
        $account->email = $request->input('email');
        $account->username = $request->input('username');
        if ($request->input('password')) $account->password = Hash::make($request->input('password')); // hashed
        $account->save();
    
        // Update the user's role
        $account->syncRoles($request->input('roles')); // Use syncRoles to replace the current role with the new one
    
        return redirect()->route('account.index')->with('success', 'Akun berhasil diperbarui!');
    }

    //HAPUS DATA AKUN
    public function destroy($id){
        $account = User::findOrFail($id);
        $account->delete();

        return redirect()->route('account.index')->with('success', 'Akun berhasil dihapus!');
    }
}
