<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\PenilaianSiswa;
use App\Models\AbsensiSiswa;
use App\Models\Penilaian;
use App\Models\P5BK;
use App\Models\Mapel;
use App\Models\MapelKelas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class PesertaDidikController extends Controller
{
    public function index($semesterId)
    {
        $user = Auth::user();

        $pesertadidiks = Siswa::join('kelas_siswa', 'kelas_siswa.siswa_id', '=', 'siswas.id')
        ->join('kelas', 'kelas.id', '=', 'kelas_siswa.kelas_id')
        ->join('semesters', 'semesters.id', '=', 'kelas.id_semester')
        ->join('gurus', 'gurus.id', '=', 'kelas.id_guru')
        ->join('users', 'users.id', '=', 'gurus.id_user')
        ->where('users.id', $user->id)
        ->where('semesters.id', $semesterId)
        ->where('kelas.kelas', '!=', 'Ekskul')
        ->select('siswas.*')
        ->get();

        return view('walikelas.index', compact('pesertadidiks'));
    }

    // Display student attendance for a specific semester
    public function attendanceIndex($semesterId)
    {
        $user = Auth::user();

        // Get students for the specified semester
        $pesertadidiks = Siswa::join('kelas_siswa', 'kelas_siswa.siswa_id', '=', 'siswas.id')
            ->join('kelas', 'kelas.id', '=', 'kelas_siswa.kelas_id')
            ->join('semesters', 'semesters.id', '=', 'kelas.id_semester')
            ->join('gurus', 'gurus.id', '=', 'kelas.id_guru')
            ->join('users', 'users.id', '=', 'gurus.id_user')
            ->where('users.id', $user->id)
            ->where('semesters.id', $semesterId)
            ->where('kelas.kelas', '!=', 'Ekskul')
            ->select('siswas.id', 'siswas.nama', 'siswas.nisn')
            ->orderBy('siswas.nama', 'asc')
            ->get();

        // Get attendance records for the current date
        $attendanceRecords = AbsensiSiswa::whereIn('id_siswa', $pesertadidiks->pluck('id'))
            ->whereDate('date', Carbon::today()) // Adjust to any date as necessary
            ->get();

        // dd($pesertadidiks);
        // Map attendance status by student ID
        $attendance = [];
        $last_update = [];
        foreach ($attendanceRecords as $record) {
            $attendance[$record->id_siswa] = $record->status;
            $last_update[$record->id_siswa] = Carbon::parse($record->updated_at)->addHours(7)->format('Y-m-d H:i:s');
        }

        return view('walikelas.attendance', compact('pesertadidiks', 'attendance', 'last_update', 'semesterId'));
    }

    // Store the attendance for students
    public function storeAttendance(Request $request)
    {
        $request->validate([
            'attendance' => 'required|array',
            'date' => 'required|date',
        ]);

        foreach ($request->attendance as $siswaId => $status) {
            // Check if attendance already exists for the given student and date
            $attendance = AbsensiSiswa::updateOrCreate(
                ['id_siswa' => $siswaId, 'date' => $request->date],
                ['status' => $status]
            );
        }

        return redirect()->route('pesertadidik.attendanceIndex', ['semesterId' => $request->semester_id])
            ->with('success', 'Presensi berhasil disimpan!');
    }

    public function fetchAttendance(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|integer',
            'date' => 'required|date',
        ]);

        $user = Auth::user();

        // Get students for the specified semester
        $pesertadidiks = Siswa::join('kelas_siswa', 'kelas_siswa.siswa_id', '=', 'siswas.id')
            ->join('kelas', 'kelas.id', '=', 'kelas_siswa.kelas_id')
            ->join('semesters', 'semesters.id', '=', 'kelas.id_semester')
            ->join('gurus', 'gurus.id', '=', 'kelas.id_guru')
            ->join('users', 'users.id', '=', 'gurus.id_user')
            ->where('users.id', $user->id)
            ->where('semesters.id', $request->semester_id)
            ->where('kelas.kelas', '!=', 'Ekskul')
            ->select('siswas.id', 'siswas.nama', 'siswas.nisn')
            ->orderBy('siswas.nama', 'asc')
            ->get();

        // Get attendance records for the selected date
        $attendanceRecords = AbsensiSiswa::whereIn('id_siswa', $pesertadidiks->pluck('id'))
            ->whereDate('date', $request->date)
            ->get();

        // Map attendance status by student ID
        $attendance = [];
        $last_update = [];
        foreach ($attendanceRecords as $record) {
            $attendance[$record->id_siswa] = $record->status;
            $last_update[$record->id_siswa] = Carbon::parse($record->updated_at)->addHours(7)->format('Y-m-d H:i:s');
        }

        return response()->json([
            'success' => true,
            'students' => $pesertadidiks,
            'attendance' => $attendance,
            'last_update' => $last_update,
        ]);
    }


    public function saveAttendanceAjax(Request $request)
    {
        $request->validate([
            'attendance' => 'required|array',
            'date' => 'required|date',
            'semester_id' => 'required|integer',
        ]);
    
        foreach ($request->attendance as $siswaId => $status) {
            AbsensiSiswa::updateOrCreate(
                ['id_siswa' => $siswaId, 'date' => $request->date],
                ['status' => $status]
            );
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil disimpan!',
        ]);
    }

    public function removeAttendanceAjax(Request $request)
    {
        $request->validate([
            'attendance' => 'required|array',
            'date' => 'required|date',
            'semester_id' => 'required|integer',
        ]);

        foreach ($request->attendance as $siswaId) {
            AbsensiSiswa::where([
                'id_siswa' => $siswaId,
                'date' => $request->date
            ])->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil dihapus!',
        ]);
    }


    public function bukaLegerNilai($kelasId, $semesterId)
    {
        $user = Auth::user(); // Get the currently logged-in user
    
        $mapelIds = MapelKelas::join('mapels', 'mapels.id', '=', 'mapel_kelas.mapel_id')
            ->where('mapel_kelas.kelas_id', $kelasId)
            ->orderBy('mapels.nama')
            ->pluck('mapel_kelas.mapel_id');

        $datas['all'] = collect();
        $datas['sts'] = collect();
        $datas['sas'] = collect();

        foreach ($mapelIds as $mapelId) {
            $query = PenilaianSiswa::join('penilaians as b', 'b.id', '=', 'penilaian_siswa.penilaian_id')
                ->join('siswas as c', 'c.id', '=', 'penilaian_siswa.siswa_id')
                ->join('penilaian_t_p_s as j', 'j.penilaian_id', '=', 'b.id')
                ->join('t_p_s as d', 'd.id', '=', 'j.tp_id')
                ->join('c_p_s as e', 'e.id', '=', 'd.cp_id')
                ->join('mapel_kelas as f', 'f.mapel_id', '=', 'e.mapel_id')
                ->join('mapels as z', 'z.id', '=', 'f.mapel_id')
                ->join('kelas as g', 'g.id', '=', 'f.kelas_id')
                ->join('gurus as h', 'h.id', '=', 'g.id_guru')
                ->join('users as i', 'i.id', '=', 'h.id_user')
                ->where('i.id', $user->id)
                
                ->where('g.id', $kelasId)
                ->where('z.id', $mapelId) // Filter hanya untuk mapel saat ini
                ->where('z.semester_id', request()->session()->get('semester_id'))
                ->select(
                    'c.id as siswa_id',
                    'c.nama as siswa_name',
                    'c.nisn as nisn',
                    'c.agama',
                    'g.rombongan_belajar as kelas',
                    'z.nama as mapel_name',
                    'z.parent',
                    DB::raw("AVG(CASE WHEN b.tipe = 'Tugas' THEN penilaian_siswa.nilai_akhir END) AS avg_tugas"),
                    DB::raw("AVG(CASE WHEN b.tipe = 'UH' THEN penilaian_siswa.nilai_akhir END) AS avg_uh"),
                    DB::raw("AVG(CASE WHEN b.tipe = 'SAS' THEN penilaian_siswa.nilai_akhir END) AS avg_sas"),
                    DB::raw("AVG(CASE WHEN b.tipe = 'STS' THEN penilaian_siswa.nilai_akhir END) AS avg_sts"),
                    DB::raw("MIN(b.tanggal) AS first_tanggal"),
                    DB::raw("MAX(b.tanggal) AS last_tanggal"),
                    DB::raw("COUNT(*) as count")
                )
                ->groupBy('c.id', 'c.nama', 'z.nama', 'g.rombongan_belajar', 'c.nisn', 'c.agama', 'z.parent')
                ->orderBy('siswa_name', 'asc');
                // ->orderBy('mapel_name', 'asc');

            $all = $query->get();
            $sts = (clone $query)->where(function ($query) use ($mapelId) {
                $query->where('b.tipe', '=', 'STS')
                    ->orWhere('b.tanggal', '<', function ($subquery) use ($mapelId) {
                        $subquery->selectRaw('MIN(penilaians.tanggal)')
                            ->from('penilaians')
                            ->join('mapel_kelas as mk', 'mk.id', '=', 'penilaians.mapel_kelas_id')
                            ->join('mapels as m', 'm.id', '=', 'mk.mapel_id')
                            ->where('m.id', $mapelId)
                            ->where('penilaians.tipe', '=', 'STS');
                    });
            })->get();
            $sas = (clone $query)->where(function ($query) use ($mapelId) {
                $query->where('b.tipe', '=', 'SAS')
                    ->orWhere('b.tanggal', '<', function ($subquery) use ($mapelId) {
                        $subquery->selectRaw('MIN(penilaians.tanggal)')
                            ->from('penilaians')
                            ->join('mapel_kelas as mk', 'mk.id', '=', 'penilaians.mapel_kelas_id')
                            ->join('mapels as m', 'm.id', '=', 'mk.mapel_id')
                            ->where('m.id', $mapelId)
                            ->where('penilaians.tipe', '=', 'SAS');
                    });
            })->get();

            $datas['all'] = $datas['all']->merge($all);
            $datas['sts'] = $datas['sts']->merge($sts);
            $datas['sas'] = $datas['sas']->merge($sas);
        }
    
        $parents = Mapel::whereNull('guru_id')->pluck('nama', 'id');

        // Transform the data
        $result['all'] = [];
        $result['sts'] = [];
        $result['sas'] = [];
        foreach($datas as $key_data => $data_collection){
            foreach ($data_collection as $data) {
                $hasil_akhir = collect([ 
                    $data->avg_tugas, 
                    $data->avg_uh, 
                    $data->avg_sas, 
                    $data->avg_sts,
                ])->filter()->avg(); // Hitung rata-rata hanya dari nilai yang tidak null

                if (!isset($result[$key_data][$data->siswa_id])) {
                    $result[$key_data][$data->siswa_id] = [
                        'nama' => $data->siswa_name,
                        'nisn' => $data->nisn,
                        'kelas' => $data->kelas,
                        'agama' => $data->agama,
                        'first_tanggal' => $data->first_tanggal,
                        'last_tanggal' => $data->last_tanggal,
                        // 'count' => $data->count ?? 0
                    ];
                } else {
                    // Jika sudah ada, perbarui first_tanggal dan last_tanggal
                    $result[$key_data][$data->siswa_id]['first_tanggal'] = min($result[$key_data][$data->siswa_id]['first_tanggal'], $data->first_tanggal);
                    $result[$key_data][$data->siswa_id]['last_tanggal'] = max($result[$key_data][$data->siswa_id]['last_tanggal'], $data->last_tanggal);
                    // Tambahkan count
                    // $result[$key_data][$data->siswa_id]['count'] += $data->count ?? 0;
                }

                if ($data->parent && isset($parents[$data->parent])) {
                    $result[$key_data][$data->siswa_id][$parents[$data->parent]] = number_format(round($hasil_akhir, 2), 2);
                } else {
                    $result[$key_data][$data->siswa_id][$data->mapel_name] = number_format(round($hasil_akhir, 2), 2);
                }
            }

            // Gabungkan first_tanggal dan last_tanggal dalam format "YYYY-MM-DD - YYYY-MM-DD"
            foreach ($result[$key_data] as &$res) {
                $res['tanggal'] = trim(($res['first_tanggal'] ?? '') . ' - ' . ($res['last_tanggal'] ?? ''), ' -');
                unset($res['first_tanggal'], $res['last_tanggal']); // Hapus setelah digabung
            }
            unset($res); // Hapus reference untuk keamanan
        }

        // dd($result);

        $subjects = collect($result['sts'])->flatMap(function ($row) {
            return array_keys((array)$row);
        })->unique()->filter(fn($key) => !in_array($key, ['nama', 'kelas', 'nisn', 'tanggal', 'agama']));

        $transposed = [];
        foreach (array_keys($result) as $key) {
            foreach ($result[$key] as $index => $value) {
                $transposed[$index][$key] = $value;
            }
        }

        return view('walikelas.legerNilai', ['data' => $transposed , 'subjects' => $subjects, 'semesterId' => $semesterId]);
    }    

    public function generateRapotPDF(Request $request)
    {
        $user = Auth::user();

        $semesterId = request()->session()->get('semester_id');

        $data = $request->all();

        $subjects = $data['subjects'] ?? [];
        $studentName = $data['student_name'] ?? 'Unknown Student';
        $agama = $data['student_religion'] ?? 'Unknown Religion';
    
        $tipe = $data['tipe_penilaian'];
        if ($tipe === 'sts') {
            $tanggal_sts = explode(" - ", $data['tanggal_sts'])[1];

            $komentar = $data['komentar'] ?? '';
            
            $parents =Mapel::where('semester_id', $semesterId)->whereNull('guru_id')->get();
            $parentWithChildren = [];
            foreach ($parents as $parent) {
                $children = Mapel::where('semester_id', $semesterId)
                    ->where('parent', $parent->id)
                    ->pluck('nama')
                    ->toArray(); 

                $parentWithChildren[$parent->nama] = $children;
            }
        
            // Initialize komentarRapot
            $komentarRapot = [];
            $newSubjects = [];

            foreach ($subjects as $subject => $grades) {
                if (isset($parentWithChildren[$subject])) {
                    $subject = array_filter($parentWithChildren[$subject], function($value) use ($agama) {
                        return strpos($value, $agama) !== false;
                    });
                    $subject = reset($subject);
                }

                // Menyimpan subjek baru ke array baru
                $newSubjects[$subject] = $grades;
                $komentarCK = Penilaian::query()
                    ->select('tps.nama')
                    ->join('penilaian_t_p_s as a', 'a.penilaian_id', '=', 'penilaians.id')
                    ->join('t_p_s as tps', 'tps.id', '=', 'a.tp_id')
                    ->join('c_p_s as cps', 'cps.id', '=', 'tps.cp_id')
                    ->join('mapel_kelas as mk', 'mk.id', '=', 'penilaians.mapel_kelas_id')
                    ->join('mapels as mapels', 'mapels.id', '=', 'mk.mapel_id') // Mapel join dari mapel_kelas
                    ->where('mapels.nama', '=', $subject)
                    ->where('mapels.semester_id', request()->session()->get('semester_id'))
                    ->where(function ($query) use ($subject) {
                        $query->where('penilaians.tipe', '=', 'STS')
                            ->orWhere('penilaians.tanggal', '<', function ($subquery) use ($subject) {
                                $subquery->selectRaw('MIN(penilaians.tanggal)')
                                    ->from('penilaians')
                                    ->join('mapel_kelas as mk', 'mk.id', '=', 'penilaians.mapel_kelas_id')
                                    ->join('mapels as m', 'm.id', '=', 'mk.mapel_id')
                                    ->where('m.nama', '=', $subject)
                                    ->where('penilaians.tipe', '=', 'STS');
                            });
                    })
                    ->groupBy('tps.id', 'tps.nama')
                    ->get();
            
                // Store the retrieved 'nama' values in komentarRapot
                foreach ($komentarCK as $komentarItem) {
                    $komentarRapot[$subject][] = $komentarItem->nama;
                }
            }        
            $subjects = $newSubjects;
            
            $rombelData = Kelas::join('kelas_siswa', 'kelas.id', '=', 'kelas_siswa.kelas_id')
                ->join('siswas', 'siswas.id', '=', 'kelas_siswa.siswa_id')
                ->join('semesters', 'kelas.id_semester', '=', 'semesters.id')
                ->where('siswas.id', $data['student_id'])
                ->where('kelas.kelas', '!=', 'Ekskul')
                ->where('semesters.id', $semesterId)
                ->value('rombongan_belajar');

            $siswaData = Siswa::find($data['student_id']);

            // Fetch attendance summary for the student
            $absensiSummary = AbsensiSiswa::where('id_siswa', $data['student_id'])
                ->where('status', '!=', 'hadir')
                ->where('date', '<=', $tanggal_sts)
                ->selectRaw('status, COUNT(status) as count')
                ->groupBy('status')
                ->get();

            $ttd = [];
            $guru = Guru::join('users', 'users.id', '=', 'gurus.id_user')
                ->where('users.id', $user->id)
                ->first();
            $ttd["walikelas"] = trim($guru->gelar_depan." ".$guru->nama."".$guru->gelar_belakang);
            $ttd["nip_walikelas"] = $guru->nip;

            $semesterData = Semester::find($semesterId);

            // Pass the data to the view for PDF generation
            $pdf = PDF::loadView('walikelas.rapot_sts', [
                'ttd' => $ttd,
                'nisn' => $siswaData->nisn,
                'semester' => $semesterData->semester,
                'tahunAjaran' => $semesterData->tahun_ajaran,
                'rombelData' => $rombelData,
                'semester_id' => $semesterId,
                'subjects' => $subjects,
                'studentName' => $studentName,
                'komentar' => $komentar,
                'komentarRapot' => $komentarRapot,
                'absensiSummary' => $absensiSummary, // Include absensiSummary in the view
            ]);
        
            // Return the PDF as a stream
            return $pdf->stream("RAPOR TENGAH SEMESTER_".strtoupper($studentName)."_{$siswaData->nisn}.pdf");

        } else if ($tipe === 'sas') {
            $tanggal_sas = explode(" - ", $data['tanggal_sas'])[1];

            $komentar = $data['komentar'] ?? '';
            $prestasi = [
                'prestasi_1' => $data['prestasi_1'] ?? null,
                'prestasi_2' => $data['prestasi_2'] ?? null,
                'prestasi_3' => $data['prestasi_3'] ?? null,
            ];
        
            $parents =Mapel::where('semester_id', $semesterId)->whereNull('guru_id')->get();
            $parentWithChildren = [];
            foreach ($parents as $parent) {
                $children = Mapel::where('semester_id', $semesterId)
                    ->where('parent', $parent->id)
                    ->pluck('nama')
                    ->toArray(); 

                $parentWithChildren[$parent->nama] = $children;
            }
        
            // Initialize komentarRapot
            $komentarRapot = [];
            $newSubjects = [];

            foreach ($subjects as $subject => $grades) {
                if (isset($parentWithChildren[$subject])) {
                    $subject = array_filter($parentWithChildren[$subject], function($value) use ($agama) {
                        return strpos($value, $agama) !== false;
                    });
                    $subject = reset($subject);
                }

                // Menyimpan subjek baru ke array baru
                $newSubjects[$subject] = $grades;
                $komentarCK = Penilaian::query()
                    ->select('tps.nama')
                    ->join('penilaian_t_p_s as a', 'a.penilaian_id', '=', 'penilaians.id')
                    ->join('t_p_s as tps', 'tps.id', '=', 'a.tp_id')
                    ->join('c_p_s as cps', 'cps.id', '=', 'tps.cp_id')
                    ->join('mapel_kelas as mk', 'mk.id', '=', 'penilaians.mapel_kelas_id')
                    ->join('mapels as mapels', 'mapels.id', '=', 'mk.mapel_id') // Mapel join dari mapel_kelas
                    ->where('mapels.nama', '=', $subject)
                    ->where('mapels.semester_id', request()->session()->get('semester_id'))
                    ->where(function ($query) use ($subject) {
                        $query->where('penilaians.tipe', '=', 'SAS')
                            ->orWhere('penilaians.tanggal', '<', function ($subquery) use ($subject) {
                                $subquery->selectRaw('MIN(penilaians.tanggal)')
                                    ->from('penilaians')
                                    ->join('mapel_kelas as mk', 'mk.id', '=', 'penilaians.mapel_kelas_id')
                                    ->join('mapels as m', 'm.id', '=', 'mk.mapel_id')
                                    ->where('m.nama', '=', $subject)
                                    ->where('penilaians.tipe', '=', 'SAS');
                            });
                    })
                    ->groupBy('tps.id', 'tps.nama')
                    ->get();
            
                // Store the retrieved 'nama' values in komentarRapot
                foreach ($komentarCK as $komentarItem) {
                    $komentarRapot[$subject][] = $komentarItem->nama;
                }
            }        
            $subjects = $newSubjects;
        
            // Fetch extracurricular (ekskul) data
            $ekskulData = DB::table('penilaian_ekskuls as a')
                ->join('kelas as b', 'b.id', '=', 'a.kelas_id')
                ->join('siswas as c', 'c.id', '=', 'a.siswa_id')
                ->join('semesters as d', 'd.id', '=', 'b.id_semester')
                ->where('a.siswa_id', $data['student_id'])
                ->where('d.id', $semesterId)
                ->select('b.rombongan_belajar', 'a.nilai')
                ->get();
            
            $rombelData = Kelas::join('kelas_siswa', 'kelas.id', '=', 'kelas_siswa.kelas_id')
                ->join('siswas', 'siswas.id', '=', 'kelas_siswa.siswa_id')
                ->join('semesters', 'kelas.id_semester', '=', 'semesters.id')
                ->where('siswas.id', $data['student_id'])
                ->where('kelas.kelas', '!=', 'Ekskul')
                ->where('semesters.id', $semesterId)
                ->value('rombongan_belajar');

            $siswaData = Siswa::find($data['student_id']);

            // Fetch attendance summary for the student
            $absensiSummary = AbsensiSiswa::where('id_siswa', $data['student_id'])
                ->where('status', '!=', 'hadir')
                ->where('date', '<=', $tanggal_sas)
                ->selectRaw('status, COUNT(status) as count')
                ->groupBy('status')
                ->get();

            $p5bkData = P5BK::where('semester_id', $semesterId)
            ->where('siswa_id', $data['student_id'])
            ->select('dimensi', 'capaian')
            ->get();

            $ttd = [];
            $guru = Guru::join('users', 'users.id', '=', 'gurus.id_user')
                ->where('users.id', $user->id)
                ->first();
            $ttd["walikelas"] = trim($guru->gelar_depan." ".$guru->nama."".$guru->gelar_belakang);
            $ttd["nip_walikelas"] = $guru->nip;
            $kepsek = Guru::where('jabatan', 'Kepala Sekolah')
                ->first();
            $ttd["kepsek"] = trim($kepsek->gelar_depan." ".$kepsek->nama."".$kepsek->gelar_belakang);
            $ttd["nip_kepsek"] = $kepsek->nip;

            $semesterData = Semester::find($semesterId);

            // dd($komentarRapot);

            // Pass the data to the view for PDF generation
            $pdf = PDF::loadView('walikelas.rapot_sas', [
                'ttd' => $ttd,
                'nisn' => $siswaData->nisn,
                'semester' => $semesterData->semester,
                'tahunAjaran' => $semesterData->tahun_ajaran,
                'rombelData' => $rombelData,
                'semester_id' => $semesterId,
                'p5bkData' => $p5bkData,
                'subjects' => $subjects,
                'studentName' => $studentName,
                'komentar' => $komentar,
                'prestasi' => $prestasi,
                'ekskulData' => $ekskulData,
                'komentarRapot' => $komentarRapot,
                'absensiSummary' => $absensiSummary, 
            ]);
        
            // Return the PDF as a stream
            return $pdf->stream("RAPOR AKHIR SEMESTER_".strtoupper($studentName)."_{$siswaData->nisn}.pdf");
        }
    }

    // Index function for displaying the form with siswa options
    public function p5bkIndex(Request $request, $semesterId)
    {
        $user = Auth::user();
        
        // Fetch siswa options for the logged-in user
        $siswaOptions = Siswa::join('kelas_siswa', 'kelas_siswa.siswa_id', '=', 'siswas.id')
            ->join('kelas', 'kelas.id', '=', 'kelas_siswa.kelas_id')
            ->join('semesters', 'semesters.id', '=', 'kelas.id_semester')
            ->join('gurus', 'gurus.id', '=', 'kelas.id_guru')
            ->join('users', 'users.id', '=', 'gurus.id_user')
            ->where('users.id', $user->id)
            ->where('semesters.id', $semesterId)
            ->where('kelas.kelas', '!=', 'Ekskul')
            ->select('siswas.*', 'kelas.rombongan_belajar')
            ->get();
        
        // Fetch P5BK data for the selected semester and siswa
        $p5bk = P5BK::where('semester_id', $semesterId)
            ->whereIn('siswa_id', $siswaOptions->pluck('id'))
            ->get();
        
        return view('walikelas.p5bk', compact('siswaOptions', 'p5bk', 'semesterId'));
    }
    
    // Fetch P5BK data for a specific siswa (AJAX request)
    public function fetchP5BK(Request $request)
    {
        $semesterId = $request->semester_id;
        $siswaId = $request->siswa_id;
        
        // Fetch P5BK data for the selected student and semester
        $p5bkData = P5BK::where('semester_id', $semesterId)
            ->where('siswa_id', $siswaId)
            ->get();
        
        // Return the data as a JSON response
        return response()->json($p5bkData);
    }    
    
    // Save P5BK data via AJAX
    public function saveP5BKAjax(Request $request, $semesterId)
    {
        // Validate the input data
        $request->validate([
            'capaian' => 'required|array',  // Ensure 'capaian' is an array
            'siswa_id' => 'required|integer',  // Ensure siswa_id is present
        ]);
    
        // Save the data (this is an example, adjust as necessary)
        foreach ($request->capaian as $dimensi => $capaian) {
            P5BK::updateOrCreate(
                [
                    'siswa_id' => $request->siswa_id,
                    'semester_id' => $semesterId,
                    'dimensi' => $dimensi,  // Ensure the 'dimensi' exists
                ],
                [
                    'capaian' => $capaian,
                    'status' => 1, // Set status to true (fulfilled)
                ]
            );
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Penilaian P5 berhasil disimpan!',
        ]);
    }
    
    public function bukuAbsen($semesterId)
    {
        $user = Auth::user();
        $students = DB::table('absensi_siswas as a')
            ->join('siswas as b', 'b.id', '=', 'a.id_siswa')
            ->join('kelas_siswa as c', 'c.siswa_id', '=', 'b.id')
            ->join('kelas as d', 'd.id', '=', 'c.kelas_id')
            ->join('gurus as f', 'f.id', '=', 'd.id_guru')
            ->join('users as g', 'g.id', '=', 'f.id_user')
            ->join('semesters as h', 'h.id', '=', 'd.id_semester')
            ->where('g.id', $user->id)
            ->where('h.id', $semesterId)
            ->where('d.kelas', '!=', 'Ekskul')
            ->select(
                'b.nama',
                'b.nisn',
                DB::raw("COUNT(CASE WHEN a.status = 'hadir' THEN a.id END) AS count_hadir"),
                DB::raw("COUNT(CASE WHEN a.status = 'terlambat' THEN a.id END) AS count_terlambat"),
                DB::raw("COUNT(CASE WHEN a.status = 'ijin' THEN a.id END) AS count_ijin"),
                DB::raw("COUNT(CASE WHEN a.status = 'alpha' THEN a.id END) AS count_alpha"),
                DB::raw("COUNT(CASE WHEN a.status = 'sakit' THEN a.id END) AS count_sakit"),
                DB::raw("COUNT(a.id) AS count_all")
            )
            ->groupBy('b.id', 'b.nama', 'b.nisn')
            ->get();
    
        return view('walikelas.bukuAbsen', compact('students'));
    }
}