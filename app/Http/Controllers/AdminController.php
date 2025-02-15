<?php

namespace App\Http\Controllers;

use App\Exports\AdminExport;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Imports\AdminImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\User;
use Mail;

class AdminController extends Controller 
{
    public function index(Request $request)
    {
        // Ini untuk filter semester berapa yg mau dilist
        $jabatan = $request->input('jabatan');

        $admin = Admin::orderBy('created_at', 'desc')->get();
        return view('admin.index', compact('admin', 'jabatan'));
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|file|max:2048'
        ]);
        Excel::import(new AdminImport, $request->file('file'));

        return redirect()->route('admin.index')->with('success', 'File berhasil diimport!');
    }

    public function export() {
        return Excel::download(new AdminExport, 'Tenaga Kependidikan.xlsx', );
    }

    public function create(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50|unique:admins,nip',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'agama' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'pangkat_golongan' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:50',
        ]);

        Admin::create($request->all());
        return redirect()->route('admin.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $guru = Admin::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50|unique:gurus,nip,'.$id,
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'agama' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'pangkat_golongan' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:50',
        ]);

        $guru->update($request->all());
        return redirect()->route('admin.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id) {
        $guru = Admin::findOrFail($id);
        $guru->delete();
        return redirect()->route('admin.index')->with('success', 'Data berhasil dihapus!');
    }

    public function generateUser(Request $request, $adminId)
    {
        $admin = Admin::findOrFail($adminId);

        $request->validate([
            'email' => 'required|email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string',
        ]);

        $user = User::create([
            'name' => $admin->nama,
            'email' => $request->email,
            'username' => $request->username,
            'password' => $request->password
        ]);
        
        Mail::send('email.akun', ['username' => $request->username, 'password' => $request->password], function($message) use($request) {
            $message->to("$request->email");
            $message->subject('Akun SMP anda telah dibuat!');
        });  

        $user->assignRole('Admin');
        $admin->id_user = $user->id;
        $admin->save();
        return redirect()->route('admin.index')->with('success', 'Berhasil membuat akun baru.');
    }
}
