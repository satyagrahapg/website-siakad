<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::all();
        return view('semester.index', compact('semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'status' => 'required|integer',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        // If the status is 1, set other semesters' status to 0
        if ($request->status == 1) {
            Semester::where('status', 1)->update(['status' => 0]);
        }

        Semester::create($request->all());

        return redirect()->route('semesters.index')->with('success', 'Semester berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'semester' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'status' => 'required|integer',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        // If the status is 1, set other semesters' status to 0
        if ($request->status == 1) {
            Semester::where('status', 1)->where('id', '!=', $id)->update(['status' => 0]);
        }

        $semester = Semester::findOrFail($id);
        $semester->update($request->all());

        return redirect()->route('semesters.index')->with('success', 'Semester berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Semester::find($id)->delete();
        return redirect()->route('semesters.index')->with('success', 'Semester berhasil dihapus!');
    }
}
