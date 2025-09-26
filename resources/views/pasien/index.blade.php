@extends('layouts.sidebar-layout')

@push('styles')
    <style>
        .search-results {
            border-radius: 0 0 0.375rem 0.375rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .search-result-item:hover {
            background-color: #f8f9fa;
        }
        .search-result-item:last-child {
            border-bottom: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h2">Daftar Pasien</h1>
        <a href="{{ route('non_karyawan.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill"></i> Daftar Pasien Baru (Umum)
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-4 position-relative">
                <div class="input-group">
                    <input type="search" class="form-control" id="patient-search" placeholder="Cari berdasarkan Nama, NIP, atau NIK..." autocomplete="off">
                    <span class="btn btn-outline-secondary" id="search-icon"><i class="bi bi-search"></i></span>
                </div>
                <div id="search-results" class="search-results position-absolute w-100" style="z-index: 1050; max-height: 400px; overflow-y: auto; background: white; border: 1px solid #ddd; border-top: none; display: none;"></div>
            </div>

            <div id="patient-table-container">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>ID Pasien (NIP/NIK)</th>
                                <th>Nama Pasien</th>
                                <th>Pasien</th>
                                <th>Tanggal Lahir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pasien as $p)
                            <tr>
                                <td>{{ $loop->iteration + $pasien->firstItem() - 1 }}</td>
                                {{-- Tampilkan NIP jika ada, jika tidak, tampilkan NIK --}}
                                <td>{{ $p->nip ?? $p->nik }}</td>
                                <td>{{ $p->nama_karyawan }}</td>
                                <td>
                                    {{-- Tampilkan badge berdasarkan apakah relasi 'karyawan' ada atau tidak --}}
                                    @if($p->karyawan)
                                        <span class="badge bg-primary">Karyawan</span>
                                    @else
                                        <span class="badge bg-success">Non-Karyawan</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Ambil tanggal lahir dari profil yang sesuai --}}
                                    @php
                                        $tanggal_lahir = $p->karyawan?->tanggal_lahir ?? $p->nonKaryawan?->tanggal_lahir;
                                    @endphp
                                    {{ $tanggal_lahir ? \Carbon\Carbon::parse($tanggal_lahir)->isoFormat('D MMMM YYYY') : '-' }}
                                </td>
                                <td>
                                    {{-- Arahkan ke rute detail yang sesuai --}}
                                    @if($p->karyawan)
                                         <a href="{{ route('pasien.show', $p->nip) }}" class="btn btn-info btn-sm">Lihat Kartu</a>
                                    @else
                                         <a href="{{ route('pasien.show_non_karyawan', $p->nik) }}" class="btn btn-info btn-sm">Lihat Kartu</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data pasien ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $pasien->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let searchTimeout;
            let originalTable = $('#patient-table-container').html();
            
            // Store original table content
            $('#patient-table-container').html($('.table-responsive').parent().html());
            
            // Live search functionality
            $('#patient-search').on('keyup', function() {
                let query = $(this).val();
                let searchResults = $('#search-results');
                
                // Clear previous timeout
                clearTimeout(searchTimeout);
                
                if (query.length >= 2) {
                    // Show loading
                    $('#search-icon').html('<i class="spinner-border spinner-border-sm"></i>');
                    
                    // Debounce: wait 300ms before searching
                    searchTimeout = setTimeout(function() {
                        $.ajax({
                            url: `{{ url('/api/pasien-search') }}`,
                            type: 'GET',
                            data: { q: query },
                            success: function(data) {
                                $('#search-icon').html('<i class="bi bi-search"></i>');
                                
                                if (data.success && data.data.length > 0) {
                                    let resultsHtml = '';
                                    data.data.forEach(function(pasien, index) {
                                        let tanggalLahir = pasien.tanggal_lahir 
                                            ? new Date(pasien.tanggal_lahir).toLocaleDateString('id-ID', { 
                                                day: 'numeric', 
                                                month: 'long', 
                                                year: 'numeric' 
                                              })
                                            : '-';
                                        
                                        resultsHtml += `
                                            <div class="search-result-item p-3 border-bottom" style="cursor: pointer;" onclick="window.location.href='${pasien.url}'">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1">${pasien.nama}</h6>
                                                        <div class="text-muted small">
                                                            ID: ${pasien.identifier} | 
                                                            <span class="badge ${pasien.type === 'karyawan' ? 'bg-primary' : 'bg-success'}">${pasien.type === 'karyawan' ? 'Karyawan' : 'Non-Karyawan'}</span> | 
                                                            Lahir: ${tanggalLahir}
                                                        </div>
                                                    </div>
                                                    <a href="${pasien.url}" class="btn btn-info btn-sm">Lihat Kartu</a>
                                                </div>
                                            </div>
                                        `;
                                    });
                                    searchResults.html(resultsHtml).show();
                                } else {
                                    searchResults.html('<div class="p-3 text-muted text-center">Tidak ditemukan pasien</div>').show();
                                }
                            },
                            error: function() {
                                $('#search-icon').html('<i class="bi bi-search"></i>');
                                searchResults.html('<div class="p-3 text-danger text-center">Gagal memuat data</div>').show();
                            }
                        });
                    }, 300);
                } else {
                    searchResults.hide();
                    $('#search-icon').html('<i class="bi bi-search"></i>');
                }
            });

            // Hide search results on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#patient-search, #search-results').length) {
                    $('#search-results').hide();
                }
            });

            // Clear search
            $('#patient-search').on('input', function() {
                if ($(this).val() === '') {
                    $('#search-results').hide();
                }
            });
        });
    </script>
@endpush