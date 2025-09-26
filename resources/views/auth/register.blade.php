<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pasien</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Gaya tambahan untuk memposisikan form di tengah halaman */
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .register-card {
            width: 100%;
            max-width: 800px;
        }
        .form-row {
            display: flex;
            gap: 1rem;
        }
        .form-col {
            flex: 1;
        }
        
        /* Custom dropdown styling */
        .custom-dropdown {
            position: relative;
        }
        
        .custom-dropdown select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: white;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px;
            padding-right: 2.5rem;
        }
        
        .custom-dropdown select[size] {
            max-height: 150px; /* Tinggi untuk menampilkan 4-5 opsi */
            overflow-y: auto;
            background-image: none;
            padding-right: 0.75rem;
        }
        
        .custom-dropdown select::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-dropdown select::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .custom-dropdown select::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        
        .custom-dropdown select::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="card shadow-lg register-card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Registrasi Pasien</h4>
                </div>
            </div>
            <div class="card-body p-4">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

                        <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Row 1: NIP & Name -->
                <div class="form-row mb-3">
                    <div class="form-col">
                        <label for="nip" class="form-label" style="display: block !important;">{{ __('NIP') }}</label>
                        <input id="nip" class="form-control" type="text" name="nip" value="{{ old('nip') }}" required autofocus autocomplete="nip" style="display: block !important; visibility: visible !important;" />
                        @error('nip')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-col">
                        <label for="name" class="form-label">{{ __('Nama') }}</label>
                        <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" />
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row 2: Email & Kantor -->
                <div class="form-row mb-3">
                    <div class="form-col">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-col">
                        <label for="kantor" class="form-label">{{ __('Kantor') }}</label>
                        <div class="custom-dropdown">
                            <select id="kantor" name="kantor" class="form-select" required onclick="toggleDropdown(this)">
                                <option value="">Pilih Kantor</option>
                                <option value="Kanwil" {{ old('kantor') == 'Kanwil' ? 'selected' : '' }}>Kanwil</option>
                                <option value="KPP Gayam Sari" {{ old('kantor') == 'KPP Gayam Sari' ? 'selected' : '' }}>KPP Gayam Sari</option>
                                <option value="KPP Madya SMG" {{ old('kantor') == 'KPP Madya SMG' ? 'selected' : '' }}>KPP Madya SMG</option>
                                <option value="KPP SMG Selatan" {{ old('kantor') == 'KPP SMG Selatan' ? 'selected' : '' }}>KPP SMG Selatan</option>
                                <option value="KPP SMG Tengah 1" {{ old('kantor') == 'KPP SMG Tengah 1' ? 'selected' : '' }}>KPP SMG Tengah 1</option>
                                <option value="KPTIK" {{ old('kantor') == 'KPTIK' ? 'selected' : '' }}>KPTIK</option>
                                <option value="PT Gumilang" {{ old('kantor') == 'PT Gumilang' ? 'selected' : '' }}>PT Gumilang</option>
                                <option value="Kanwil DJPB" {{ old('kantor') == 'Kanwil DJPB' ? 'selected' : '' }}>Kanwil DJPB</option>
                                <option value="KPTIK BMN Semarang" {{ old('kantor') == 'KPTIK BMN Semarang' ? 'selected' : '' }}>KPTIK BMN Semarang</option>
                                <option value="KPP Madya Dua Semarang" {{ old('kantor') == 'KPP Madya Dua Semarang' ? 'selected' : '' }}>KPP Madya Dua Semarang</option>
                                <option value="Kanwil DJP Jateng 1" {{ old('kantor') == 'Kanwil DJP Jateng 1' ? 'selected' : '' }}>Kanwil DJP Jateng 1</option>
                                <option value="Kanwil DJKN" {{ old('kantor') == 'Kanwil DJKN' ? 'selected' : '' }}>Kanwil DJKN</option>
                                <option value="KPKNL Semarang" {{ old('kantor') == 'KPKNL Semarang' ? 'selected' : '' }}>KPKNL Semarang</option>
                            </select>
                        </div>
                        @error('kantor')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row 3: Password & Confirm Password -->
                <div class="form-row mb-3">
                    <div class="form-col">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" />
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-col">
                        <label for="password_confirmation" class="form-label">{{ __('Konfirmasi Password') }}</label>
                        <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
                        @error('password_confirmation')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row 4: Agama & Tanggal Lahir -->
                <div class="form-row mb-3">
                    <div class="form-col">
                        <label for="agama" class="form-label">{{ __('Agama') }}</label>
                        <select id="agama" name="agama" class="form-select" required>
                            <option value="">Pilih Agama</option>
                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                        @error('agama')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-col">
                        <label for="tanggal_lahir" class="form-label">{{ __('Tanggal Lahir') }}</label>
                        <input id="tanggal_lahir" class="form-control" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required />
                        @error('tanggal_lahir')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row 5: Alamat (Full Width) -->
                <div class="mb-3">
                    <label for="alamat" class="form-label">{{ __('Alamat') }}</label>
                    <textarea id="alamat" class="form-control" name="alamat" rows="2" required>{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="d-flex gap-3 mt-4">
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary flex-fill">
                        <i class="fas fa-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-user-plus me-1"></i> {{ __('Daftar') }}
                    </button>
                </div>

                <div class="text-center mt-3">
                    <p class="text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none text-primary">{{ __('Login di sini') }}</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleDropdown(selectElement) {
            if (selectElement.size === 1 || selectElement.size === 0) {
                // Expand to show 4 options with scroll
                selectElement.size = 4;
                selectElement.style.height = 'auto';
            }
        }
        
        // Close dropdown when clicking outside or when selection is made
        document.addEventListener('click', function(event) {
            const selects = document.querySelectorAll('select[size]');
            selects.forEach(function(select) {
                if (!select.contains(event.target)) {
                    select.size = 1;
                }
            });
        });
        
        // Close dropdown when option is selected
        document.getElementById('kantor').addEventListener('change', function() {
            this.size = 1;
        });
        
        // Prevent dropdown from closing when scrolling inside it
        document.getElementById('kantor').addEventListener('mousedown', function(e) {
            e.stopPropagation();
        });
    </script>
</body>
</html>
