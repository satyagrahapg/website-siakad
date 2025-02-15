<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SemesterSelectionController extends Controller
{
    public function selectSemester(Request $request)
    {
        // Store selected semesterId in the session
        // $request->session()->put('semester_id', $request->input('semester_id'));
        session(['semester_id' => $request->input('semester_id')]);
        // Redirect back to the previous page or to a specific route
        return redirect()->route('home');
    }
}
