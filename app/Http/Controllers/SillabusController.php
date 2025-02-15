<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CP;
use App\Models\TP;
use App\Models\Mapel;

class SillabusController extends Controller
{
    //Ini fungsi" untuk CRUD CP //

    public function index($mapelId)
    {
        $cps = CP::where('mapel_id', $mapelId)->get();
        $mapel = Mapel::findOrFail($mapelId); 
    
        return view('silabus.index', compact('cps', 'mapelId', 'mapel'));
    }

    public function storeCP(Request $request, $mapelId)
    {
        $request->validate([
            'nomor'=>  'required|integer',
            'nama' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255'
        ]);
    
        $cp = CP::create([
            'nomor'=>  $request->nomor,
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'mapel_id' => $mapelId,
        ]);
    
        return redirect()->route('silabus.index', $mapelId)->with('success', 'CP berhasil ditambahkan!');
    }
    
    public function updateCP(Request $request, $mapelId, $cpId)
    {
        // Validate the incoming request data
        $request->validate([
            'nomor'=>  'required|integer',
            'nama' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255'
        ]);

        // Find the CP record by its ID
        $cp = CP::findOrFail($cpId);

        // Update the CP record
        $cp->nomor = $request->input('nomor');
        $cp->nama = $request->input('nama');
        $cp->keterangan = $request->input('keterangan');
        $cp->save();

        // Redirect with success message
        return redirect()->route('silabus.index', [$mapelId])->with('success', 'Silabus berhasil diperbarui.');
    }

    public function deleteCP($mapelId, $cpId)
    {
        // Find the CP record by its ID
        $cp = CP::findOrFail($cpId);

        // Delete the CP record
        $cp->delete();

        // Redirect with success message
        return redirect()->route('silabus.index', [$mapelId])->with('success', 'Silabus berhasil dihapus.');
    }

    // End of CP's function codes //

    //Codes for TP's starts here yak//

    public function bukaTP($mapelId, $cpId)
    {
        $tps = TP::where('cp_id', $cpId)->get(); // Add `->get()` to retrieve the collection
        $cps = CP::findOrFail($cpId);
        $mapel = Mapel::findOrFail($mapelId); 

        return view('silabus.buka', compact('tps', 'cps', 'mapelId', 'cpId', 'mapel')); // Pass additional IDs if needed
    }

    // Store TP under a CP
    public function storeTP(Request $request, $mapelId, $cpId)
    {
        $request->validate([
            'nomor'=>  'required|integer',
            'nama' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255'
        ]);
    
        TP::create([
            'nomor'=>  $request->nomor,
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'cp_id' => $cpId,
        ]);
    
        return redirect()->back()->with('success', 'TP berhasil ditambahkan!');
    }
    
    // Update TP
    public function updateTP(Request $request, $mapelId, $cpId, $tpId)
    {
        $request->validate([
            'nomor'=>  'required|integer',
            'nama' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255'
        ]);
    
        $tp = TP::findOrFail($tpId);
        $tp->nomor = $request->input('nomor');
        $tp->nama = $request->input('nama');
        $tp->keterangan = $request->input('keterangan');
        $tp->save();
    
        return redirect()->back()->with('success', 'TP berhasil diperbarui.');
    }
    
    // Delete TP
    public function deleteTP($mapelId, $cpId, $tpId)
    {
        $tp = TP::findOrFail($tpId);
        $tp->delete();

        return redirect()->back()->with('success', 'TP berhasil dihapus.');
    }
}
