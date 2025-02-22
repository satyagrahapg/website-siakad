<?php

namespace App\Http\Controllers;

use App\Exports\TendikExport;
use App\Models\Tendik;
use Illuminate\Http\Request;
use App\Imports\TendikImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\User;
use Mail;

class TendikController extends Controller 
{
    public function index(Request $request)
    {
        // Ini untuk filter semester berapa yg mau dilist
        $jabatan = $request->input('jabatan');

        $tendik = Tendik::orderBy('created_at', 'desc')->get();
        return view('tendik.index', compact('tendik', 'jabatan'));
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|file|max:2048'
        ]);
        Excel::import(new TendikImport, $request->file('file'));

        return redirect()->route('tendik.index')->with('success', 'File berhasil diimport!');
    }

    public function export() {
        return Excel::download(new TendikExport, 'Tenaga Kependidikan.xlsx', );
    }

    public function create(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50|unique:tendiks,nip',
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

        Tendik::create($request->all());
        return redirect()->route('tendik.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $guru = Tendik::findOrFail($id);

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
        return redirect()->route('tendik.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id) {
        $guru = Tendik::findOrFail($id);
        $guru->delete();
        return redirect()->route('tendik.index')->with('success', 'Data berhasil dihapus!');
    }

    public function generateUser(Request $request, $adminId)
    {
        $tendik = Tendik::findOrFail($adminId);

        $request->validate([
            'email' => 'required|email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string',
            'roles' => 'required'
        ]);

        $user = User::create([
            'name' => $tendik->nama,
            'email' => $request->email,
            'username' => $request->username,
            'password' => $request->password
        ]);
        
        Mail::send('email.akun', ['username' => $request->username, 'password' => $request->password], function($message) use($request) {
            $message->to("$request->email");
            $message->subject('Akun SMP anda telah dibuat!');
        });  

        $user->syncRoles($request->roles);
        $tendik->id_user = $user->id;
        $tendik->save();
        return redirect()->route('tendik.index')->with('success', 'Berhasil membuat akun baru.');
    }
}
