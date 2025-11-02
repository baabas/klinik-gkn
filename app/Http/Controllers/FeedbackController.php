<?php

namespace App\Http\Controllers;

use App\Models\FeedbackPasien;
use App\Models\RekamMedis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * Menampilkan form feedback untuk tablet perawat.
     * Tablet akan otomatis mendeteksi pasien yang baru selesai pemeriksaan.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function showFeedbackForm(Request $request)
    {
        // Ambil parameter lokasi dari query string (contoh: ?lokasi=1)
        $idLokasi = $request->query('lokasi');
        
        // Validasi: id_lokasi harus diisi
        if (!$idLokasi) {
            abort(400, 'Parameter lokasi harus diisi. Contoh: /feedback/form?lokasi=1');
        }
        
        // Validasi: cek apakah lokasi valid
        $lokasi = \App\Models\LokasiKlinik::find($idLokasi);
        if (!$lokasi) {
            abort(404, 'Lokasi klinik tidak ditemukan.');
        }
        
        // Ambil rekam medis yang baru disimpan hari ini dan belum ada feedback
        // FILTER BERDASARKAN LOKASI DOKTER (melalui relasi)
        $pendingFeedback = RekamMedis::whereDate('tanggal_kunjungan', today())
            ->whereDoesntHave('feedback')
            ->whereHas('dokter', function($query) use ($idLokasi) {
                $query->where('id_lokasi', $idLokasi); // <-- FILTER via relasi dokter
            })
            ->with(['pasien.karyawan', 'pasienNonKaryawan', 'dokter'])
            ->orderBy('created_at', 'desc')
            ->first();

        return view('feedback.form', compact('pendingFeedback', 'lokasi'));
    }

    /**
     * API untuk mengecek apakah ada rekam medis baru yang perlu feedback.
     * Endpoint ini dipanggil via AJAX polling setiap 5 detik dari tablet.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPendingFeedback(Request $request)
    {
        // Ambil parameter lokasi dari query string
        $idLokasi = $request->query('lokasi');
        
        // Validasi: id_lokasi harus diisi
        if (!$idLokasi) {
            return response()->json([
                'error' => true,
                'message' => 'Parameter lokasi harus diisi.'
            ], 400);
        }
        
        // Cari rekam medis hari ini yang belum ada feedbacknya
        // FILTER BERDASARKAN LOKASI DOKTER (melalui relasi)
        $pending = RekamMedis::whereDate('tanggal_kunjungan', today())
            ->whereDoesntHave('feedback')
            ->whereHas('dokter', function($query) use ($idLokasi) {
                $query->where('id_lokasi', $idLokasi); // <-- FILTER via relasi dokter
            })
            ->with(['pasien.karyawan', 'pasienNonKaryawan.user'])
            ->orderBy('created_at', 'desc')
            ->first();

        if ($pending) {
            $pasienData = null;
            $namaPasien = null;

            // KARYAWAN: Ambil nama dari users atau karyawan
            if ($pending->nip_pasien) {
                // Priority 1: Dari users.nama_karyawan
                if ($pending->pasien && !empty($pending->pasien->nama_karyawan)) {
                    $namaPasien = $pending->pasien->nama_karyawan;
                }
                // Priority 2: Dari karyawan.nama_karyawan
                elseif ($pending->pasien && $pending->pasien->karyawan && !empty($pending->pasien->karyawan->nama_karyawan)) {
                    $namaPasien = $pending->pasien->karyawan->nama_karyawan;
                }

                $pasienData = [
                    'identifier' => $pending->nip_pasien,
                    'nama' => $namaPasien ?: 'NIP: ' . $pending->nip_pasien,
                    'type' => 'karyawan'
                ];
            }
            // NON-KARYAWAN: Ambil nama dari users via relasi
            elseif ($pending->nik_pasien) {
                if ($pending->pasienNonKaryawan && $pending->pasienNonKaryawan->user && !empty($pending->pasienNonKaryawan->user->nama_karyawan)) {
                    $namaPasien = $pending->pasienNonKaryawan->user->nama_karyawan;
                }

                $pasienData = [
                    'identifier' => $pending->nik_pasien,
                    'nama' => $namaPasien ?: 'NIK: ' . substr($pending->nik_pasien, 0, 10) . '...',
                    'type' => 'non-karyawan'
                ];
            }

            return response()->json([
                'has_pending' => true,
                'rekam_medis' => [
                    'id' => $pending->id_rekam_medis,
                    'no_rekam_medis' => $pending->no_rekam_medis ?? 'RM-' . $pending->id_rekam_medis,
                    'pasien' => $pasienData,
                    'tanggal' => $pending->tanggal_kunjungan->format('d/m/Y H:i')
                ]
            ]);
        }

        return response()->json(['has_pending' => false]);
    }

    /**
     * Menyimpan feedback dari tablet.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id_rekam_medis' => 'required|exists:rekam_medis,id_rekam_medis',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ], [
            'id_rekam_medis.required' => 'ID Rekam Medis harus diisi.',
            'id_rekam_medis.exists' => 'Rekam Medis tidak ditemukan.',
            'rating.required' => 'Rating harus dipilih.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 1.',
            'rating.max' => 'Rating maksimal 5.',
            'komentar.max' => 'Komentar maksimal 1000 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Ambil data rekam medis
            $rekamMedis = RekamMedis::findOrFail($request->id_rekam_medis);

            // Cek apakah sudah ada feedback untuk rekam medis ini
            $existingFeedback = FeedbackPasien::where('id_rekam_medis', $rekamMedis->id_rekam_medis)->first();
            if ($existingFeedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'Feedback untuk rekam medis ini sudah pernah diberikan.'
                ], 409);
            }

            // Simpan feedback
            $feedback = FeedbackPasien::create([
                'id_rekam_medis' => $rekamMedis->id_rekam_medis,
                'nip_pasien' => $rekamMedis->nip_pasien,
                'nik_pasien' => $rekamMedis->nik_pasien,
                'rating' => $request->rating,
                'komentar' => $request->komentar,
                'waktu_feedback' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas feedback Anda!',
                'data' => [
                    'id_feedback' => $feedback->id_feedback,
                    'rating' => $feedback->rating,
                    'emoji' => $feedback->rating_emoji,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan feedback: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan laporan feedback untuk dashboard pengadaan.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Query dasar dengan relasi
        $query = FeedbackPasien::with([
            'rekamMedis.pasien.karyawan',
            'rekamMedis.pasienNonKaryawan',
            'rekamMedis.dokter.karyawan'
        ])->orderBy('waktu_feedback', 'desc');

        // Filter berdasarkan tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate('waktu_feedback', '>=', $request->start_date);
        }

        // Filter berdasarkan tanggal akhir
        if ($request->filled('end_date')) {
            $query->whereDate('waktu_feedback', '<=', $request->end_date);
        }

        // Filter berdasarkan rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Pagination
        $feedbacks = $query->paginate(20)->withQueryString();

        // Statistik feedback
        $statsQuery = FeedbackPasien::query();
        
        // Terapkan filter tanggal pada statistik juga
        if ($request->filled('start_date')) {
            $statsQuery->whereDate('waktu_feedback', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $statsQuery->whereDate('waktu_feedback', '<=', $request->end_date);
        }

        $stats = [
            'total' => $statsQuery->count(),
            'rata_rata' => round($statsQuery->avg('rating'), 2),
            'sangat_puas' => (clone $statsQuery)->where('rating', 5)->count(),
            'puas' => (clone $statsQuery)->where('rating', 4)->count(),
            'cukup' => (clone $statsQuery)->where('rating', 3)->count(),
            'tidak_puas' => (clone $statsQuery)->where('rating', 2)->count(),
            'sangat_tidak_puas' => (clone $statsQuery)->where('rating', 1)->count(),
        ];

        // Hitung persentase
        if ($stats['total'] > 0) {
            $stats['persentase'] = [
                'sangat_puas' => round(($stats['sangat_puas'] / $stats['total']) * 100, 1),
                'puas' => round(($stats['puas'] / $stats['total']) * 100, 1),
                'cukup' => round(($stats['cukup'] / $stats['total']) * 100, 1),
                'tidak_puas' => round(($stats['tidak_puas'] / $stats['total']) * 100, 1),
                'sangat_tidak_puas' => round(($stats['sangat_tidak_puas'] / $stats['total']) * 100, 1),
            ];
        } else {
            $stats['persentase'] = [
                'sangat_puas' => 0,
                'puas' => 0,
                'cukup' => 0,
                'tidak_puas' => 0,
                'sangat_tidak_puas' => 0,
            ];
        }

        return view('feedback.index', compact('feedbacks', 'stats'));
    }

    /**
     * Menampilkan detail feedback.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $feedback = FeedbackPasien::with([
            'rekamMedis.pasien.karyawan',
            'rekamMedis.pasienNonKaryawan',
            'rekamMedis.dokter.karyawan',
            'rekamMedis.detailDiagnosa.penyakit',
            'rekamMedis.resepObat.obat'
        ])->findOrFail($id);

        return view('feedback.show', compact('feedback'));
    }
}
