<?php

namespace App\Http\Controllers;

use App\Models\BatchTryouts;
use App\Models\SoalTryout;
use App\Models\Tryouts;
use App\Models\UserAnswer;
use App\Models\UserTime;
use App\Models\UserTryouts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TryoutController extends Controller
{
    public function index()
    {
        $batches = BatchTryouts::all();
        return view('tryouts.index', compact('batches'));
    }

    public function getTryouts(Request $request)
    {
        try {
            $batch_end_date = BatchTryouts::find($request->batch)->end_date;
            $tryouts = Tryouts::select(['tryouts.id', 'tryouts.nama', 'tryouts.tanggal'])
                ->leftJoin('user_tryouts', function ($join) {
                    $join->on('tryouts.id', '=', 'user_tryouts.tryout_id')
                        ->where('user_tryouts.user_id', '=', Auth::id());
                })
                ->where('tryouts.batch_id', $request->batch)
                ->get()
                ->map(function ($tryout) {
                    // Tambahkan status berdasarkan data dari user_tryouts
                    $tryout->status = $tryout->status ?? 'belum_dikerjakan'; // Default jika tidak ditemukan
                    return $tryout;
                })
                ->groupBy('status'); // Kelompokkan berdasarkan status

            return response()->json(['status' => 'success', 'message' => 'Data berhasil diambil', 'data' => $tryouts, 'batch_end_date' => $batch_end_date]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan']);
        }
    }

    public function show($id)
    {
        try {
            $tryout = Tryouts::findOrFail($id);
            return view('tryouts.tryout-tes', compact('tryout'));
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan']);
        }
    }
    public function hasil($id)
    {
        try {
            $tryout = Tryouts::findOrFail($id);
            return view('tryouts.hasil', compact('tryout'));
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan']);
        }
    }


    public function getQuestions(Request $request)
    {
        try {
            $soal = SoalTryout::select(['id', 'nomor', 'soal', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'pilihan_e'])->where('tryout_id', $request->id_tryout)
                ->orderBy('nomor', 'asc')
                ->get();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil diambil', 'data' => $soal]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan']);
        }
    }

    public function saveAnswer(Request $request)
    {
        try {
            UserAnswer::updateOrCreate(
                ['user_id' => Auth::user()->id, 'soal_id' => $request->id],
                ['jawaban' => $request->pilihan, 'status' => 'sudah_dijawab']
            );

            return response()->json(['message' => 'Answer saved successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Failed to save answer.'], 500);
        }
    }

    public function saveEndTime(Request $request)
    {
        $request->validate([
            'id_tryout' => 'required|exists:tryouts,id',
            'end_time' => 'required|date',
        ]);

        // Temukan UserTryouts berdasarkan id_tryout dan user_id
        $tryout = UserTime::where('tryout_id', $request->id_tryout)
            ->where('user_id', Auth::user()->id)
            ->first();

        if (!$tryout) {
            return response()->json(['status' => 'error', 'message' => 'User tryout not found'], 404);
        }

        // Jika end_time masih null, simpan end_time baru
        if ($tryout->end_time === null) {
            $tryout->end_time = $request->end_time;
            $tryout->save();

            return response()->json(['status' => 'success', 'message' => 'End time saved successfully']);
        }

        return response()->json(['status' => 'error', 'message' => 'End time already set'], 400);
    }

    public function getEndTime(Request $request)
    {
        $request->validate([
            'id_tryout' => 'required|exists:tryouts,id',
        ]);

        $user_tryout = UserTime::where('user_id', Auth::user()->id)
            ->where('tryout_id', $request->id_tryout)
            ->first();

        if (!$user_tryout) {
            return response()->json(['status' => 'error', 'message' => 'User tryout not found'], 404);
        }

        return response()->json(['end_time' => $user_tryout->end_time]);
    }
}
