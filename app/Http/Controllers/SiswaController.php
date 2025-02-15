<?php

namespace App\Http\Controllers;

use App\Exports\SiswaExport;
use App\Models\Siswa;
use Illuminate\Http\Request;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Colors\Rgb\Channels\Red;
use Mail;

class SiswaController extends Controller 
{
    public function index(){
        $siswas = Siswa::orderBy('nama', 'asc')
            ->get();
        return view('siswa.index', compact('siswas'));
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|max:2048'
        ]);
        Excel::import(new SiswaImport, $request->file('file'));
        
        return redirect()->route('siswa.index')->with('success', 'File berhasil diimport!');
    }

    public function export() {
        return Excel::download(new SiswaExport, 'siswa.xlsx', );
    }

    public function showImportForm()
    {
        return view('siswa.import');
    }

    public function generateUser(Request $request, $siswaId)
    {
        // // Find the `Siswa` record
        $siswa = Siswa::findOrFail($siswaId);

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // // Create a new user with the required attributes (no hashing)
        $user = User::create([
            'name' => $siswa->nama,
            'email' => $validated['email'],
            'username' => $siswa->nisn,
            'password' => Hash::make($validated['password']),
        ]);

        Mail::send('email.akun', ['username' => $siswa->nisn, 'password' => $request->password], function($message) use($request) {
            $message->to("$request->email");
            $message->subject('Akun SMP anda telah dibuat!');
        }); 

        // Automatically assign the 'Siswa' role to the new user
        $user->assignRole('Siswa');
        $siswa->id_user = $user->id;
        $siswa->save();

        return redirect()->route('siswa.index')->with('success', 'User berhasil dibuat dengan username ' . $user->username . ' dan password ' . $user->password);
    }

    public function delete($siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);
        $siswa->delete();

        return redirect()->route('siswa.index')->with('success', 'Data berhasil dihapus!');
    }
    
    public function store(Request $request)
    {
        // Validate that only `no_pendaftaran` is required and all other fields are nullable
        $validated = $request->validate([
            'nama' => 'nullable|string|max:255',
            'nis' => 'nullable|string|max:50|unique:siswas,nis,',
            'nisn' => 'nullable|string|max:50|unique:siswas,nisn,',
            'tanggal_lahir' => 'nullable|date|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|string|max:10',
            'agama' => 'nullable|string|max:50',
            'status_keluarga' => 'nullable|string|max:50',
            'anak_ke' => 'nullable|integer',
            'alamat_lengkap' => 'nullable|string|max:500',
            'telepon' => 'nullable|string|max:15',
            'asal_sekolah' => 'nullable|string|max:255',
            'tanggal_diterima' => 'nullable|date',
            'jalur_penerimaan' => 'nullable|string|max:255',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'nama_wali' => 'nullable|string|max:255',
            'pekerjaan_wali' => 'nullable|string|max:255',
            'angkatan' => 'nullable|integer',
        ]);
        
        Siswa::create($validated);

        return redirect()->route('siswa.index')->with('success', 'Data Siswa berhasil ditambahkan');
    }   
    public function update(Request $request, $siswaId)
    {
        // Validate that only `no_pendaftaran` is required and all other fields are nullable
        $validated = $request->validate([
            'nama' => 'nullable|string|max:255',
            // 'nis' => 'nullable|string|max:50|unique:siswas,nis,' . $siswaId,
            'nisn' => 'nullable|string|max:50|unique:siswas,nisn,' . $siswaId,
            'tanggal_lahir' => 'nullable|date|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|string|max:10',
            'agama' => 'nullable|string|max:50',
            'status_keluarga' => 'nullable|string|max:50',
            'anak_ke' => 'nullable|integer',
            'alamat' => 'nullable|string|max:500',
            'telepon' => 'nullable|string|max:15',
            'asal_sekolah' => 'nullable|string|max:255',
            'tanggal_diterima' => 'nullable|date',
            'jalur_penerimaan' => 'nullable|string|max:255',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'nama_wali' => 'nullable|string|max:255',
            'pekerjaan_wali' => 'nullable|string|max:255',
            'angkatan' => 'nullable|integer',
        ]);
        
        $siswa = Siswa::findOrFail($siswaId);
        $siswa->update($validated);

        return redirect()->route('siswa.index')->with('success', 'Data Siswa berhasil diubah');
    }   

}
