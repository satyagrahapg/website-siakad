<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KalenderAkademik;
use Illuminate\Http\JsonResponse;

class KalenderAkademikController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        return view('kalenderakademik.index');
    }

    public function listEvent(Request $request)
    {
        $start = $request->start ? date('Y-m-d', strtotime($request->start)) : now()->startOfMonth()->toDateString();
        $end = $request->end ? date('Y-m-d', strtotime($request->end)) : now()->endOfMonth()->toDateString();

        $events = KalenderAkademik::where('start', '>=', $start)
        ->where('end', '<=' , $end)->get()
        ->map( fn ($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'start' => $item->start,
            'end' => $item->end,
            'tipe_kegiatan' => $item->tipe_kegiatan
        ]);

        return response()->json($events);
    }

    /**
     * Handle AJAX requests for adding, updating, and deleting events
     *
     * @return JsonResponse
     */
    public function ajax(Request $request): JsonResponse
    {
        switch ($request->type) {
            case 'add':
                $event = KalenderAkademik::create([
                    'title' => $request->title,
                    'tipe_kegiatan' => $request->tipe_kegiatan,
                    'start' => $request->start,
                    'end' => $request->end,
                ]);

                return response()->json($event);

            case 'update':
                $event = KalenderAkademik::find($request->id);
                if ($event) {
                    $event->update([
                        'title' => $request->title,
                        'tipe_kegiatan' => $request->tipe_kegiatan,
                        'start' => $request->start,
                        'end' => $request->end,
                    ]);
                }

                return response()->json($event);

            case 'delete':
                $event = KalenderAkademik::find($request->id);
                if ($event) {
                    $event->delete();
                }

                return response()->json($event);

            default:
                return response()->json(['error' => 'Invalid request type'], 400);
        }
    }
}
