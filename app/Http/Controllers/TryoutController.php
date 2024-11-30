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
            $tryouts = Tryouts::select(['tryouts.id', 'tryouts.nama', 'tryouts.tanggal', 'user_tryouts.status as user_status'])
                ->leftJoin('user_tryouts', function ($join) {
                    $join->on('tryouts.id', '=', 'user_tryouts.tryout_id')
                        ->where('user_tryouts.user_id', '=', Auth::id());
                })
                ->where('tryouts.batch_id', $request->batch)
                ->get()
                ->map(function ($tryout) {
                    // Tentukan status, jika user_tryouts.status null, set ke 'not_started'
                    $tryout->status = $tryout->user_status ?? 'not_started'; // Default jika tidak ditemukan atau null
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
            UserTryouts::updateOrCreate(
                [
                    'tryout_id' => $tryout->id,
                    'user_id' => Auth::user()->id
                ],
                [
                    'status' => 'started'
                ]
            );

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
            $soal = SoalTryout::with('userAnswer')->select(['id', 'nomor', 'soal', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'pilihan_e'])->where('tryout_id', $request->id_tryout)
                ->orderBy('nomor', 'asc')
                ->get();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil diambil', 'data' => $soal]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan ' . $e->getMessage()]);
        }
    }

    public function saveAnswer(Request $request)
    {
        try {
            UserAnswer::updateOrCreate(
                ['user_id' => Auth::user()->id, 'soal_id' => $request->id],
                ['jawaban' => $request->pilihan, 'status' => $request->status]
            );


            return response()->json(['message' => 'Answer saved successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Failed to save answer. ' . $th->getMessage()], 500);
        }
    }

    public function saveTimeLeft(Request $request)
    {
        $request->validate([
            'id_tryout' => 'required|exists:tryouts,id',
            'sisa_waktu' => 'required|numeric',
        ]);

        // Temukan UserTryouts berdasarkan id_tryout dan user_id
        $tryout = UserTime::where('tryout_id', $request->id_tryout)
            ->where('user_id', Auth::user()->id)
            ->first();

        if (!$tryout) {
            return response()->json(['status' => 'error', 'message' => 'User tryout not found'], 404);
        }

        $tryout->sisa_waktu = $request->sisa_waktu;
        $tryout->save();

        return response()->json(['status' => 'success', 'message' => 'Time updated successfully']);
    }

    public function getTimeLeft(Request $request)
    {
        $request->validate([
            'id_tryout' => 'required|exists:tryouts,id',
        ]);

        $user_tryout = UserTime::where('user_id', Auth::user()->id)
            ->where('tryout_id', $request->id_tryout)
            ->first();

        if (!$user_tryout) {
            $tryout = Tryouts::find($request->id_tryout);

            if (!$tryout) {
                return response()->json(['status' => 'error', 'message' => 'Tryout not found'], 404);
            }

            $user_tryout = new UserTime();
            $user_tryout->user_id = Auth::user()->id;
            $user_tryout->tryout_id = $request->id_tryout;
            $user_tryout->sisa_waktu = $tryout->waktu;
            $user_tryout->save();

            return response()->json(['status' => 'success', 'message' => 'User tryout created successfully', 'sisa_waktu' => $user_tryout->sisa_waktu]);
        }

        return response()->json(['status' => 'success', 'message' => 'Timeleft get successfully', 'sisa_waktu' => $user_tryout->sisa_waktu]);
    }
}
