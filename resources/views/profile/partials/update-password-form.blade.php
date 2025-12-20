<section>
    <header class="mb-4">
        <h3 class="h5 fw-bold text-dark">
            Update Password
        </h3>
        <p class="text-muted small mt-2">
            Pastikan akun Anda menggunakan password yang panjang dan acak untuk keamanan.
        </p>
    </header>

    @if (session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>Password berhasil diupdate!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Password Saat Ini</label>
            <div class="input-group">
                <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                       id="update_password_current_password" name="current_password" autocomplete="current-password">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_current_password', this)">
                    <i class="bi bi-eye"></i>
                </button>
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">Password Baru</label>
            <div class="input-group">
                <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                       id="update_password_password" name="password" autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_password', this)">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
            <div class="input-group">
                <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                       id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_password_confirmation', this)">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i>Simpan Password
            </button>
        </div>
    </form>
</section>

<script>
function togglePassword(fieldId, button) {
    const field = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
