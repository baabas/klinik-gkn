@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Input Rekam Medis Baru</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="mb-4 p-3 bg-primary bg-opacity-10 border border-primary rounded-3 text-center">
                        <p class="mb-1">Pasien:</p>
                        <h3 class="fw-bold mb-1">{{ $user->nama_karyawan }}</h3>
                        <p class="text-muted mb-0">NIP: {{ $user->nip }}</p>
                    </div>

                    @if ($errors->any() || session('error'))
                        <div class="alert alert-danger" role="alert">
                            <h5 class="alert-heading">Oops! Terjadi kesalahan.</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                                @if(session('error'))
                                    <li>{{ session('error') }}</li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('rekam-medis.store', $user->nip) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Diagnosa Penyakit</label>
                            <select id="select-diagnosa" multiple="multiple" name="diagnosa_kode[]" class="form-control">
                                @foreach($daftar_penyakit as $penyakit)
                                    <option value="{{ $penyakit->kode_penyakit }}" {{ (collect(old('diagnosa_kode'))->contains($penyakit->kode_penyakit)) ? 'selected':'' }}>
                                        {{ $penyakit->nama_penyakit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                             <label class="form-label fw-bold">Resep Obat</label>
                             <div id="resep-wrapper">
                                 </div>
                             <button type="button" id="tambah-obat-btn" class="btn btn-outline-success btn-sm mt-2">
                                <i class="bi bi-plus-circle"></i> Tambah Obat
                             </button>
                        </div>

                        <div class="mb-3">
                            <label for="riwayat_sakit" class="form-label">Riwayat Sakit (Catatan)</label>
                            <textarea id="riwayat_sakit" name="riwayat_sakit" rows="3" class="form-control">{{ old('riwayat_sakit') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="pengobatan" class="form-label">Pengobatan (Catatan)</label>
                            <textarea id="pengobatan" name="pengobatan" rows="3" class="form-control">{{ old('pengobatan') }}</textarea>
                        </div>

                        <h5 class="fs-6 fw-bold border-top pt-3">Berobat Untuk Keluarga (Opsional)</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-7">
                                <label for="nama_sa" class="form-label">Nama Suami / Istri / Anak</label>
                                <input type="text" id="nama_sa" name="nama_sa" class="form-control" value="{{ old('nama_sa') }}" placeholder="Kosongkan jika untuk diri sendiri">
                            </div>
                             <div class="col-md-5">
                                <label for="jenis_kelamin_sa" class="form-label">Jenis Kelamin S/A</label>
                                <select id="jenis_kelamin_sa" name="jenis_kelamin_sa" class="form-select">
                                    <option value="" selected>Pilih...</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin_sa') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin_sa') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>


                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('pasien.show', $user->nip) }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Rekam Medis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#select-diagnosa, #jenis_kelamin_sa').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih...',
            });

            const daftarObat = @json($daftar_obat);

            function tambahBarisResep() {
                const selectOptions = daftarObat.map(obat => `<option value="${obat.id_obat}">${obat.nama_obat}</option>`).join('');

                const barisResepHtml = `
                    <div class="input-group mb-2 resep-baris">
                        <select name="obat_id[]" class="form-select select-obat" required>
                            <option value="">Pilih Obat...</option>
                            ${selectOptions}
                        </select>
                        <input type="number" name="kuantitas[]" class="form-control" placeholder="Qty" min="1" required style="max-width: 80px;">
                        <button type="button" class="btn btn-outline-danger hapus-resep-btn"><i class="bi bi-trash"></i></button>
                    </div>
                `;
                $('#resep-wrapper').append(barisResepHtml);
                $('.select-obat').last().select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Obat...',
                });
            }

            $('#tambah-obat-btn').on('click', tambahBarisResep);

            $('#resep-wrapper').on('click', '.hapus-resep-btn', function() {
                $(this).closest('.resep-baris').remove();
            });

            // Jika ada data resep lama (saat validasi gagal), buat ulang barisnya
            @if(old('obat_id'))
                @foreach(old('obat_id') as $index => $obatId)
                    tambahBarisResep();
                    $('.select-obat').last().val('{{ $obatId }}').trigger('change');
                    $('input[name="kuantitas[]"]').last().val('{{ old("kuantitas.".$index) }}');
                @endforeach
            @endif
        });
    </script>
@endsection
