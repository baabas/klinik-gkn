@extends('layouts.sidebar-layout')

@section('title', 'Daftar Penyakit')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Daftar Penyakit</h1>
                    <p class="mb-0 text-muted">Kelola data penyakit berdasarkan kode ICD10</p>
                </div>
                <a href="{{ route('daftar-penyakit.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle-fill me-2"></i>Tambah Penyakit
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow">
                <div class="card-header bg-white border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-clipboard-data-fill me-2"></i>Data Penyakit
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search" id="search-icon"></i>
                                        <div class="spinner-border spinner-border-sm d-none" role="status" id="search-loading">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </span>
                                    <input type="text" 
                                           id="live-search" 
                                           class="form-control" 
                                           placeholder="Ketik untuk mencari kode ICD10 atau nama penyakit..." 
                                           value="{{ request('search') }}"
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="clear-search" style="display: none;">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0" id="search-results">
                    @if($penyakits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Kode ICD10</th>
                                        <th width="50%">Nama Penyakit</th>
                                        <th width="15%">Dibuat</th>
                                        <th width="15%">Diperbarui</th>
                                        <th width="5%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($penyakits as $penyakit)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $penyakit->ICD10 }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $penyakit->nama_penyakit }}</strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $penyakit->created_at ? $penyakit->created_at->format('d/m/Y H:i') : '-' }}
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $penyakit->updated_at ? $penyakit->updated_at->format('d/m/Y H:i') : '-' }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('daftar-penyakit.edit', $penyakit->ICD10) }}" 
                                                       class="btn btn-sm btn-warning text-white" title="Edit Data Penyakit">
                                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="confirmDelete('{{ $penyakit->ICD10 }}', '{{ addslashes($penyakit->nama_penyakit) }}')" 
                                                            title="Hapus Data Penyakit">
                                                        <i class="bi bi-trash3-fill me-1"></i>Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer bg-white border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Menampilkan {{ $penyakits->firstItem() }} - {{ $penyakits->lastItem() }} 
                                    dari {{ $penyakits->total() }} data
                                </div>
                                {{ $penyakits->withQueryString()->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Data penyakit tidak ditemukan</h5>
                            <p class="text-muted mb-4">
                                @if(request('search'))
                                    Tidak ada hasil untuk pencarian "{{ request('search') }}"
                                @else
                                    Belum ada data penyakit yang tersimpan
                                @endif
                            </p>
                            <a href="{{ route('daftar-penyakit.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle-fill me-2"></i>Tambah Penyakit Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus penyakit:</p>
                <div class="alert alert-warning">
                    <strong id="delete-code"></strong> - <span id="delete-name"></span>
                </div>
                <p class="text-danger small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Data yang sudah dihapus tidak dapat dikembalikan!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Debounce function untuk menghindari terlalu banyak request
function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// Live search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('live-search');
    const searchIcon = document.getElementById('search-icon');
    const searchLoading = document.getElementById('search-loading');
    const clearButton = document.getElementById('clear-search');
    const searchResults = document.getElementById('search-results');
    
    // Debounced search function (500ms delay)
    const debouncedSearch = debounce(function(searchTerm) {
        performSearch(searchTerm);
    }, 500);
    
    // Event listener untuk input search
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        
        // Show/hide clear button
        if (searchTerm.length > 0) {
            clearButton.style.display = 'block';
        } else {
            clearButton.style.display = 'none';
        }
        
        // Trigger debounced search
        if (searchTerm.length >= 2 || searchTerm.length === 0) {
            debouncedSearch(searchTerm);
        }
    });
    
    // Clear search functionality
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        clearButton.style.display = 'none';
        performSearch('');
    });
    
    // Function to perform search
    function performSearch(searchTerm) {
        // Show loading state
        searchIcon.classList.add('d-none');
        searchLoading.classList.remove('d-none');
        
        // Create URL with search parameter
        const url = new URL(window.location.href);
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }
        
        // Perform AJAX request
        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            }
        })
        .then(response => response.text())
        .then(html => {
            // Parse response and update results
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newResults = doc.getElementById('search-results');
            
            if (newResults) {
                searchResults.innerHTML = newResults.innerHTML;
                
                // Update URL without page reload
                if (searchTerm) {
                    window.history.pushState({search: searchTerm}, '', url.toString());
                } else {
                    window.history.pushState({}, '', url.pathname);
                }
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            showSearchError();
        })
        .finally(() => {
            // Hide loading state
            searchLoading.classList.add('d-none');
            searchIcon.classList.remove('d-none');
        });
    }
    
    // Function to show search error
    function showSearchError() {
        searchResults.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h5 class="text-muted">Terjadi kesalahan saat mencari</h5>
                <p class="text-muted">Silakan coba lagi dalam beberapa saat</p>
                <button class="btn btn-primary" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Muat Ulang
                </button>
            </div>
        `;
    }
    
    // Handle browser back/forward
    window.addEventListener('popstate', function(event) {
        const url = new URL(window.location.href);
        const searchTerm = url.searchParams.get('search') || '';
        searchInput.value = searchTerm;
        
        if (searchTerm) {
            clearButton.style.display = 'block';
        } else {
            clearButton.style.display = 'none';
        }
    });
});

// Delete confirmation function
function confirmDelete(icd10, namaPenyakit) {
    document.getElementById('delete-code').textContent = icd10;
    document.getElementById('delete-name').textContent = namaPenyakit;
    document.getElementById('delete-form').action = '{{ url("daftar-penyakit") }}/' + icd10;
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush