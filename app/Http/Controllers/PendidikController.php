<?php

namespace App\Http\Controllers;

use App\Exports\PendidikExport;
use App\Models\Pendidik;
use Illuminate\Http\Request;
use App\Imports\PendidikImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\User;
use Mail; 

class PendidikController extends Controller 
{
    public function index(){
        $pendidiks = Pendidik::with('user')
            ->orderBy('nama', 'asc')
            ->get();

        return view('pendidik.index', compact('pendidiks'));
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:xlsx'
        ], [
            'file.required' => 'File wajib diunggah.',
            'file.file'     => 'Pastikan yang diunggah adalah file.',
            'file.max'      => 'Ukuran file tidak boleh lebih dari 10 MB.',
            'file.mimes'    => 'Format file harus XLSX.'
        ]);
        Excel::import(new PendidikImport, $request->file('file'));

        return redirect()->route('pendidik.index')->with('success', 'File berhasil diimport!');
    }

    public function export() {
        return Excel::download(new PendidikExport, 'pendidik.xlsx');
    }

    public function create(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50|unique:pendidiks,nip',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'agama' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'pangkat_golongan' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:50',
        ]);
        $data = $request->all();
        $data['gelar'] = trim($data['gelar_depan']) . '|, ' . trim($data['gelar_belakang']);
        unset($data['gelar_depan']); // Hapus gelar_depan
        unset($data['gelar_belakang']); // Hapus gelar_belakang

        Pendidik::create($data);
        return redirect()->route('pendidik.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $guru = Pendidik::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50|unique:pendidiks,nip,'.$id,
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'agama' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'pangkat_golongan' => 'nullable|string|max:50',
            'pendidikan' => 'nullable|string|max:50',
        ]);

        $data = $request->all();
        $data['gelar'] = trim($data['gelar_depan']) . '|, ' . trim($data['gelar_belakang']);
        unset($data['gelar_depan']); // Hapus gelar_depan
        unset($data['gelar_belakang']); // Hapus gelar_belakang

        $guru->update($data);
        return redirect()->route('pendidik.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id) {
        $guru = Pendidik::findOrFail($id);
        $guru->delete();
        return redirect()->route('pendidik.index')->with('success', 'Data berhasil dihapus!');
    }

    public function generateUser(Request $request, $guruId)
    {   
        $guru = Pendidik::findOrFail($guruId);
        
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string',
            'email' => 'required|email',
            'roles' => 'required'
        ]);

        $user = User::create([
            'name' => $guru->nama,
            'username' => $request->username,
            'password' => $request->password,
            'email' => $request->email,
        ]);

        Mail::send('email.akun', ['username' => $request->username, 'password' => $request->password], function($message) use($request) {
            $message->to("$request->email");
            $message->subject('Akun SMP anda telah dibuat!');
        });

        $user->syncRoles($request->roles);
        $guru->id_user = $user->id;
        $guru->save();
        return redirect()->route('pendidik.index')->with('success', 'Berhasil membuat akun pendidik baru.');
    }

    public function editRole(Request $request, $guruId)
    {   
        $guru = Pendidik::findOrFail($guruId);
        
        $request->validate([
            'roles' => 'required'
        ]);

        $user = User::findOrFail($guru->id_user);

        $user->syncRoles($request->roles);
        return redirect()->route('pendidik.index')->with('success', 'Berhasil mengedit role.');
    }
}
