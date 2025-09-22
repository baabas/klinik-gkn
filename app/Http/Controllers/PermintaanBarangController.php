<?php

namespace App\Http\Controllers;

use App\Models\BarangKemasan;
use App\Models\BarangMedis;
use App\Models\PermintaanBarang;
use App\Models\StokBarang;
use App\Models\StokHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PermintaanBarangController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $search = $request->input('search');
        $status = $request->input('status');

        $query = PermintaanBarang::with(['peminta', 'lokasi'])
            ->latest('tanggal')
            ->search($search)
            ->status($status);

        if ($user->hasRole('DOKTER') && $user->id_lokasi) {
            $query->where('lokasi_id', $user->id_lokasi);
        }

        /** @var LengthAwarePaginator $permintaan */
        $permintaan = $query->paginate(15)->withQueryString();

        return view('permintaan.index', [
            'permintaan' => $permintaan,
            'statusOptions' => PermintaanBarang::statusOptions(),
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorizeDoctor();

        return view('permintaan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeDoctor();

        $user = Auth::user();

        if (! $user->id_lokasi) {
            return back()->withInput()->with('error', 'Lokasi dokter belum diatur. Hubungi administrator.');
        }

        $validated = $this->validatePermintaan($request);

        $permintaan = DB::transaction(function () use ($validated, $user) {

            $permintaan = PermintaanBarang::create([
                'kode' => PermintaanBarang::generateKode(),
                'tanggal' => $validated['tanggal'],
                'catatan' => $validated['catatan'] ?? null,
                'status' => PermintaanBarang::STATUS_DRAFT,
                'peminta_id' => $user->id,
                'lokasi_id' => $user->id_lokasi,
            ]);

            $this->syncDetails($permintaan, $validated['details_registered'], $validated['details_new']);

            return $permintaan;
        });

        return redirect()
            ->route('permintaan.show', $permintaan)
            ->with('success', 'Permintaan barang berhasil dibuat sebagai draft.');
    }

    public function show(PermintaanBarang $permintaan): View
    {
        $permintaan->load(['peminta', 'lokasi', 'details.barang', 'details.kemasan']);

        return view('permintaan.show', [
            'permintaan' => $permintaan,
        ]);
    }

    public function edit(PermintaanBarang $permintaan): View
    {
        $this->authorizeDoctor();

        if (! $permintaan->isDraft() || $permintaan->peminta_id !== Auth::id()) {
            abort(403);
        }

        $permintaan->load(['details.barang', 'details.kemasan']);

        return view('permintaan.edit', [
            'permintaan' => $permintaan,
        ]);
    }

    public function update(Request $request, PermintaanBarang $permintaan): RedirectResponse
    {
        $this->authorizeDoctor();

        if (! $permintaan->isDraft() || $permintaan->peminta_id !== Auth::id()) {
            abort(403);
        }

        $validated = $this->validatePermintaan($request);

        DB::transaction(function () use ($permintaan, $validated) {
            $permintaan->update([
                'tanggal' => $validated['tanggal'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            $permintaan->details()->delete();

            $this->syncDetails($permintaan, $validated['details_registered'], $validated['details_new']);
        });

        return redirect()
            ->route('permintaan.show', $permintaan)
            ->with('success', 'Draft permintaan berhasil diperbarui.');
    }

    public function destroy(PermintaanBarang $permintaan): RedirectResponse
    {
        $this->authorizeDoctor();

        if (! $permintaan->isDraft() || $permintaan->peminta_id !== Auth::id()) {
            abort(403);
        }

        $permintaan->delete();

        return redirect()
            ->route('permintaan.index')
            ->with('success', 'Permintaan berhasil dihapus.');
    }

    public function submit(PermintaanBarang $permintaan): RedirectResponse
    {
        $this->authorizeDoctor();

        if (! $permintaan->isDraft() || $permintaan->peminta_id !== Auth::id()) {
            abort(403);
        }

        $permintaan->update(['status' => PermintaanBarang::STATUS_DIAJUKAN]);

        return redirect()
            ->route('permintaan.show', $permintaan)
            ->with('success', 'Permintaan berhasil diajukan.');
    }

    public function approve(PermintaanBarang $permintaan): RedirectResponse
    {
        $this->authorizePengadaan();

        if (! $permintaan->isDiajukan()) {
            return back()->with('error', 'Hanya permintaan yang diajukan yang dapat disetujui.');
        }

        $permintaan->update(['status' => PermintaanBarang::STATUS_DISETUJUI]);

        return redirect()
            ->route('permintaan.show', $permintaan)
            ->with('success', 'Permintaan barang disetujui.');
    }

    public function reject(Request $request, PermintaanBarang $permintaan): RedirectResponse
    {
        $this->authorizePengadaan();

        if (! $permintaan->isDiajukan()) {
            return back()->with('error', 'Hanya permintaan yang diajukan yang dapat ditolak.');
        }

        $permintaan->update([
            'status' => PermintaanBarang::STATUS_DITOLAK,
            'catatan' => $request->filled('catatan') ? $request->string('catatan')->toString() : $permintaan->catatan,
        ]);

        return redirect()
            ->route('permintaan.show', $permintaan)
            ->with('success', 'Permintaan barang ditolak.');
    }

    public function fulfill(Request $request, PermintaanBarang $permintaan): RedirectResponse
    {
        $this->authorizePengadaan();

        if (! $permintaan->isDisetujui()) {
            return back()->with('error', 'Hanya permintaan yang sudah disetujui yang dapat dipenuhi.');
        }

        $createBaru = collect($request->input('buat_barang_baru', []))->map(fn ($value) => (int) $value)->all();

        try {
            DB::transaction(function () use ($permintaan, $createBaru) {
                $permintaan->loadMissing('details.barang', 'details.kemasan');

                foreach ($permintaan->details as $detail) {
                    if ($detail->barang_id) {
                        $barang = $detail->barang;
                        $kemasan = $detail->kemasan;
                        $jumlahKemasan = $detail->jumlah_kemasan ?? ($detail->jumlah !== null ? (int) $detail->jumlah : 0);
                        $isi = $detail->isi_per_kemasan ?? $kemasan?->isi_per_kemasan ?? 0;

                        $jumlahUnit = $detail->total_unit_dasar
                            ?? $detail->total_unit
                            ?? ($isi > 0 ? $jumlahKemasan * $isi : null);

                        if (! $jumlahUnit || $jumlahUnit <= 0) {
                            continue;
                        }

                        $stokSebelum = (int) $barang->stok;

                        if ($stokSebelum < $jumlahUnit) {
                            throw new \RuntimeException("Stok {$barang->nama_obat} tidak mencukupi.");
                        }

                        $stokLokasi = StokBarang::query()
                            ->lockForUpdate()
                            ->firstOrCreate(
                                [
                                    'id_barang' => $barang->id_obat,
                                    'id_lokasi' => $permintaan->lokasi_id,
                                ],
                                [
                                    'jumlah' => 0,
                                ],
                            );

                        $stokLokasiSebelum = (int) $stokLokasi->jumlah;
                        $stokLokasiSesudah = $stokLokasiSebelum + $jumlahUnit;

                        $detail->update([
                            'kemasan_id' => $detail->kemasan_id ?? $kemasan?->id,
                            'barang_kemasan_id' => $detail->barang_kemasan_id ?? $kemasan?->id,
                            'jumlah' => $jumlahKemasan,
                            'jumlah_kemasan' => $jumlahKemasan ?: null,
                            'isi_per_kemasan' => $isi ?: null,
                            'satuan_kemasan' => $detail->satuan_kemasan ?? $kemasan?->nama_kemasan,
                            'kemasan' => $detail->satuan_kemasan ?? $kemasan?->nama_kemasan,
                            'total_unit' => $jumlahUnit,
                            'total_unit_dasar' => $jumlahUnit,
                            'base_unit' => $detail->base_unit ?? $barang->satuan_dasar,
                        ]);

                        $stokLokasi->jumlah = $stokLokasiSesudah;
                        $stokLokasi->save();

                        $totalStokBarang = StokBarang::query()
                            ->where('id_barang', $barang->id_obat)
                            ->sum('jumlah');

                        $barang->update([
                            'stok' => $totalStokBarang,
                        ]);

                        StokHistory::create([
                            'id_barang' => $barang->id_obat,
                            'id_lokasi' => $permintaan->lokasi_id,
                            'perubahan' => $jumlahUnit,
                            'stok_sebelum' => $stokLokasiSebelum,
                            'stok_sesudah' => $stokLokasiSesudah,
                            'keterangan' => 'Pemenuhan permintaan '.$permintaan->kode,
                            'user_id' => Auth::id(),
                            'tanggal_transaksi' => now()->toDateString(),
                            'jumlah_kemasan' => $jumlahKemasan ?: null,
                            'isi_per_kemasan' => $isi ?: null,
                            'satuan_kemasan' => $detail->satuan_kemasan ?? $kemasan?->nama_kemasan,
                            'kemasan_id' => $detail->kemasan_id ?? $kemasan?->id,
                            'base_unit' => $detail->base_unit ?? $barang->satuan_dasar,
                        ]);
                    } elseif (in_array($detail->id, $createBaru, true)) {
                    $barangBaru = BarangMedis::create([
                        'kode_obat' => BarangMedis::generateKode('OBAT'),
                        'nama_obat' => $detail->nama_barang_baru,
                        'tipe' => 'OBAT',
                        'satuan_dasar' => $detail->satuan ?? 'Unit',
                        'stok' => 0,
                        'created_by' => Auth::id(),
                    ]);

                    if ($detail->kemasan) {
                        $kemasanBaru = $barangBaru->kemasanBarang()->create([
                            'nama_kemasan' => $detail->kemasan,
                            'isi_per_kemasan' => 1,
                            'is_default' => true,
                        ]);

                        $detail->update([
                            'barang_kemasan_id' => $kemasanBaru->id,
                        ]);
                    }

                    $detail->update([
                        'barang_id' => $barangBaru->id_obat,
                    ]);
                }
                }

                $permintaan->update(['status' => PermintaanBarang::STATUS_DIPENUHI]);
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('permintaan.show', $permintaan)
            ->with('success', 'Permintaan telah dipenuhi dan stok diperbarui.');
    }

    public function searchBarang(Request $request): JsonResponse
    {
        if (! Auth::check() || ! Auth::user()->hasRole(['DOKTER', 'PENGADAAN', 'ADMIN'])) {
            abort(403);
        }

        $term = $request->input('q');

        $items = BarangMedis::query()
            ->select(['id_obat', 'nama_obat', 'kode_obat', 'satuan_dasar'])
            ->when($term, function ($query) use ($term) {
                $query->where('nama_obat', 'like', "%{$term}%")
                    ->orWhere('kode_obat', 'like', "%{$term}%");
            })
            ->orderBy('nama_obat')
            ->limit(20)
            ->get()
            ->map(fn (BarangMedis $barang) => [
                'id' => $barang->id_obat,
                'text' => sprintf('%s (%s)', $barang->nama_obat, $barang->kode_obat),
                'satuan' => $barang->satuan_dasar,
            ]);

        return response()->json(['results' => $items]);
    }

    public function kemasan(BarangMedis $barang): JsonResponse
    {
        if (! Auth::check() || ! Auth::user()->hasRole(['DOKTER', 'PENGADAAN', 'ADMIN'])) {
            abort(403);
        }

        $barang->loadMissing('kemasanBarang');

        $kemasan = $barang->kemasanBarang->map(fn (BarangKemasan $kemasan) => [
            'id' => $kemasan->id,
            'text' => $kemasan->nama_kemasan,
            'isi' => $kemasan->isi_per_kemasan,
            'is_default' => $kemasan->is_default,
        ]);

        return response()->json([
            'data' => $kemasan,
            'satuan' => $barang->satuan_dasar,
        ]);
    }

    private function authorizeDoctor(): void
    {
        if (! Auth::check() || ! Auth::user()->hasRole('DOKTER')) {
            abort(403);
        }
    }

    private function authorizePengadaan(): void
    {
        if (! Auth::check() || ! Auth::user()->hasRole(['PENGADAAN', 'ADMIN'])) {
            abort(403);
        }
    }

    private function validatePermintaan(Request $request): array
    {
        $payload = $request->all();

        $payload['details_registered'] = collect($request->input('details_registered', []))
            ->filter(function ($detail) {
                $jumlah = Arr::get($detail, 'jumlah_kemasan');

                return Arr::get($detail, 'barang_id')
                    || Arr::get($detail, 'kemasan_id')
                    || ($jumlah !== null && $jumlah !== '');
            })
            ->map(function ($detail) {
                return [
                    'barang_id' => Arr::get($detail, 'barang_id'),
                    'kemasan_id' => Arr::get($detail, 'kemasan_id'),
                    'jumlah_kemasan' => Arr::get($detail, 'jumlah_kemasan'),
                    'keterangan' => Arr::get($detail, 'keterangan'),
                ];
            })
            ->values()
            ->toArray();

        $payload['details_new'] = collect($request->input('details_new', []))
            ->filter(function ($detail) {
                $jumlah = Arr::get($detail, 'jumlah');

                return Arr::get($detail, 'nama')
                    || ($jumlah !== null && $jumlah !== '')
                    || Arr::get($detail, 'satuan')
                    || Arr::get($detail, 'kemasan')
                    || Arr::get($detail, 'keterangan');
            })
            ->map(function ($detail) {
                return [
                    'nama' => Arr::get($detail, 'nama'),
                    'jumlah' => Arr::get($detail, 'jumlah'),
                    'satuan' => Arr::get($detail, 'satuan'),
                    'kemasan' => Arr::get($detail, 'kemasan'),
                    'keterangan' => Arr::get($detail, 'keterangan'),
                ];
            })
            ->values()
            ->toArray();

        $validator = Validator::make($payload, [
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
            'details_registered' => ['nullable', 'array'],
            'details_registered.*.barang_id' => ['required', 'exists:barang_medis,id_obat'],
            'details_registered.*.kemasan_id' => ['required', 'exists:barang_kemasan,id'],
            'details_registered.*.jumlah_kemasan' => ['required', 'integer', 'min:1'],
            'details_registered.*.keterangan' => ['nullable', 'string', 'max:255'],
            'details_new' => ['nullable', 'array'],
            'details_new.*.nama' => ['required', 'string', 'max:255'],
            'details_new.*.jumlah' => ['required', 'numeric', 'min:0.01'],
            'details_new.*.satuan' => ['required', 'string', 'max:50'],
            'details_new.*.kemasan' => ['nullable', 'string', 'max:150'],
            'details_new.*.keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $validator->after(function ($validator) use ($payload) {
            $registered = $payload['details_registered'] ?? [];
            $new = $payload['details_new'] ?? [];

            if (empty($registered) && empty($new)) {
                $validator->errors()->add('details_registered', 'Minimal satu detail harus ditambahkan.');
            }

            foreach ($registered as $index => $item) {
                $barangId = Arr::get($item, 'barang_id');
                $kemasanId = Arr::get($item, 'kemasan_id');

                if ($barangId && $kemasanId) {
                    $exists = BarangKemasan::where('id', $kemasanId)
                        ->where('barang_id', $barangId)
                        ->exists();

                    if (! $exists) {
                        $validator->errors()->add("details_registered.{$index}.kemasan_id", 'Kemasan tidak valid untuk barang yang dipilih.');
                    }
                }
            }
        });

        $data = $validator->validate();

        return [
            'tanggal' => $data['tanggal'],
            'catatan' => $data['catatan'] ?? null,
            'details_registered' => collect($data['details_registered'] ?? [])
                ->map(function ($detail) {
                    return [
                        'barang_id' => (int) $detail['barang_id'],
                        'kemasan_id' => (int) $detail['kemasan_id'],
                        'jumlah_kemasan' => (int) $detail['jumlah_kemasan'],
                        'keterangan' => $detail['keterangan'] ?? null,
                    ];
                })
                ->toArray(),
            'details_new' => collect($data['details_new'] ?? [])
                ->map(function ($detail) {
                    return [
                        'nama' => $detail['nama'],
                        'jumlah' => (float) $detail['jumlah'],
                        'satuan' => $detail['satuan'],
                        'kemasan' => $detail['kemasan'] ?? null,
                        'keterangan' => $detail['keterangan'] ?? null,
                    ];
                })
                ->toArray(),
        ];
    }

    private function syncDetails(PermintaanBarang $permintaan, array $registeredItems, array $newItems): void
    {
        foreach ($registeredItems as $item) {
            $barang = BarangMedis::find($item['barang_id']);
            $kemasan = BarangKemasan::find($item['kemasan_id']);

            if (! $barang || ! $kemasan) {
                continue;
            }

            $jumlah = (int) $item['jumlah_kemasan'];
            $totalUnit = $kemasan->isi_per_kemasan * $jumlah;

            $permintaan->details()->create([
                'barang_id' => $barang->id_obat,
                'barang_kemasan_id' => $kemasan->id,
                'kemasan_id' => $kemasan->id,
                'jumlah' => $jumlah,
                'jumlah_kemasan' => $jumlah,
                'isi_per_kemasan' => $kemasan->isi_per_kemasan,
                'total_unit' => $totalUnit,
                'total_unit_dasar' => $totalUnit,
                'satuan' => $barang->satuan_dasar,
                'base_unit' => $barang->satuan_dasar,
                'kemasan' => $kemasan->nama_kemasan,
                'satuan_kemasan' => $kemasan->nama_kemasan,
                'keterangan' => $item['keterangan'] ?? null,
            ]);
        }

        foreach ($newItems as $item) {
            $permintaan->details()->create([
                'nama_barang_baru' => $item['nama'],
                'jumlah' => (float) $item['jumlah'],
                'satuan' => $item['satuan'],
                'kemasan' => $item['kemasan'] ?? null,
                'keterangan' => $item['keterangan'] ?? null,
            ]);
        }
    }
}
