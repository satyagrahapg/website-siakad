<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Penilaian;
use App\Models\TP;
use App\Models\Siswa;
use App\Models\PenilaianSiswa;
use App\Models\PenilaianEkskul;
use App\Models\MapelKelas;
use App\Models\PenilaianTP;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    public function index($mapelKelasId)
    {
        // Find the MapelKelas record or fail gracefully if it doesn't exist
        $mapelKelas = MapelKelas::findOrFail($mapelKelasId);

        // Get Siswa data for the specific class
        $getSiswaClassData = Siswa::join('kelas_siswa', 'kelas_siswa.siswa_id', '=', 'siswas.id')
            ->where('kelas_siswa.kelas_id', $mapelKelas->kelas_id)
            ->get();

        $penilaians = Penilaian::join('mapel_kelas as b', 'b.id', '=', 'penilaians.mapel_kelas_id')
            ->join('penilaian_t_p_s as c', 'c.penilaian_id', '=', 'penilaians.id') // Join ke tabel pivot
            ->join('t_p_s as t', 't.id', '=', 'c.tp_id') // Join ke tabel t_p_s
            ->join('c_p_s as cp', 'cp.id', '=', 't.cp_id') // Join ke tabel c_p_s
            ->where('b.id', $mapelKelasId)
            ->select(
                'penilaians.id',
                'penilaians.tipe',
                'penilaians.judul',
                'penilaians.kktp',
                'penilaians.tanggal',
                'penilaians.keterangan',
                DB::raw("GROUP_CONCAT(CONCAT(cp.nomor, '.', t.nomor) ORDER BY cp.nomor ASC, t.nomor ASC SEPARATOR ', ') as tp_ids"), // Gabungkan c_p_s.nomor dan t_p_s.nomor
                DB::raw("GROUP_CONCAT(c.tp_id ORDER BY cp.nomor ASC, t.nomor ASC) as array_tp_ids_raw") // Gabungkan tp_id dalam urutan yang sama
            )
            ->groupBy('penilaians.id', 'penilaians.tipe', 'penilaians.judul', 'penilaians.kktp', 'penilaians.tanggal', 'penilaians.keterangan') // Grouping
            ->orderBy('penilaians.tanggal', 'desc') // Urutkan berdasarkan tanggal
            ->get()
            ->map(function ($item) {
                // Ubah string array_tp_ids_raw menjadi array
                $item->array_tp_ids = explode(',', $item->array_tp_ids_raw);
                unset($item->array_tp_ids_raw); // Hapus field raw untuk kebersihan data
                return $item;
            });

        // Get the Kelas record related to the MapelKelas
        $kelas = Kelas::findOrFail($mapelKelas->kelas_id);

        $mapel = Mapel::findOrFail($mapelKelas->mapel_id);
        // if (!$mapel->guru_id) $mapel = Mapel::join('pendidiks as pdk', 'pdk.id', '=', 'mapels.guru_id')
        //     ->where('mapels.parent', '=', $mapelKelas->mapel_id)
        //     ->where('pdk.id_user', '=', auth()->user()->id)
        //     ->select('mapels.*')
        //     ->first();
            
        // Options for TP, based on the provided MapelKelas ID
        $tpOptions = TP::select(
            't_p_s.id',
            DB::raw("CONCAT(c_p_s.nomor, '.', t_p_s.nomor, ' ', t_p_s.nama) as formatted_name") // Format string sesuai kebutuhan
        )
        ->join('c_p_s', 'c_p_s.id', '=', 't_p_s.cp_id')
        ->join('mapel_kelas', 'mapel_kelas.mapel_id', '=', 'c_p_s.mapel_id')
        ->where('mapel_kelas.id', $mapelKelasId)
        ->orderBy('c_p_s.nomor') // Urutkan berdasarkan nomor cps
        ->orderBy('t_p_s.nomor') // Urutkan berdasarkan nomor tps
        ->get();

        $typesExist = array_unique($penilaians->pluck('tipe')->toArray());

        // dd($penilaians);

        // Return the view with the necessary data
        return view('penilaian.index', [
            'penilaians' => $penilaians,
            'typesExist' => $typesExist,
            'kelas' => $kelas,
            'mapel' => $mapel,
            'tpOptions' => $tpOptions,
            'getSiswaClassData' => $getSiswaClassData,
            'mapelKelasId' => $mapelKelasId,
            'mapelId' => $mapelKelas->mapel_id, // Ensure mapel_id exists in MapelKelas
        ]);
    }


    public function storePenilaian(Request $request, $mapelKelasId)
    {
        // dd($request->all(), $request->session()->get('semester_id'));
        $request->validate([
            'tipe' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'tanggal' => ['required', function ($attribute, $value, $fail) use ($request) {
                $semester = Semester::find($request->session()->get('semester_id'));
    
                if (!$semester) {
                    $fail('Semester tidak ditemukan.');
                    return;
                }
    
                if ($value < $semester->start || $value > $semester->end) {
                    $fail('Tanggal tidak valid untuk semester yang dipilih! Pilih tanggal antara ' 
                        . $semester->start . ' dan ' . $semester->end 
                        . ' untuk ' . $semester->semester . ' ' . $semester->tahun_ajaran . '.');
                }
            }
        ],
            'kktp' => 'required|integer',
            'keterangan' => 'nullable|string|max:255',
            'tp_ids' => 'required',
        ]);       

        $mapelkelas = MapelKelas::find($mapelKelasId);

        // Create the Penilaian
        $penilaian = Penilaian::create([
            'tipe' => $request->tipe,
            'judul' => $request->judul,
            'tanggal' => $request->tanggal,
            'kktp' => $request->kktp,
            'keterangan' => $request->keterangan,
            'mapel_kelas_id' => $mapelKelasId
        ]);

        $penilaian->tps()->sync($request->tp_ids);

        $mapel = Mapel::findOrFail($mapelkelas->mapel_id);

        $get_siswa_class_data = Siswa::join('kelas_siswa', 'kelas_siswa.siswa_id', '=', 'siswas.id')
            ->where('kelas_siswa.kelas_id', $mapelkelas->kelas_id)
            ->get();
        
        // dd($get_siswa_class_data);

        // Create PenilaianSiswa records
        foreach ($get_siswa_class_data as $siswa) {
            if ($mapel->parent && strpos($mapel->nama, $siswa->agama) === false) continue;
            PenilaianSiswa::create([
                'status' => 0, // Default status
                'nilai' => null,
                'remedial' => null,
                'nilai_akhir' => null,
                'penilaian_id' => $penilaian->id,
                'siswa_id' => $siswa->siswa_id,
            ]);
        }

        return redirect()->route('penilaian.index', [$mapelKelasId])->with('success', 'Penilaian berhasil ditambahkan!');
    }


    public function updatePenilaian(Request $request, $mapelKelasId, $penilaianId)
    {
        // Validate the incoming request data
        $request->validate([
            'tipe' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'tanggal' => ['required', function ($attribute, $value, $fail) use ($request) {
                $semester = Semester::find($request->session()->get('semester_id'));
    
                if (!$semester) {
                    $fail('Semester tidak ditemukan.');
                    return;
                }
    
                if ($value < $semester->start || $value > $semester->end) {
                    $fail('Tanggal tidak valid untuk semester yang dipilih! Pilih tanggal antara ' 
                        . $semester->start . ' dan ' . $semester->end 
                        . ' untuk ' . $semester->semester . ' ' . $semester->tahun_ajaran . '.');
                }
            }
        ],
            'kktp' => 'required|integer',
            'keterangan' => 'nullable|string|max:255',
            'tp_ids' => 'required',
        ]);
        
        // Find the Penilaian record by its ID
        $penilaian = Penilaian::findOrFail($penilaianId);

        // Update the Penilaian record
        $penilaian->tipe = $request->input('tipe');
        $penilaian->judul = $request->input('judul');
        $penilaian->tanggal = $request->input('tanggal');
        $penilaian->kktp = $request->input('kktp');
        $penilaian->keterangan = $request->input('keterangan');
        $penilaian->mapel_kelas_id = $mapelKelasId;
        $penilaian->save();

        $penilaian->tps()->sync($request->tp_ids);

        // Redirect with success message
        return redirect()->route('penilaian.index', [$mapelKelasId])->with('success', 'Penilaian berhasil diperbarui!');
    }

    public function deletePenilaian($mapelKelasId, $penilaianId)
    {
        // Find the CP record by its ID
        $penilaian = Penilaian::findOrFail($penilaianId);

        // Delete the CP record
        $penilaian->delete();

        // Redirect with success message
        return redirect()->route('penilaian.index', [$mapelKelasId])->with('success', 'Penilaian berhasil dihapus!');
    }

    // End of Penilaian's function codes //

    public function bukaPenilaian($mapelKelasId, $penilaianId)
    {
        $penilaian_siswas = PenilaianSiswa::join('siswas', 'siswas.id', '=', 'penilaian_siswa.siswa_id')
            ->where('penilaian_id', $penilaianId)
            ->select('penilaian_siswa.id', 'penilaian_siswa.status', 'penilaian_siswa.nilai', 'penilaian_siswa.remedial', 'penilaian_siswa.nilai_akhir', 'penilaian_siswa.penilaian_id', 'penilaian_siswa.siswa_id', 'siswas.nama')
            ->get();

        $penilaian = Penilaian::where('penilaians.id', $penilaianId)
            ->select('penilaians.id', 'penilaians.tipe', 'penilaians.judul', 'penilaians.kktp', 'penilaians.keterangan')
            ->first();
            
        return view('penilaian.buka', compact('penilaian_siswas', 'mapelKelasId', 'penilaian'));
    }

    public function updatePenilaianSiswaBatch(Request $request, $mapelKelasId)
    {
        $penilaianData = $request->input('penilaian', []); // Get penilaian data from the request

        foreach ($penilaianData as $penilaianSiswaId => $data) {
            $penilaian = PenilaianSiswa::find($penilaianSiswaId); // Get the PenilaianSiswa record
            if ($penilaian) {
                // Update values
                $penilaian->nilai = $data['nilai'] ?? null;
                $penilaian->remedial = $data['remedial'] ?? null;

                // Access kktp from the related Penilaian model
                $kkm = $penilaian->penilaian->kktp; // Assuming penilaian() relationship is defined in PenilaianSiswa model

                if ($penilaian->nilai > $kkm && !is_null($penilaian->remedial)) {
                    if ($penilaian->remedial > $penilaian->nilai) {
                        $penilaian->nilai_akhir = ($penilaian->nilai + $penilaian->remedial) / 2;
                    } else {
                        $penilaian->nilai_akhir = $penilaian->nilai;
                    }
                } elseif ($penilaian->nilai < $kkm && !is_null($penilaian->remedial)) {
                    if ($penilaian->remedial > $kkm) {
                        $penilaian->nilai_akhir = $kkm;
                    } elseif ($penilaian->remedial > $penilaian->nilai) {
                        $penilaian->nilai_akhir = $penilaian->remedial;
                    } else {
                        $penilaian->nilai_akhir = $penilaian->nilai;
                    }
                } else {
                    $penilaian->nilai_akhir = $penilaian->nilai;
                }
                // Update status based on null data on penilaian
                $penilaian->status = !is_null($penilaian->nilai) || !is_null($penilaian->remedial) || !is_null($penilaian->nilai_akhir);

                // Save the updated record
                $penilaian->save();
            }
        }

        return redirect()->back()->with('success', 'Penilaian berhasil diperbarui!');
    }

    public function bukuNilai($mapelKelasId)
    {
        // $mapelKelas = MapelKelas::find($mapelKelasId);
        $datas = PenilaianSiswa::join('penilaians as b', 'b.id', '=', 'penilaian_siswa.penilaian_id')
            ->join('siswas as c', 'c.id', '=', 'penilaian_siswa.siswa_id')
            // ->join('mapel_kelas as f', 'f.mapel_id', '=', 'b.mapel_kelas_id')
            // ->where('f.kelas_id', $mapelKelas->kelas_id)
            ->where('b.mapel_kelas_id', $mapelKelasId)
            ->select(
                'c.nama',
                'c.nisn',
                DB::raw("AVG(CASE WHEN b.tipe = 'Tugas' THEN penilaian_siswa.nilai_akhir END) as avg_tugas"),
                DB::raw("AVG(CASE WHEN b.tipe = 'UH' THEN penilaian_siswa.nilai_akhir END) as avg_uh"),
                DB::raw("AVG(CASE WHEN b.tipe = 'SAS' THEN penilaian_siswa.nilai_akhir END) as avg_sas"),
                DB::raw("AVG(CASE WHEN b.tipe = 'STS' THEN penilaian_siswa.nilai_akhir END) as avg_sts"),
                DB::raw("(
                    AVG(CASE WHEN b.tipe = 'Tugas' THEN penilaian_siswa.nilai_akhir END) +
                    AVG(CASE WHEN b.tipe = 'UH' THEN penilaian_siswa.nilai_akhir END) +
                    AVG(CASE WHEN b.tipe = 'SAS' THEN penilaian_siswa.nilai_akhir END) +
                    AVG(CASE WHEN b.tipe = 'STS' THEN penilaian_siswa.nilai_akhir END)
                ) / 4 as nilai_akhir")
            )
            ->groupBy('c.nama')
            ->groupBy('c.nisn')
            ->get();

        $kelas = Kelas::join('mapel_kelas', 'mapel_kelas.kelas_id', '=', 'kelas.id')
            ->select('kelas.rombongan_belajar as rombel')
            ->where('mapel_kelas.id', $mapelKelasId)
            ->first();

        $mapel = Mapel::join('mapel_kelas', 'mapel_kelas.mapel_id', '=', 'mapels.id')
            ->select('mapels.nama')
            ->where('mapel_kelas.id', $mapelKelasId)
            ->first();

        return view('penilaian.buku-nilai', compact('datas', 'kelas', 'mapel'));
    }

    public function penilaianEkskul(Request $request, $kelasId, $mapelId)
    {
        // Fetch all penilaianEkskul records related to the specified mapel
        $penilaianEkskuls = PenilaianEkskul::where('kelas_id', $kelasId)
        ->get();

        return view('penilaian.ekskul', compact('penilaianEkskuls', 'kelasId', 'mapelId'));
    }

    public function updateAllPenilaianEkskul(Request $request, $kelasId, $mapelId)
    {

        $data = $request->input('nilai', []);

        foreach ($data as $penilaianEkskulId => $dataEkskul) {
            $penilaian = PenilaianEkskul::find($penilaianEkskulId);

            if ($penilaian) {
                // Update values
                $penilaian->nilai = $dataEkskul['nilai'] ?? null;
            }

            $penilaian->save();
        }

        return redirect()->back()
            ->with('success', 'Penilaian ekstrakurikuler berhasil diperbarui!');
    }
}
