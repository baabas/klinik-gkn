@forelse ($barang as $item)
    @php
        $stokGkn1 = (int) ($item->stok_gkn1 ?? 0);
        $stokGkn2 = (int) ($item->stok_gkn2 ?? 0);
        $totalStok = (int) ($item->stok_sum_jumlah ?? 0);
    @endphp
    <tr class="align-middle">
        <td class="text-center">{{ $loop->iteration + $barang->firstItem() - 1 }}</td>
        <td class="text-center"><code>{{ $item->kode_obat }}</code></td>
        <td>
            <div class="fw-semibold">{{ $item->nama_obat }}</div>
            <div class="text-muted small">Total: {{ $totalStok ? number_format($totalStok) : 0 }} {{ strtolower($item->satuan_terkecil ?? '') }}</div>
        </td>
        <td class="text-center">
            <span class="badge {{ $item->kategori_barang == 'Obat' ? 'bg-primary' : 'bg-success' }}">{{ $item->kategori_barang }}</span>
        </td>
        <td class="text-center">
            <span class="badge bg-secondary">{{ $item->kemasan ?? 'Box' }}</span>
        </td>
        <td class="text-center">
            @if($item->isi_kemasan_jumlah && $item->isi_kemasan_satuan)
                <div><strong>{{ $item->isi_kemasan_jumlah }}</strong></div>
                <small class="text-muted">{{ $item->isi_kemasan_satuan }}</small>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td class="text-center">
            @if($item->isi_per_satuan)
                <div><strong>{{ $item->isi_per_satuan }}</strong></div>
                @if($item->satuan_terkecil && $item->isi_kemasan_satuan)
                    <small class="text-muted">{{ $item->satuan_terkecil }}/{{ $item->isi_kemasan_satuan }}</small>
                @endif
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td class="text-center">
            @if($item->satuan_terkecil)
                <span class="badge bg-info text-dark">{{ $item->satuan_terkecil }}</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td class="text-center">
            @if($item->tanggal_masuk_terakhir)
                <small class="text-muted">{{ \Illuminate\Support\Carbon::parse($item->tanggal_masuk_terakhir)->format('d/m/Y') }}</small>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td class="text-center">
            @if($item->expired_terdekat)
                @php
                    $expiredDate = \Illuminate\Support\Carbon::parse($item->expired_terdekat);
                    $isExpired = $expiredDate->isPast();
                    $isNearExpiry = $expiredDate->diffInDays(now()) <= 30 && !$isExpired;
                @endphp
                <small class="
                    @if($isExpired) text-danger fw-bold
                    @elseif($isNearExpiry) text-dark fw-bold
                    @else text-muted
                    @endif
                ">
                    {{ $expiredDate->format('d/m/Y') }}
                    @if($isExpired) 
                        <i class="bi bi-exclamation-triangle-fill" title="Sudah Kadaluarsa"></i>
                    @elseif($isNearExpiry) 
                        <i class="bi bi-exclamation-triangle" title="Akan Segera Kadaluarsa"></i>
                    @endif
                </small>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td class="text-center"><strong class="text-primary">{{ number_format($stokGkn1) }}</strong></td>
        <td class="text-center"><strong class="text-success">{{ number_format($stokGkn2) }}</strong></td>
        <td class="text-center"><strong class="text-dark">{{ number_format($totalStok) }}</strong></td>
        <td class="text-center sticky-action-column">
            <div class="d-flex flex-wrap gap-1 justify-content-center">
                {{-- Tombol yang bisa diakses semua role terkait (Dokter & Pengadaan) --}}
                <a href="{{ route('barang-medis.show', $item->id_obat) }}" class="btn btn-info btn-sm" title="Lihat Detail Stok"><i class="bi bi-eye"></i></a>

                {{-- Tombol Distribusi sekarang bisa diakses oleh Dokter dan Pengadaan --}}
                @if(Auth::user()->hasRole('DOKTER') || Auth::user()->hasRole('PENGADAAN'))
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#distribusiModal-{{ $item->id_obat }}" title="Distribusi Stok">
                        <i class="bi bi-truck"></i>
                    </button>
                @endif

                {{-- Tombol yang HANYA bisa diakses oleh Pengadaan --}}
                @if(Auth::user()->hasRole('PENGADAAN'))
                    <a href="{{ route('barang-medis.edit', $item->id_obat) }}" class="btn btn-warning btn-sm" title="Edit Barang"><i class="bi bi-pencil-square"></i></a>
                    <form action="{{ route('barang-medis.destroy', $item->id_obat) }}" method="POST" class="d-inline" 
                          onsubmit="return confirm('⚠️ HAPUS BARANG: {{ $item->nama_obat }}\n\nPeringatan:\n• Barang dengan stok ({{ number_format($totalStok) }}) tidak dapat dihapus\n• Barang dalam permintaan aktif tidak dapat dihapus\n• Penghapusan akan menghilangkan semua data secara permanen\n\nLanjutkan hapus?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Barang - Cek stok dan permintaan terlebih dahulu"><i class="bi bi-trash"></i></button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="14" class="text-center text-muted py-4">
            <i class="bi bi-search mb-2" style="font-size: 2rem;"></i>
            <div>Tidak ada data barang medis ditemukan.</div>
        </td>
    </tr>
@endforelse