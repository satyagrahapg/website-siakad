<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\AbsensiSiswa;
use App\Models\Penilaian;
use App\Models\PenilaianSiswa;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HalamanSiswaController extends Controller
{   
    public function absensi(Request $request)
    {
        $semesterId = $request->session()->get('semester_id');

        $semester = Semester::find($semesterId, ['start', 'end']);
        
        $user = Auth::user(); 
        $kelas = Kelas::join('kelas_siswa as ks', 'ks.kelas_id', '=', 'kelas.id')
            ->join('siswas as s', 's.id', '=', 'ks.siswa_id')
            ->join('gurus as g', 'g.id', '=', 'kelas.id_guru')
            ->where('s.id_user', $user->id)
            ->where('kelas.id_semester', $semesterId)
            ->where('kelas.kelas', '!=', 'Ekskul')
            ->orderBy('kelas.id_semester')
            ->select('kelas.rombongan_belajar', 'g.nama', 'g.gelar')
            ->first();

        $dataKelas = [];
        if ($kelas) {
            $gelar = explode('|', $kelas->gelar);
            $dataKelas['nama'] = $kelas->rombongan_belajar;
            $dataKelas['nama_guru'] = ($gelar[0] ? $gelar[0].' ' : '').$kelas->nama.$gelar[1];
        }
        
        $dataAbsensi = AbsensiSiswa::join('siswas as s', 's.id', '=', 'absensi_siswas.id_siswa')
            ->join('kelas_siswa as ks', 'ks.siswa_id', '=', 's.id')
            ->join('kelas as k', 'k.id', '=', 'ks.kelas_id')
            ->where('k.id_semester', $semesterId)
            ->where('s.id_user', $user->id)
            ->where('k.kelas', '!=', 'Ekskul')
            ->whereBetween('absensi_siswas.date', [$semester->start, $semester->end])
            ->select('absensi_siswas.*')
            ->orderBy('absensi_siswas.date', 'desc')
            ->get();

        $absensiSummary = AbsensiSiswa::join('siswas as s', 's.id', '=', 'absensi_siswas.id_siswa')
            ->join('kelas_siswa as ks', 'ks.siswa_id', '=', 's.id')
            ->join('kelas as k', 'k.id', '=', 'ks.kelas_id')
            ->where('k.id_semester', $semesterId)
            ->where('s.id_user', $user->id)
            ->where('k.kelas', '!=', 'Ekskul')
            ->whereBetween('absensi_siswas.date', [$semester->start, $semester->end])
            ->selectRaw('absensi_siswas.status, COUNT(absensi_siswas.status) as count')
            ->groupBy('absensi_siswas.status')
            ->get();
                
        $absensi = [];
        foreach ($absensiSummary as $record) {
            $absensi[$record->status] = $record->count;
        }

        return view('siswapage.absensi', compact('dataAbsensi', 'absensi', 'dataKelas'));
    }

    public function bukuNilaiSiswa(Request $request)
    {        
        $semesterId = $request->session()->get('semester_id');
        $siswa = Siswa::where('id_user', auth()->user()->id)->first();

        $mapels = Mapel::join('mapel_kelas', 'mapels.id', '=', 'mapel_kelas.mapel_id')
            ->join('kelas', 'mapel_kelas.kelas_id', '=', 'kelas.id')
            ->join('kelas_siswa', 'kelas.id', '=', 'kelas_siswa.kelas_id')
            ->join('siswas', 'kelas_siswa.siswa_id', '=', 'siswas.id')
            ->where('kelas.id_semester', $semesterId)
            ->where('siswas.id_user', auth()->user()->id)
            ->where('mapels.kelas', '!=', 'Ekskul')
            ->whereNotNull('mapels.guru_id')
            ->whereNull('mapels.parent')
            ->orWhere(function ($query) use ($siswa, $semesterId) {
                $query->whereNotNull('mapels.parent')
                    ->where('siswas.id_user', auth()->user()->id)
                    ->where('mapels.kelas', '!=', 'Ekskul')
                    ->whereNotNull('mapels.guru_id')
                    ->where('kelas.id_semester', $semesterId)
                    ->where('mapels.nama', 'like', '%' . $siswa->agama . '%');
            })
            ->select('mapels.id', 'mapels.nama', DB::raw("CONCAT(kelas.rombongan_belajar, ' - ', mapels.nama) as nama"))
            ->orderBy('mapels.nama')
            ->distinct()
            ->get();
        
        return view('siswapage.buku-nilai', compact('mapels', 'semesterId'));
    }
    
    // Helper method to fetch mapels the user is enrolled in
    // AJAX method to fetch student's scores based on selected mapel
    public function fetchBukuNilai(Request $request)
    {
        $user = Auth::user();
        $idMapel = $request->idMapel; // The selected mapel name from AJAX request
    
        // Fetch data for the student's scores based on selected mapel name
        $penilaians = $this->fetchPenilaianSiswa($user->id, $idMapel);
    
        // Calculate average scores for 'Tugas' and 'UH'
        $nilaiAkhirTugas = number_format($this->calculateAverageScore($penilaians, 'Tugas'), 2);
        $nilaiAkhirUH = number_format($this->calculateAverageScore($penilaians, 'UH'), 2);
    
        // Return the data as JSON for AJAX response
        return response()->json([
            'penilaians' => $penilaians,
            'nilaiAkhirTugas' => $nilaiAkhirTugas,
            'nilaiAkhirUH' => $nilaiAkhirUH,
        ]);
    }
    
    // Helper method to fetch student's scores for a given mapel
    private function fetchPenilaianSiswa($userId, $idMapel)
    {
        return PenilaianSiswa::join('penilaians as p', 'p.id', '=', 'penilaian_siswa.penilaian_id')
            ->join('siswas as s', 's.id', '=', 'penilaian_siswa.siswa_id')
            ->join('penilaian_t_p_s as pt', 'pt.penilaian_id', '=', 'p.id')
            ->join('t_p_s as t', 't.id', '=', 'pt.tp_id')
            ->join('c_p_s as cp', 'cp.id', '=', 't.cp_id')
            ->join('mapels as m', 'm.id', '=', 'cp.mapel_id')
            ->where('s.id_user', $userId)  // Filter by user ID
            ->where('m.id', $idMapel)  // Filter by mapel name
            ->select(
                'p.judul',
                'p.tanggal',
                'p.tipe',
                DB::raw("GROUP_CONCAT(CONCAT(cp.nomor, '.', t.nomor) ORDER BY cp.nomor ASC, t.nomor ASC SEPARATOR ', ') as nomor_tp"),
                'penilaian_siswa.nilai')
            ->groupBy('p.judul', 'p.tipe', 'penilaian_siswa.nilai', 'p.tanggal')
            ->orderBy('p.tanggal', 'desc')
            ->get();
    }
    
    // Helper method to calculate average scores based on tipe ('Tugas' or 'UH')
    private function calculateAverageScore($penilaians, $tipe)
    {
        return $penilaians
            ->where('tipe', $tipe)  // Filter records where tipe is either 'Tugas' or 'UH'
            ->avg('nilai');         // Calculate the average for the 'nilai' column
    }    
}
