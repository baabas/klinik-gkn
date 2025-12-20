<section>
    <header class="mb-4">
        <h3 class="h5 fw-bold text-dark">
            Informasi Profile
        </h3>
        <p class="text-muted small mt-2">
            Update informasi profile dan alamat email akun Anda.
        </p>
    </header>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>Profile berhasil diupdate!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>Link verifikasi baru telah dikirim ke email Anda.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        @if($user->nip)
            <div class="mb-3">
                <label for="nip" class="form-label">NIP</label>
                <input type="text" class="form-control bg-light" id="nip" name="nip" value="{{ $user->nip }}" disabled readonly>
                <small class="form-text text-muted">NIP tidak dapat diubah</small>
            </div>

            <div class="mb-3">
                <label for="nama_karyawan" class="form-label">Nama</label>
                <input type="text" class="form-control bg-light" id="nama_karyawan" name="nama_karyawan" value="{{ $user->nama_karyawan }}" disabled readonly>
                <small class="form-text text-muted">Nama tidak dapat diubah</small>
            </div>
        @endif

        <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-2">
                    <small>
                        Email Anda belum diverifikasi.
                        <button form="send-verification" class="btn btn-link btn-sm p-0 align-baseline">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </small>
                </div>
            @endif
        </div>

        @if($user->nip && $user->karyawan)
            @php
                $userRole = $user->akses ?? 'PASIEN';
            @endphp

            <div class="mb-3">
                <label for="no_hp" class="form-label">No. HP</label>
                <input type="text" class="form-control @error('no_hp') is-invalid @enderror" 
                       id="no_hp" name="no_hp" value="{{ old('no_hp', $user->karyawan->no_hp ?? '') }}" 
                       placeholder="Contoh: 08123456789">
                @error('no_hp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                       id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->karyawan->tanggal_lahir ?? '') }}">
                @error('tanggal_lahir')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                          id="alamat" name="alamat" rows="3" 
                          placeholder="Masukkan alamat lengkap Anda">{{ old('alamat', $user->karyawan->alamat ?? '') }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if($userRole === 'PASIEN')
                <div class="mb-3">
                    <label for="alergi" class="form-label">Alergi</label>
                    <textarea class="form-control @error('alergi') is-invalid @enderror" 
                              id="alergi" name="alergi" rows="3" 
                              placeholder="Masukkan alergi jika ada (contoh: Seafood, Obat Antibiotik, dll)">{{ old('alergi', $user->karyawan->alergi ?? '') }}</textarea>
                    @error('alergi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Informasi alergi penting untuk rekam medis Anda</small>
                </div>
            @endif

            @if(in_array($userRole, ['DOKTER', 'PENGADAAN']))
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <input type="text" class="form-control bg-light" id="jabatan" value="{{ $user->karyawan->jabatan ?? '-' }}" disabled readonly>
                        <small class="form-text text-muted">Jabatan tidak dapat diubah</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kantor" class="form-label">Kantor</label>
                        <input type="text" class="form-control bg-light" id="kantor" value="{{ $user->karyawan->kantor ?? '-' }}" disabled readonly>
                        <small class="form-text text-muted">Kantor tidak dapat diubah</small>
                    </div>
                </div>
            @endif
        @endif

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</section>
