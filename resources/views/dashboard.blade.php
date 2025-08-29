<x-app-layout>
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h3 class="card-title h2">Dashboard</h3>
                <p class="card-text text-muted">Selamat datang di sistem informasi Klinik.</p>
            </div>
        </div>

        <div class="card text-center text-bg-primary mb-4">
            <div class="card-body">
                  <h5 class="card-title">Jumlah Kunjungan Hari Ini</h5>
                  <p class="display-3 fw-bold mb-0">{{ $kasus_hari_ini }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Jumlah per Jenis Penyakit (Bulan Ini)</h5>
                        @if($data_penyakit->isNotEmpty())
                            <ul class="list-unstyled">
                                @php $max_penyakit = $data_penyakit->max('jumlah') ?: 1; @endphp
                                @foreach($data_penyakit as $penyakit)
                                    <li class="mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $penyakit->nama_penyakit }}</span>
                                            <strong>{{ $penyakit->jumlah }}</strong>
                                        </div>
                                        <div class="progress" style="height: 1rem;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($penyakit->jumlah / $max_penyakit) * 100 }}%;" aria-valuenow="{{ $penyakit->jumlah }}" aria-valuemin="0" aria-valuemax="{{ $max_penyakit }}"></div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-center text-muted">Belum ada data.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                         <h5 class="card-title mb-4">Persentase Penyakit (Bulan Ini)</h5>
                         @if($data_penyakit->isNotEmpty() && $total_kasus_penyakit > 0)
                            <ul class="list-unstyled">
                                 @foreach($data_penyakit as $penyakit)
                                 <li class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                     <strong class="fs-4">{{ number_format(($penyakit->jumlah / $total_kasus_penyakit) * 100, 2) }}%</strong>
                                     <span class="text-muted align-self-center">{{ $penyakit->nama_penyakit }}</span>
                                 </li>
                                 @endforeach
                            </ul>
                        @else
                            <p class="text-center text-muted">Belum ada data.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                         <h5 class="card-title mb-4">Jumlah Pemakaian Obat (Bulan Ini)</h5>
                         @if($data_obat->isNotEmpty())
                            <ul class="list-unstyled">
                                 @php $max_obat = $data_obat->max('jumlah') ?: 1; @endphp
                                 @foreach ($data_obat as $obat)
                                     <li class="mb-2">
                                         <div class="d-flex justify-content-between">
                                             <span>{{ $obat->nama_obat }}</span>
                                             <strong>{{ $obat->jumlah }}</strong>
                                         </div>
                                         <div class="progress" style="height: 1rem;">
                                             <div class="progress-bar" role="progressbar" style="width: {{ ($obat->jumlah / $max_obat) * 100 }}%;" aria-valuenow="{{ $obat->jumlah }}" aria-valuemin="0" aria-valuemax="{{ $max_obat }}"></div>
                                         </div>
                                     </li>
                                 @endforeach
                            </ul>
                        @else
                            <p class="text-center text-muted">Belum ada data.</p>
                        @endif
                    </div>
                </div>
            </div>

             <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Persentase Pemakaian Obat (Bulan Ini)</h5>
                        @if($data_obat->isNotEmpty() && $total_pemakaian_obat > 0)
                            <ul class="list-unstyled">
                                 @foreach($data_obat as $obat)
                                 <li class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                     <strong class="fs-4">{{ number_format(($obat->jumlah / $total_pemakaian_obat) * 100, 2) }}%</strong>
                                     <span class="text-muted align-self-center">{{ $obat->nama_obat }}</span>
                                 </li>
                                 @endforeach
                            </ul>
                        @else
                            <p class="text-center text-muted">Belum ada data.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
