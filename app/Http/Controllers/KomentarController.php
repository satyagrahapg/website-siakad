<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KomentarCK;
use App\Models\Mapel;

class KomentarController extends Controller //INI GA DIPAKE, BEKAS DULU KOMENTAR CK RAPOR
{
    public function index($mapelId){

        $komentarCK = KomentarCK::where('mapel_id', $mapelId)->first();

        $mapel = Mapel::findOrFail($mapelId); 
        return view('komentarck.index', compact('komentarCK', 'mapelId', 'mapel'));
 
    }
    
    public function updateKomentar(Request $request, $mapelId)
    {
        // Validate incoming request data
        $request->validate([
            'komentar_tengah_semester' => 'required|string',
            'komentar_akhir_semester' => 'required|string'
        ]);

        // Find the komentar record based on mapelId
        $komentarCK = KomentarCK::where('mapel_id', $mapelId)->first();

        // If the record exists, update it
        if ($komentarCK) {
            $komentarCK->komentar_tengah_semester = $request->komentar_tengah_semester;
            $komentarCK->komentar_akhir_semester = $request->komentar_akhir_semester;
            $komentarCK->save();
            
            return redirect()->back()->with('success', 'Komentar updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Komentar not found!');
        }
    }
}
