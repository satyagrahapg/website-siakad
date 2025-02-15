<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\PenilaianEkskul;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class EkstrakulikulerController extends Controller
{
    public function index(Request $request)
    {
        // Ini untuk filter semester berapa yg mau dilist
        $semesterId = $request->input('semester_id');
        
        $kelas = Kelas::with(['guru', 'semester', 'siswas'])
            ->when($semesterId, function ($query, $semesterId) {
                    $query->where('id_semester', $semesterId);
                })
            ->get();
    
        $walikelas = Guru::whereHas('user.roles', function ($query) {
            $query->where('name', 'Wali Kelas'); // Check if the role is 'Wali Kelas'
        })->get();
    
        // Get all semesters for the filter options
        $semesters = Semester::all();
    
        // Get all students
        $siswa = Siswa::all();
    
        return view('kelas.ekstrakulikuler', compact('kelas', 'semesters', 'siswa', 'walikelas', 'semesterId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rombongan_belajar' => 'required|string|max:255',
            'id_guru' => 'required|exists:gurus,id',
            'id_semester' => 'required|exists:semesters,id',
        ]);

        $kelas = Kelas::create([
            'kelas' => 'Ekskul',
            'rombongan_belajar' => $request->rombongan_belajar,
            'id_guru' => $request->id_guru,
            'id_semester' => $request->id_semester
        ]);
        
        $mapel = Mapel::create([
            'nama' => $request->rombongan_belajar,
            'kelas' => 'Ekskul',
            'guru_id' => $request->id_guru,
            'semester_id' => $request->id_semester,

        ]);

        $mapel->kelas()->syncWithoutDetaching($kelas->id);

        return redirect()->route('kelas.ekstrakulikuler')->with('success', 'Ekstrakulikuler berhasil ditambahkan!');
    }

    public function hapusEkskul($ekskulId)
    {
        $kelas = Kelas::findOrFail($ekskulId);
        $kelas->delete();

        Mapel::where('kelas_id', $ekskulId)->delete();

        return redirect()->route('ekstrakulikuler.index')->with('success', 'Ekstrakurikuler berhasil dihapus!');
    }

    public function bukaKelas($ekskulId)
    {
        // Load the class along with its students
        $kelas = Kelas::with('siswas:id,nama,nisn')->findOrFail($ekskulId);

        // Get the semester ID of the current class
        $semesterId = $kelas->id_semester;

        // Retrieve all students
        $allSiswa = Siswa::select('id', 'nama', 'nisn')->get();

        // Filter out students who are already in a class for this semester
        $assignedSiswaIds = DB::table('kelas_siswa')
            ->join('kelas', 'kelas_siswa.kelas_id', '=', 'kelas.id')
            ->where('kelas.id_semester', $semesterId)
            ->pluck('kelas_siswa.siswa_id')
            ->toArray();

        // Get only students not already assigned in this semester
        $availableSiswa = $allSiswa->reject(function ($siswa) use ($assignedSiswaIds) {
            return in_array($siswa->id, $assignedSiswaIds);
        });

        $siswas = $kelas->siswas->map(function ($siswa) {
            return [
                'nama' => $siswa->nama,
                'nisn' => $siswa->nisn,
            ];
        });

        // Pass the data to the view
        return view('kelas.buka', [
            'kelas' => $kelas,
            'siswas' => $availableSiswa,
            'daftar_siswa' => $siswas,
            'ekskulId' => $ekskulId
        ]);
    }

    // Add a student to a class
    public function addStudentToClass(Request $request, $kelasId, $ekskulId)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswas,id',
        ]);

        // Find the class
        $kelas = Kelas::findOrFail($kelasId);

        // Check if the class already has 30 students
        if ($kelas->siswas()->count() >= 30) {
            return redirect()->back()->with('error', 'Kelas sudah penuh. Maksimal 30 siswa.');
        }

        // Attach the student to the class using the pivot table
        $kelas->siswas()->syncWithoutDetaching($request->id_siswa);

        $penilaian = PenilaianEkskul::create([
            'nilai' => null,
            'mapel_id' => $ekskulId,
            'siswa_id' => $request->id_siswa
        ]);

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function update(Request $request, $kelasId)
    {
        $request->validate([
            'id_guru' => 'required|exists:gurus,id',
            'id_semester' => 'required|exists:semesters,id',
            'kelas' => 'required|string|max:255',
            'rombongan_belajar' => 'required|string'
        ]);
    
        $kelas = Kelas::findOrFail($kelasId);
        $kelas->id_guru = $request->input('id_guru');  
        $kelas->id_semester = $request->input('id_semester');  
        $kelas->kelas = $request->input('kelas');
        $kelas->rombongan_belajar = $request->input('rombongan_belajar');
        $kelas->save();
    
        return redirect()->route('kelas.ekstrakulikuler')->with('success', 'Kelas berhasil diperbarui!');
    }
}
