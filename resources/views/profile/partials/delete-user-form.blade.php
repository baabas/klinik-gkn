<section>
    <header class="mb-4">
        <h3 class="h5 fw-bold text-danger">
            Hapus Akun
        </h3>
        <p class="text-muted small mt-2">
            Setelah akun Anda dihapus, semua data dan informasi akan dihapus secara permanen. 
        </p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
        <i class="bi bi-trash me-2"></i>Hapus Akun
    </button>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteAccountModalLabel">
                            <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus Akun
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="fw-bold">Apakah Anda yakin ingin menghapus akun Anda?</p>
                        <p class="text-muted small">
                            Setelah akun dihapus, semua data akan hilang secara permanen. Masukkan password Anda untuk konfirmasi.
                        </p>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                                   id="password" name="password" placeholder="Masukkan password Anda">
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i>Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
