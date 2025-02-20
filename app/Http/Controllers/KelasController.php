<?php

namespace App\Http\Controllers;

use App\Exports\KelasExport;
use App\Http\Requests\AutoAddStudentRequest;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\Mapel;
use App\Models\PenilaianEkskul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KelasController extends Controller
{
    // Show form to create a new class
    public function create()
    {
        // Get all Wali Kelas
        $walikelas = Guru::whereHas('user.roles', function ($query) {
            $query->where('name', 'Wali Kelas'); // Check if the role is 'Wali Kelas'
        })->get(); // Fetch the full objects so we can use IDs

        // Get all semesters
        $semesters = Semester::all();

        return view('kelas.create', compact('walikelas', 'semesters'));
    }

    // Store a new class
    public function store(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string',
            'rombongan_belajar' => 'required|string|max:255',
            'id_guru' => 'required|exists:gurus,id',
            'id_semester' => 'required|exists:semesters,id',
        ]);

        // Create the class
        $kelas = Kelas::create($request->only(['kelas', 'rombongan_belajar', 'id_guru', 'id_semester']));

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan!');
    }

    // Show classes
    public function index(Request $request)
    {
        // Retrieve the filter inputs
        $semesterId = $request->input('semester_id');
        $filterKelas = $request->input('kelas');

        // Apply filters for Kelas based on semester and class name
        $kelas = Kelas::with(['guru', 'semester', 'siswas'])
            ->when($semesterId, function ($query, $semesterId) {
                $query->where('id_semester', $semesterId);
            })
            ->when($filterKelas, function ($query, $filterKelas) {
                $query->where('kelas', $filterKelas);
            })
            ->get();

        $gurus = Guru::all();

        // Only retrieve teachers who are assigned as 'Wali Kelas'
        $walikelas = Guru::whereHas('user.roles', function ($query) {
            $query->where('name', 'Wali Kelas');
        })->get();

        // Get all semesters and class names for the filter options
        $semesters = Semester::all();
        $listKelas = Kelas::select('kelas')
            ->distinct()
            ->orderBy('kelas', 'ASC')
            ->get();


        // Get all students
        $siswa = Siswa::all();

        return view('kelas.index', compact('listKelas', 'kelas', 'semesters', 'siswa', 'walikelas', 'semesterId', 'gurus', 'filterKelas'));
    }


    public function hapusKelas($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $kelas->delete();

        if ($kelas->kelas === 'Ekskul') {
            $mapel = Mapel::where('nama', $kelas->rombongan_belajar)
                ->where('guru_id', $kelas->id_guru)
                ->where('semester_id', $kelas->id_semester)
                ->first();

            if ($mapel) {
                $mapel->delete();
            }
        }

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus!');
    }

    public function bukaKelas($kelasId, Request $request)
    {
        $selectedAngkatan = $request->input('angkatan');

        $angkatan = Siswa::select('angkatan')
            ->distinct()
            ->pluck('angkatan');

        // Load the class along with its students
        $kelas = Kelas::with('siswas:id,nama,nisn,angkatan,jenis_kelamin,agama')->findOrFail($kelasId);

        // Get the semester ID of the current class
        $semesterId = $kelas->id_semester;

        // Get the rombongan belajar for ekskul siswa assignment
        $rombonganBelajar = $kelas->rombongan_belajar;

        // Retrieve all students
        $allSiswa = Siswa::select('*')
            ->where('angkatan', $selectedAngkatan)
            ->get();

        $allSiswaEkskul = Siswa::select('*')
            ->get();

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

        $assignedEkskulSiswaIds = DB::table('kelas_siswa')
            ->join('kelas', 'kelas_siswa.kelas_id', '=', 'kelas.id')
            ->where('kelas.id_semester', $semesterId)
            ->where('kelas.rombongan_belajar', $rombonganBelajar)
            ->pluck('kelas_siswa.siswa_id')
            ->toArray();

        $availableEkskulSiswa = $allSiswaEkskul->reject(function ($siswa) use ($assignedEkskulSiswaIds) {
            return in_array($siswa->id, $assignedEkskulSiswaIds);
        });

        if ($kelas->kelas === 'Ekskul') {
            // Pass the data to the view
            return view('kelas.buka-ekskul', [
                'kelas' => $kelas,
                'siswas' => $availableEkskulSiswa,
                'daftar_siswa' => $kelas->siswas
            ]);
        } else {
            // Get all kelas data
            $semesters = Semester::all();

            // Pass the data to the view
            return view('kelas.buka', [
                'kelas' => $kelas,
                'siswas' => $availableSiswa,
                'daftar_siswa' => $kelas->siswas,
                'angkatan' => $angkatan,
                'semesters' => $semesters
            ]);
        }
    }

    public function autoAddStudents($kelasId, AutoAddStudentRequest $request)
    {  
        // This method is made to automatically add students to a class based on the remaining slots
        // This method expects a POST request with the following fields
        // - angkatan : the angkatan of the students to be added
        // - jumlahSiswa : the number of students to be added
        // - jumlahSiswaLaki : the number of male students to be added
        // - jumlahSiswaPerempuan : the number of female students to be added


        // Load the class along with its students
        $kelas = Kelas::with('siswas:id,nama,nisn,jenis_kelamin')->findOrFail($kelasId);

        // Check the current number of students in the class, and if it is already full
        $currentSiswaCount = $kelas->siswas()->count();
        $classIsFull = $currentSiswaCount >= request('jumlahSiswa');
        

        // Check if male and female student counts are already at the limit
        $currentMaleCount = $kelas->siswas()->where('jenis_kelamin','Laki-laki')->count();
        $currentFemaleCount = $kelas->siswas()->where('jenis_kelamin', 'Perempuan')->count();
        $maleSlots = request('jumlahSiswaLaki') - $currentMaleCount;
        $femaleSlots = request('jumlahSiswaPerempuan') - $currentFemaleCount;

        // If the class is already full, return an error message
        if ($classIsFull) {
            return redirect()->back()->with('error', 'Kelas sudah penuh. Jumlah siswa yang ada di kelas melebihi jumlah siswa yang dimasukkan. Jumlah Siswa sekarang: ' . $currentSiswaCount . '. Jumlah siswa yang dimasukkan: ' . request('jumlahSiswa'));
        }

        // Get the semester ID of the current class
        $semesterId = $kelas->id_semester;
        
        // Get available students of the specified angkatan who are not already assigned this semester
        $availableSiswa = Siswa::inRandomOrder()->where('angkatan', request('angkatan'))->whereDoesntHave('kelases', function ($query) use ($semesterId) {
            $query->where('id_semester', $semesterId);
        })->get();

        $maleStudents = collect();
        $femaleStudents = collect();

        // If male slots are available, take a number of students according to the slots
        
        if ($maleSlots) {
            $maleStudents = $availableSiswa->where('jenis_kelamin', 'Laki-laki')->take($maleSlots);
        } 

        // If female slots are available, take a number of students according to the slots
        if ($femaleSlots) {
            $femaleStudents = $availableSiswa->where('jenis_kelamin', 'Perempuan')->take($femaleSlots);
        }

        // Combine male and female students
        $selectedStudents = $maleStudents->merge($femaleStudents)->sortBy('nama');

        // Attach students to the class without detaching existing students
        $kelas->siswas()->syncWithoutDetaching($selectedStudents->pluck('id')->toArray());

        return redirect()->back()->with('success', $maleStudents->count() . ' siswa Laki-laki dan '. $femaleStudents->count() . ' siswa Perempuan berhasil ditambahkan ke kelas.' . ' Jumlah siswa sekarang: ' . $kelas->siswas()->count());
    }


    // Add a student to a class
    public function addStudentToClass(Request $request, $kelasId)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswas,id',
        ]);

        // Find the class
        $kelas = Kelas::findOrFail($kelasId);

        // Check if the class already has 30 students
        if ($kelas->siswas()->count() >= 30) {
            return redirect()->back()->with('error', 'Kelas sudah penuh');
        }

        // Attach the student to the class using the pivot table
        $kelas->siswas()->syncWithoutDetaching($request->id_siswa);

        if ($kelas->kelas === 'Ekskul') {
            $penilaian = PenilaianEkskul::create([
                'nilai' => null,
                'siswa_id' => $request->id_siswa,
                'kelas_id' => $kelasId
            ]);
        }
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

        // Find and update the Kelas record
        $kelas = Kelas::findOrFail($kelasId);

        if ($kelas->kelas === 'Ekskul') {
            $mapel = Mapel::where('nama', $kelas->rombongan_belajar)
                ->where('guru_id', $kelas->id_guru)
                ->where('semester_id', $kelas->id_semester)
                ->first();

            // If a matching Mapel is found, update its fields to match Kelas
            if ($mapel) {
                $mapel->kelas = 'Ekskul';
                $mapel->nama = $request->input('rombongan_belajar');
                $mapel->guru_id = $request->input('id_guru');
                $mapel->semester_id = $request->input('id_semester');
                $mapel->save();
            }
        }

        $kelas->id_guru = $request->input('id_guru');
        $kelas->id_semester = $request->input('id_semester');
        $kelas->kelas = $request->input('kelas');
        $kelas->rombongan_belajar = $request->input('rombongan_belajar');
        // Conditional update for the Mapel model if Kelas is of type 'Ekskul'


        $kelas->save();

        // Redirect back to the index with a success message
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui!');
    }

    public function storeEkskul(Request $request)
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

        return redirect()->route('kelas.index')->with('success', 'Ekstrakurikuler berhasil ditambahkan!');
    }

    public function exportKelas(Kelas $kelasId) {
        // dd($kelasId->id);
        return (new KelasExport($kelasId->id))->download( 'siswa ' . $kelasId->rombongan_belajar . '.xlsx');
    }

    public function deleteAssignedSiswa($kelasId, $siswaId)
    {
        // Assuming the relationship is defined as 'kelases' on the Siswa model for the many-to-many relation
        $siswa = Siswa::find($siswaId);

        if (!$siswa) {
            return redirect()->back()->with('error', 'Siswa not found');
        }

        // Detach the student (siswa) from the class (kelas)
        $siswa->kelases()->detach($kelasId);

        return redirect()->back()->with('success', 'Peserta Didik berhasil dihapus!');
    }

    public function getKelas(Request $request)
    {
        $semesterId = $request->input('semesterId');
        $kelas = Kelas::where('id_semester', $semesterId)->get();

        return response()->json($kelas);
    }

    public function importSiswaFromKelas(Request $request, Kelas $kelasId)
    {
        $request->validate([
            'semester' => 'required|exists:semesters,id',
            'kelas' => 'required|exists:kelas,id',
        ]);

        $siswas = Siswa::whereHas('kelases', function ($query) use($request) {
            $query->where('kelas_id', $request->kelas);
        })->get();

        $kelasId->siswas()->syncWithoutDetaching($siswas->pluck('id')->toArray());

        return redirect()->back()->with('success', 'Data siswa berhasil diimport');
    }
}
