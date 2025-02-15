<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Semester;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\KomentarCK;
use Illuminate\Support\Facades\DB;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve all mapels for the dropdown list
        $listMapel = Mapel::select('nama')->distinct()->get();
        $semesters = Semester::all();
        $gurus = Guru::all();
        $kelas = Kelas::all();

        // Initialize the query for mapels and apply filters if present
        $query = Mapel::with('guru', 'semester'); // Eager load related data

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->input('semester_id'));
        }

        if ($request->filled('mapel')) {
            $mapel = Mapel::where('nama', $request->input('mapel'))->first();
            $query->where('id', $mapel->id);
            if (!$mapel->guru) $query->orWhere('parent', $mapel->id);
        }

        // Get the filtered results
        $mapels = $query->get();

        // Get unique semester_ids and kelas values (7, 8, or 9) from the filtered mapels
        $semesterIds = $mapels->pluck('semester_id')->unique();
        $kelasValues = $mapels->pluck('kelas')->unique();

        // Split the kelas values into an array of values
        $kelasValues = $kelasValues->map(function ($item) {
            return explode(',', $item);
        })->flatten()->unique();

        // Filter Kelas based on semester_ids and kelas values from the mapels
        $kelasOptions = Kelas::whereIn('id_semester', $semesterIds)
                            ->whereIn('kelas', $kelasValues)
                            ->get();

        // Generate the rombel data by grouping and combining rombongan_belajar for each mapel ID
        $rombel = DB::table('mapels')
                    ->join('mapel_kelas', 'mapel_kelas.mapel_id', '=', 'mapels.id')
                    ->join('kelas', 'mapel_kelas.kelas_id', '=', 'kelas.id')
                    ->select('mapels.id', 'kelas.rombongan_belajar')
                    ->get()
                    ->groupBy('id')
                    ->map(function ($items) {
                        return $items->pluck('rombongan_belajar')->implode(', ');
                    });

        // Pass data to the view
        return view('mapel.index', compact('kelasOptions', 'kelas', 'semesters', 'gurus', 'mapels', 'rombel', 'listMapel'));
    }

    

    public function hapusMapel($mapelId)
    {
        $mapel = Mapel::findOrFail($mapelId);
        $mapel->delete();

        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'nama' => 'required|string|max:255', // Ensure 'nama' is a required string with a maximum length
            'kelas' => 'required',
            'kelas.*' => 'required|in:7,8,9', // Ensure 'kelas' is an array with values 7, 8, or 9
            'guru_id' => 'required_unless:parent_id,null|exists:gurus,id', // Ensure 'guru_id' exists in the gurus table
            'semester_id' => 'required|exists:semesters,id', // Ensure 'semester_id' exists in the semesters table,
            'parent_id' => 'nullable|prohibited_if:guru_id,null|exists:mapels,id'
        ]);
        
        
        // Create a new Mata Pelajaran (Mapel) using the validated data
        $mapel = Mapel::create([
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'guru_id' => $request->guru_id,
            'semester_id' => $request->semester_id,
            'parent' => $request->parent_id
        ]);

        if ($request->parent_id) {
            $mapelParent = Mapel::findOrFail($request->parent_id);
            $mapel->kelas()->sync($mapelParent->kelas()->get());
        }

        $komentarCK = KomentarCK::create([
            'komentar_tengah_semester' => null,
            'komentar_akhir_semester' => null,
            'mapel_id' => $mapel->id
        ]);

        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran berhasil ditambahkan!'); // Redirect with success message
    }

    public function assignKelasToMapel(Request $request, $mapelId)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);
    
        // Find the mapel
        $mapel = Mapel::findOrFail($mapelId);
    
        // $mapel->kelas()->syncWithoutDetaching($request->kelas_id);
        $mapel->kelas()->sync($request->kelas_id);

        if (!$mapel->guru_id) {
            $mapelChild = Mapel::where('parent', '=', $mapel->id)->get();

            foreach ($mapelChild as $child) {
                $child->kelas()->sync($request->kelas_id);
            }
        }
    
        return redirect()->route('mapel.index')->with('success', 'Kelas berhasil ditambahkan ke Mata Pelajaran');
    }

    public function getMapelBySemester(Request $request) {
        $mapel = Mapel::where('semester_id', $request->semester_id)->whereNull('parent')->get()->pluck('id','nama')->toArray();

        return response()->json([
            'data' => $mapel
        ], 200);
    }
}
