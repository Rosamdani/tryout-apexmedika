<?php

namespace App\Http\Controllers;

use App\Models\UserTryouts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TryoutHasilController extends Controller
{
    public function index($id)
    {
        try {
            $userTryout = UserTryouts::where('tryout_id', $id)->where('user_id', Auth::user()->id)->first();
            $totalUser = UserTryouts::where('tryout_id', $id)->where('status', 'finished')->count();
            $userTryoutRank = UserTryouts::where('tryout_id', $id)
                ->orderBy('nilai', 'DESC')
                ->pluck('nilai')
                ->search($userTryout->nilai) + 1;

            if (!$userTryout) {
                abort(404);
            }

            $status_lulus = $userTryout->nilai >= 60 ? 'Lulus' : 'Tidak Lulus';
            return view('tryouts.hasil', compact('userTryout', 'status_lulus', 'totalUser', 'userTryoutRank'));
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan ']);
        }
    }

    public function perangkingan($id)
    {
        $userTryout = UserTryouts::with('tryout')->where('tryout_id', $id)->where('user_id', Auth::user()->id)->first();
        $totalUser = UserTryouts::where('tryout_id', $id)->where('status', 'finished')->count();
        $allUserRank = UserTryouts::with('user')->where('tryout_id', $id)
            ->where('status', 'finished')
            ->orderBy('nilai', 'DESC') // Urutkan berdasarkan nilai tertinggi
            ->limit(10)
            ->get();

        // Hitung ranking
        $allUserRank->transform(function ($user, $index) {
            $user->rank = $index + 1; // Ranking mulai dari 1
            $user->status_lulus = $user->nilai >= 60 ? 'Lulus' : 'Tidak Lulus';
            return $user;
        });



        return view('tryouts.perangkingan', compact('userTryout', 'allUserRank', 'totalUser'));
    }

    public function getKompetensiAnswer(Request $request)
    {
        try {
            $request->validate([
                'tryout_id' => 'required|exists:tryouts,id',
            ]);

            $tryoutId = $request->input('tryout_id');
            $userId = Auth::user()->id;

            $data = DB::table('soal_tryouts')
                ->select(
                    'soal_tryouts.bidang_id',
                    'soal_tryouts.kompetensi_id',
                    'bidang_tryouts.nama as kategori', // Nama kategori (Bidang)
                    'kompetensi_tryouts.nama as kompetensi', // Nama kompetensi
                    DB::raw('COUNT(soal_tryouts.id) as total_soal'), // Total soal
                    DB::raw('COUNT(CASE WHEN user_answers.jawaban = soal_tryouts.jawaban THEN 1 END) as benar'), // Jumlah soal yang dijawab benar
                    DB::raw('COUNT(CASE WHEN user_answers.jawaban != soal_tryouts.jawaban AND user_answers.jawaban IS NOT NULL THEN 1 END) as salah'), // Jumlah soal yang dijawab salah
                    DB::raw('COUNT(CASE WHEN user_answers.jawaban IS NULL THEN 1 END) as tidak_dikerjakan') // Jumlah soal yang tidak dikerjakan
                )
                ->leftJoin('bidang_tryouts', 'soal_tryouts.bidang_id', '=', 'bidang_tryouts.id')
                ->leftJoin('kompetensi_tryouts', 'soal_tryouts.kompetensi_id', '=', 'kompetensi_tryouts.id')
                ->leftJoin('user_answers', function ($join) use ($userId) {
                    $join->on('soal_tryouts.id', '=', 'user_answers.soal_id')
                        ->where('user_answers.user_id', '=', $userId);
                })
                ->where('soal_tryouts.tryout_id', $tryoutId)
                ->groupBy('soal_tryouts.bidang_id', 'soal_tryouts.kompetensi_id', 'bidang_tryouts.nama', 'kompetensi_tryouts.nama')
                ->get();

            // Data yang sudah dikelompokkan berdasarkan kategori (bidang)
            $groupedByCategory = $data->groupBy('kategori');

            // Format data untuk hasil yang diinginkan
            $formattedData = [
                'status' => 'success',
                'message' => 'Data berhasil diambil',
                'data' => [
                    'bidang' => [],
                    'kompetensi' => []
                ]
            ];

            // Kelompokkan data berdasarkan kategori
            foreach ($groupedByCategory as $kategori => $items) {
                $formattedData['data']['bidang'][$kategori] = $items;

                // Kelompokkan dalam kategori berdasarkan kompetensi
                foreach ($items as $item) {
                    $kompetensi = $item->kompetensi;

                    if (!isset($formattedData['data']['kompetensi'][$kompetensi])) {
                        $formattedData['data']['kompetensi'][$kompetensi] = [];
                    }

                    $formattedData['data']['kompetensi'][$kompetensi][] = $item;
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Data berhasil diambil', 'data' => $formattedData]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan ' . $e->getMessage()]);
        }
    }
    // Mengambil data soal berdasarkan tryout, bidang, dan kompetensi, dengan perhitungan soal yang dijawab benar, salah, dan tidak dikerjakan
}
