<?php

namespace App\Http\Controllers;

use App\Models\JamPelajaran;
use App\Models\JamPelajaranMapelKelas;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\MapelKelas;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class JadwalMapelController extends Controller
{
    public function index(Request $request)
    {
        $semesters = Semester::all();

        return view('jadwalmapel.index', compact('semesters'));
    }

    public function indexAjaxHandler(Request $request)
    {
        $action = $request->input('action');
        $response = [];

        switch ($action) {
            case 'getKelas':
                $semesterId = $request->input('semesterId');
                $response = Kelas::select('kelas.kelas')
                    ->where('id_semester', $semesterId)
                    ->where('kelas.kelas', '!=', 'Ekskul')
                    ->groupBy('kelas.kelas')->get();
                break;

            case 'getRombel':
                $semesterId = $request->input('semesterId');
                $kelasKelas = $request->input('kelasKelas');
                $response = Kelas::where('kelas.kelas', $kelasKelas)
                    ->where('id_semester', $semesterId)
                    ->get();
                break;

            case 'getMapel':
                $kelasId = $request->input('kelasId');
                $response = MapelKelas::join('mapels as m', 'm.id', '=', 'mapel_kelas.mapel_id')
                    ->select('mapel_kelas.id as id', 'm.nama as nama_mapel')
                    ->orderBy('m.nama', 'asc')
                    ->whereNull('m.guru_id')
                    ->where('mapel_kelas.kelas_id', $kelasId)->get();
                $responseWithoutParent = MapelKelas::join('mapels as m', 'm.id', '=', 'mapel_kelas.mapel_id')
                    ->join('pendidiks as pdk', 'pdk.id', '=', 'm.guru_id')
                    ->select('mapel_kelas.id as id', 'm.nama as nama_mapel', 'pdk.nama as nama_guru')
                    ->orderBy('m.nama', 'asc')
                    ->whereNull('m.parent')
                    ->where('mapel_kelas.kelas_id', $kelasId)->get();
                $response = $response->push($responseWithoutParent)->flatten();         
                break;

            case 'getJampel':
                $mapelkelasId = $request->input('mapelkelasId');
                $response = JamPelajaran::whereNull('event')
                    ->leftJoinSub(
                        DB::table('jam_pelajaran')
                            ->select('jam_pelajaran.id', 'k.rombongan_belajar')
                            ->leftJoin('jam_pelajaran_mapel_kelas as jpmk', 'jpmk.jampel_id', '=', 'jam_pelajaran.id')
                            ->leftJoin('mapel_kelas as mk', 'mk.id', '=', 'jpmk.mapel_kelas_id')
                            ->leftJoin('kelas as k', 'k.id', '=', 'mk.kelas_id')
                            ->leftJoin('mapels as m', 'm.id', '=', 'mk.mapel_id')
                            ->where('m.guru_id', '=', function($query) use ($mapelkelasId) {
                                $query->select('guru_id')
                                    ->from('mapels')
                                    ->join('mapel_kelas as mk', 'mk.mapel_id', '=', 'mapels.id')
                                    ->where('mk.id', '=', $mapelkelasId)
                                    ->limit(1);
                            }), 'bg', 'bg.id', '=', 'jam_pelajaran.id')
                    ->leftJoinSub(
                        DB::table('jam_pelajaran')
                            ->select('jam_pelajaran.id', 'k.rombongan_belajar')
                            ->leftJoin('jam_pelajaran_mapel_kelas as jpmk', 'jpmk.jampel_id', '=', 'jam_pelajaran.id')
                            ->leftJoin('mapel_kelas as mk', 'mk.id', '=', 'jpmk.mapel_kelas_id')
                            ->leftJoin('kelas as k', 'k.id', '=', 'mk.kelas_id')
                            ->leftJoin('mapels as m', 'm.id', '=', 'mk.mapel_id')
                            ->whereNull('m.guru_id')
                            ->where('m.id', '=', function($query) use ($mapelkelasId) {
                                $query->select('mapels.id')
                                    ->from('mapels')
                                    ->join('mapel_kelas as mk', 'mk.mapel_id', '=', 'mapels.id')
                                    ->where('mk.id', '=', $mapelkelasId)
                                    ->limit(1);
                            }), 'bp', 'bp.id', '=', 'jam_pelajaran.id')
                    ->leftJoinSub(
                        DB::table('jam_pelajaran')
                            ->select('jam_pelajaran.id', DB::raw('1 as booked'))
                            ->leftJoin('jam_pelajaran_mapel_kelas as jpmk', 'jpmk.jampel_id', '=', 'jam_pelajaran.id')
                            ->leftJoin('mapel_kelas as mk', 'mk.id', '=', 'jpmk.mapel_kelas_id')
                            ->where('mk.kelas_id', function($query) use ($mapelkelasId) {
                                $query->select('kelas_id')
                                    ->from('mapel_kelas')
                                    ->where('mapel_kelas.id', '=', $mapelkelasId)
                                    ->limit(1);
                            }), 'bk', 'bk.id', '=', 'jam_pelajaran.id')
                    ->select('jam_pelajaran.id', 'jam_pelajaran.hari', 'jam_pelajaran.nomor', 'jam_pelajaran.jam_mulai', 'jam_pelajaran.jam_selesai', DB::raw('COALESCE(bg.rombongan_belajar, bp.rombongan_belajar) as rombongan_belajar'), 'bk.booked')
                    ->orderBy('jam_pelajaran.hari', 'asc')
                    ->orderBy('jam_pelajaran.nomor', 'asc')
                    ->get();
                break;

            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }

        return response()->json($response);
    }

    private function generateRandomColor() {
        $min = 127;
        $r = mt_rand($min, 255);
        $g = mt_rand($min, 255);
        $b = mt_rand($min, 255);
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    public function getDataCalendar(Request $request) {
        $dayMapping = [
            'Senin' => 0,
            'Selasa' => 1,
            'Rabu' => 2,
            'Kamis' => 3,
            'Jumat' => 4,
            'Sabtu' => 5,
            'Minggu' => 6,
        ];

        $output = [];
        $jampelmapels = JamPelajaran::leftJoin('jam_pelajaran_mapel_kelas as jpmk', 'jpmk.jampel_id', '=', 'jam_pelajaran.id')
            ->leftJoin('mapel_kelas as mk', 'mk.id', '=','jpmk.mapel_kelas_id')
            ->leftJoin('mapels as m', 'm.id', '=', 'mk.mapel_id')
            ->leftJoin('kelas as k', 'k.id', '=','mk.kelas_id')
            ->where(function($query) use ($request) {
                $query->whereNotNull('jam_pelajaran.event')
                    ->orWhere(function($query) use ($request) {
                        $query->whereNotNull('m.id')
                            ->where('k.id', $request->input('rombelId'));
                    });
            })
            ->select('jam_pelajaran.*', 'm.nama as nama_mapel', 'jpmk.id as jpmk_id')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), jam_mulai ASC")->get();

        foreach ($jampelmapels as $jampel) {
            $day = $dayMapping[$jampel['hari']] ?? null;

            if ($day !== null) {
                $dayKey = array_search($day, array_column($output, 'day'));
                if ($dayKey === false) {
                    $output[] = [
                        'day' => $day,
                        'periods' => [],
                    ];
                    $dayKey = array_key_last($output);
                }

                $title = strip_tags($jampel->event ?? $jampel->nama_mapel);

                $titleColors = session()->get('titleColors', []);
                if ($jampel->event) {
                    $backgroundColor = '#909090';
                } else {
                    if (isset($titleColors[$title])) {
                        $backgroundColor = $titleColors[$title];
                    } else {
                        $backgroundColor = $this->generateRandomColor();
                        $titleColors[$title] = $backgroundColor;
                        session()->put('titleColors', $titleColors);
                    }
                }

                $output[$dayKey]['periods'][] = [
                    'start' => $jampel->jam_mulai_calendar,
                    'end' => $jampel->jam_selesai_calendar,
                    'title' => '<span jpmk-id="'.$jampel->jpmk_id.'" hari='.$jampel->hari.' style="display:none;"></span>'.$title .'<br>'.($jampel->nomor ? 'Jam ke-' . $jampel->nomor . '<br>' : '') . substr($jampel->jam_mulai, 0, 5) . ' - ' . substr($jampel->jam_selesai, 0, 5),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => '#000',
                    'textColor' => '#000',
                ];
            }
        }

        return response($output, 200)->header('Content-Type', 'application/json');
    }

    public function getDataCalendarGuru(Request $request) {
        $semesterId = $request->session()->get('semester_id');

        $dayMapping = [
            'Senin' => 0,
            'Selasa' => 1,
            'Rabu' => 2,
            'Kamis' => 3,
            'Jumat' => 4,
            'Sabtu' => 5,
            'Minggu' => 6,
        ];

        $output = [];
        $jampelmapels = JamPelajaran::leftJoin('jam_pelajaran_mapel_kelas as jpmk', 'jpmk.jampel_id', '=', 'jam_pelajaran.id')
            ->leftJoin('mapel_kelas as mk', 'mk.id', '=','jpmk.mapel_kelas_id')
            ->leftJoin('mapels as m', 'm.id', '=', 'mk.mapel_id')
            ->leftJoin('kelas as k', 'k.id', '=','mk.kelas_id')
            ->leftJoin('pendidiks as pdk', 'pdk.id', '=', 'm.guru_id')
            ->where('pdk.id_user', '=', auth()->user()->id)
            ->where('k.id_semester', '=', $semesterId)
            ->orWhereNotNull('jam_pelajaran.event')
            ->select('jam_pelajaran.*', 'm.nama as nama_mapel', 'jpmk.id as jpmk_id', 'k.rombongan_belajar as rombel')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), jam_mulai ASC")->get();

        $jampelmapelsParent = JamPelajaran::leftJoin('jam_pelajaran_mapel_kelas as jpmk', 'jpmk.jampel_id', '=', 'jam_pelajaran.id')
            ->leftJoin('mapel_kelas as mk', 'mk.id', '=','jpmk.mapel_kelas_id')
            ->leftJoin('mapels as m', 'm.parent', '=', 'mk.mapel_id')
            ->leftJoin('kelas as k', 'k.id', '=','mk.kelas_id')
            ->leftJoin('pendidiks as pdk', 'pdk.id', '=', 'm.guru_id')
            ->where('pdk.id_user', '=', auth()->user()->id)
            ->where('k.id_semester', '=', $semesterId)
            ->WhereNull('jam_pelajaran.event')
            ->select('jam_pelajaran.*', 'm.nama as nama_mapel', 'jpmk.id as jpmk_id', 'k.rombongan_belajar as rombel')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), jam_mulai ASC")->get();

        $jampelmapels = $jampelmapels->push($jampelmapelsParent)->flatten();

        foreach ($jampelmapels as $jampel) {
            $day = $dayMapping[$jampel['hari']] ?? null;

            if ($day !== null) {
                $dayKey = array_search($day, array_column($output, 'day'));
                if ($dayKey === false) {
                    $output[] = [
                        'day' => $day,
                        'periods' => [],
                    ];
                    $dayKey = array_key_last($output);
                }

                $title = strip_tags($jampel->event ?? ($jampel->rombel.' | '.$jampel->nama_mapel));

                $titleColors = session()->get('titleColors', []);
                if ($jampel->event) {
                    $backgroundColor = '#909090';
                } else {
                    if (isset($titleColors[$title])) {
                        $backgroundColor = $titleColors[$title];
                    } else {
                        $backgroundColor = $this->generateRandomColor();
                        $titleColors[$title] = $backgroundColor;
                        session()->put('titleColors', $titleColors);
                    }
                }

                $output[$dayKey]['periods'][] = [
                    'start' => $jampel->jam_mulai_calendar,
                    'end' => $jampel->jam_selesai_calendar,
                    'title' => $title .'<br>'.($jampel->nomor ? 'Jam ke-' . $jampel->nomor . '<br>' : '') . substr($jampel->jam_mulai, 0, 5) . ' - ' . substr($jampel->jam_selesai, 0, 5),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => '#000',
                    'textColor' => '#000',
                ];
            }
        }

        return response($output, 200)->header('Content-Type', 'application/json');
    }

    public function getDataCalendarSiswa(Request $request) {
        $semesterId = $request->session()->get('semester_id');

        $dayMapping = [
            'Senin' => 0,
            'Selasa' => 1,
            'Rabu' => 2,
            'Kamis' => 3,
            'Jumat' => 4,
            'Sabtu' => 5,
            'Minggu' => 6,
        ];

        $output = [];
        $jampelmapels = JamPelajaran::leftJoin('jam_pelajaran_mapel_kelas as jpmk', 'jpmk.jampel_id', '=', 'jam_pelajaran.id')
            ->leftJoin('mapel_kelas as mk', 'mk.id', '=','jpmk.mapel_kelas_id')
            ->leftJoin('mapels as m', 'm.id', '=', 'mk.mapel_id')
            ->leftJoin('kelas as k', 'k.id', '=','mk.kelas_id')
            ->where('k.id', '=', function ($query) {
                $query->select('ks.kelas_id')
                    ->from('kelas_siswa as ks')
                    ->join('siswas as s', 'ks.siswa_id', '=', 's.id')
                    ->where('s.id_user', '=', auth()->user()->id)
                    ->limit(1);
            })
            ->where('k.id_semester', $semesterId)
            ->orWhereNotNull('jam_pelajaran.event')
            ->select('jam_pelajaran.*', 'm.nama as nama_mapel', 'jpmk.id as jpmk_id', 'k.rombongan_belajar as rombel')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), jam_mulai ASC")->get();

        foreach ($jampelmapels as $jampel) {
            $day = $dayMapping[$jampel['hari']] ?? null;

            if ($day !== null) {
                $dayKey = array_search($day, array_column($output, 'day'));
                if ($dayKey === false) {
                    $output[] = [
                        'day' => $day,
                        'periods' => [],
                    ];
                    $dayKey = array_key_last($output);
                }

                $title = strip_tags($jampel->event ?? $jampel->nama_mapel);

                $titleColors = session()->get('titleColors', []);
                if ($jampel->event) {
                    $backgroundColor = '#909090';
                } else {
                    if (isset($titleColors[$title])) {
                        $backgroundColor = $titleColors[$title];
                    } else {
                        $backgroundColor = $this->generateRandomColor();
                        $titleColors[$title] = $backgroundColor;
                        session()->put('titleColors', $titleColors);
                    }
                }

                $output[$dayKey]['periods'][] = [
                    'start' => $jampel->jam_mulai_calendar,
                    'end' => $jampel->jam_selesai_calendar,
                    'title' => $title .'<br>'.($jampel->nomor ? 'Jam ke-' . $jampel->nomor . '<br>' : '') . substr($jampel->jam_mulai, 0, 5) . ' - ' . substr($jampel->jam_selesai, 0, 5),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => '#000',
                    'textColor' => '#000',
                ];
            }
        }

        return response($output, 200)->header('Content-Type', 'application/json');
    }

    public function storeMapelJampel(Request $request) {
        $request->validate([
            'mapelkelasId' =>'required',
            'jampelId' =>'required',
        ]);

        MapelKelas::findOrFail($request->input('mapelkelasId'))->jampel()->syncWithoutDetaching($request->input('jampelId'));

        return response()->json(['message' => 'Jadwal berhasil ditambahkan.']);
    }

    public function deleteMapelJampel(Request $request) {
        $request->validate([
            'jpmkId' =>'required',
        ]);

        JamPelajaranMapelKelas::findOrFail($request->input('jpmkId'))->delete();

        return response()->json(['message' => 'Jadwal berhasil dihapus.']);
    }

    public function showJampel(Request $request) {
        $jampels = JamPelajaran::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), jam_mulai ASC")->get();

        return view('jadwalmapel.index-jampel', compact('jampels'));
    }

    public function storeJampel(Request $request) {
        $request->validate([
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        JamPelajaran::create($request->all());

        return redirect()->route('jadwalmapel.index-jampel')->with('success', 'Jam pelajaran berhasil ditambahkan.');
    }

    public function hapusJampel($jampelId)
    {
        $jampel = JamPelajaran::findOrFail($jampelId);
        $jampel->delete();

        return redirect()->route('jadwalmapel.index-jampel')->with('success', 'Jam pelajaran berhasil dihapus.');
    }

    public function updateJampel(Request $request, $jampelId) {
        $request->validate([
            'hari' =>'required',
            'jam_mulai' =>'required',
            'jam_selesai' =>'required',
        ]);

        $jampel = JamPelajaran::findOrFail($jampelId);
        $jampel->update($request->all());

        return redirect()->route('jadwalmapel.index-jampel')->with('success', 'Jam pelajaran berhasil diubah.');
    }

    public function getKelasByMapel(Request $request)
    {
        $request->validate([
            'mapel_id' => 'required|exists:mapels,id', // Correct parameter name
        ]);
    
        $kelasOptions = DB::table('mapel_kelas')
            ->join('kelas', 'mapel_kelas.kelas_id', '=', 'kelas.id')
            ->where('mapel_kelas.mapel_id', $request->mapel_id) // Correct field name 'mapel_id'
            ->select('kelas.id', 'kelas.kelas', 'kelas.rombongan_belajar as rombel')
            ->get();
        
        return response()->json($kelasOptions);
    }    
}
