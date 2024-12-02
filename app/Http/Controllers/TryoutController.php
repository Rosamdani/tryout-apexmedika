<?php

namespace App\Http\Controllers;

use App\Models\BatchTryouts;
use App\Models\SoalTryout;
use App\Models\Tryouts;
use App\Models\UserAnswer;
use App\Models\UserTime;
use App\Models\UserTryouts;
use Carbon\Carbon;
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

    public function pausedTime(Request $request)
    {
        try {
            $request->validate([
                'id_tryout' => 'required|exists:tryouts,id',
            ]);
            $user_time = UserTime::updateOrCreate([
                'user_id' => Auth::user()->id,
                'tryout_id' => $request->id_tryout,
            ], [
                'sisa_waktu' => $request->sisa_waktu,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Sisa waktu updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan ' . $e->getMessage()]);
        }
    }

    public function getTimeLeft(Request $request)
    {
        try {
            $user_time = UserTime::where('user_id', Auth::user()->id)
                ->where('tryout_id', $request->id_tryout)
                ->first();
            if ($user_time) {
                return response()->json(['status' => 'success', 'data' => $user_time->sisa_waktu]);
            } else {
                $tryout = Tryouts::findOrFail($request->id_tryout);
                $user_time = UserTime::create([
                    'user_id' => Auth::user()->id,
                    'tryout_id' => $request->id_tryout,
                    'sisa_waktu' => $tryout->waktu,
                ]);
                return response()->json(['status' => 'success', 'data' => $user_time->sisa_waktu]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan ']);
        }
    }

    public function saveEndTime(Request $request)
    {
        try {
            $request->validate([
                'id_tryout' => 'required|exists:tryouts,id',
                'waktu_habis' => 'required',
            ]);
            $waktu_habis = Carbon::parse($request->waktu_habis)->format('Y-m-d H:i:s');


            UserTime::updateOrCreate([
                'user_id' => Auth::user()->id,
                'tryout_id' => $request->id_tryout,
            ], [
                'waktu_habis' => $request->waktu_habis,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Waktu habis updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan ' . $e->getMessage()]);
        }
    }

    public function getEndTime(Request $request)
    {
        $user_time = UserTime::where('user_id', Auth::user()->id)
            ->where('tryout_id', $request->id_tryout)
            ->first();
        if ($user_time) {
            return response()->json(['status' => 'success', 'data' => $user_time->waktu_habis]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'User time not found']);
        }
    }

    public function saveTimeLeft(Request $request)
    {
        $userTryout = UserTime::where('user_id', Auth::user()->id)
            ->where('tryout_id', $request->id_tryout)
            ->first();

        if (!$userTryout) {
            return response()->json(['status' => 'error', 'message' => 'Tryout tidak ditemukan']);
        }

        $userTryout->sisa_waktu = $request->sisa_waktu;
        $userTryout->save();

        return response()->json(['status' => 'success', 'message' => 'Waktu tersimpan']);
    }

    public function finishTryout(Request $request)
    {
        try {
            $user_time = UserTime::where('user_id', Auth::user()->id)
                ->where('tryout_id', $request->id_tryout)
                ->first();
            if ($user_time) {
                $user_time->delete();
                $user_tryout = UserTryouts::where('user_id', Auth::user()->id)
                    ->where('tryout_id', $request->id_tryout)
                    ->update(['status' => 'finished', 'nilai' => $this->calculateScore($request->id_tryout)]);
                return response()->json(['status' => 'success', 'message' => 'Tryout finished successfully', 'data' => $user_tryout]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'User time not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan ' . $e->getMessage()]);
        }
    }

    public function calculateScore($tryoutId)
    {
        try {
            // Ambil data tryout berdasarkan ID
            $tryout = Tryouts::with('questions')->findOrFail($tryoutId);

            // Ambil daftar soal dalam tryout
            $questions = $tryout->questions;
            $totalQuestions = $questions->count();

            // Ambil jawaban pengguna untuk soal dalam tryout
            $userAnswers = UserAnswer::whereIn('soal_id', $questions->pluck('id'))
                ->where('user_id', Auth::id()) // Menggunakan Auth::id() untuk user yang sedang login
                ->get();

            // Hitung jawaban benar
            $correctAnswers = 0;

            foreach ($questions as $question) {
                $userAnswer = $userAnswers->firstWhere('soal_id', $question->id);

                if ($userAnswer && $userAnswer->jawaban === $question->jawaban) {
                    $correctAnswers++;
                }
            }

            // Hitung skor sebagai persentase
            $score = number_format($totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0, 2, '.', ',');

            return  $score;
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
