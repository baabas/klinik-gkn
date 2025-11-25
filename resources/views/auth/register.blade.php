<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pasien - Klinik GKN</title>

    @vite(['resources/css/app.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('{{ asset('images/backgrounds/GKN1.jpg') }}');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 51, 102, 0.25) 100%);
            z-index: 0;
        }

        .auth-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1100px;
            padding: 24px;
            animation: slideIn 0.6s ease-out;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            display: grid;
            grid-template-columns: 360px 1fr;
            min-height: 640px;
        }

        .auth-header {
            text-align: center;
            padding: 48px 36px;
            background: linear-gradient(135deg, rgba(0, 102, 204, 0.45) 0%, rgba(0, 77, 153, 0.55) 100%);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-right: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
        }

        .auth-header::after {
            content: '';
            position: absolute;
            width: 160px;
            height: 160px;
            border: 24px solid rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            top: 40px;
            right: -60px;
        }

        .logo-circle {
            width: 82px;
            height: 82px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .logo-circle i {
            font-size: 38px;
            color: #0066cc;
        }

        .auth-header h2 {
            color: white;
            font-size: 26px;
            font-weight: 800;
            margin: 0 0 8px;
            text-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        }

        .auth-header p {
            color: rgba(255, 255, 255, 0.96);
            font-size: 14px;
            margin: 0 0 14px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .auth-points {
            text-align: left;
            color: rgba(255, 255, 255, 0.9);
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            margin-top: 10px;
            backdrop-filter: blur(8px);
        }

        .auth-points li {
            margin: 8px 0;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .auth-points i {
            color: #cde6ff;
        }

        .auth-body {
            padding: 42px 46px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }

        .auth-body h3 {
            margin-bottom: 6px;
            font-size: 26px;
            font-weight: 800;
            color: #1f2d3d;
        }

        .auth-body p {
            color: #5c6b7a;
            margin-bottom: 26px;
            font-size: 15px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 16px;
        }

        .full-width {
            grid-column: span 2;
        }

        .input-group-modern {
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .input-group-modern label {
            font-size: 13px;
            font-weight: 700;
            color: #2d3b50;
            margin-bottom: 7px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i.icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 16px;
            z-index: 1;
        }

        .input-with-icon input,
        .input-with-icon select,
        .input-with-icon textarea {
            width: 100%;
            padding: 12px 14px 12px 44px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 13px;
            transition: all 0.3s ease;
            background: white;
        }

        .input-with-icon textarea {
            min-height: 90px;
            resize: vertical;
        }

        .input-with-icon input:focus,
        .input-with-icon select:focus,
        .input-with-icon textarea:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 2;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.3s ease;
            width: 30px;
            height: 30px;
        }

        .password-toggle:hover {
            color: #0066cc;
            background: rgba(0, 102, 204, 0.08);
        }

        .helper-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 6px;
        }

        .btn-modern {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #0066cc 0%, #004d99 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 22px rgba(0, 102, 204, 0.35);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 102, 204, 0.45);
        }

        .btn-modern:active {
            transform: translateY(0);
        }

        .link-inline {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
        }

        .link-inline a {
            color: #0066cc;
            font-weight: 700;
            text-decoration: none;
        }

        .link-inline a:hover {
            color: #004d99;
            text-decoration: underline;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 18px;
            border: none;
            animation: slideDown 0.4s ease;
        }

        .alert-danger {
            background: #fee;
            color: #c33;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
        }

        .error-text {
            color: #c33;
            font-size: 12px;
            margin-top: 6px;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1100px) {
            .auth-card {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .auth-header {
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            }

            .full-width,
            .form-grid {
                grid-column: span 1;
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .auth-container {
                padding: 14px;
            }

            .auth-body {
                padding: 32px 26px;
            }

            .auth-header {
                padding: 36px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-circle">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h2>Klinik GKN</h2>
                <p>Lengkapi data Anda untuk membuat akun pasien</p>
                <ul class="auth-points">
                    <li><i class="bi bi-shield-check"></i>Data aman dan terverifikasi</li>
                    <li><i class="bi bi-activity"></i>Akses rekam medis pribadi</li>
                </ul>
            </div>

            <div class="auth-body">
                <h3>Registrasi Pasien</h3>
                <p>Masukkan data sesuai identitas untuk memulai layanan klinik.</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Error!</strong>
                        <ul class="mb-0 mt-2" style="list-style: none; padding-left: 0;">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('status'))
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="input-group-modern">
                            <label for="nip">NIP</label>
                            <div class="input-with-icon">
                                <i class="bi bi-person-badge icon-left"></i>
                                <input id="nip" type="text" name="nip" value="{{ old('nip') }}" required autofocus
                                       autocomplete="nip" inputmode="numeric" minlength="18" maxlength="18" pattern="\d{18}"
                                       placeholder="Masukkan 18 digit NIP">
                            </div>
                            <p class="helper-text">Masukkan 18 digit NIP tanpa spasi atau tanda baca.</p>
                            @error('nip')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group-modern">
                            <label for="name">Nama</label>
                            <div class="input-with-icon">
                                <i class="bi bi-person icon-left"></i>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                                       placeholder="Nama lengkap sesuai identitas">
                            </div>
                            @error('name')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group-modern">
                            <label for="email">Email</label>
                            <div class="input-with-icon">
                                <i class="bi bi-envelope icon-left"></i>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                       placeholder="nama@email.com">
                            </div>
                            @error('email')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group-modern">
                            <label for="kantor">Kantor</label>
                            <div class="input-with-icon">
                                <i class="bi bi-building icon-left"></i>
                                <select id="kantor" name="kantor" required>
                                    <option value="">Pilih Kantor</option>
                                    @foreach($kantors as $kantor)
                                        <option value="{{ $kantor->nama_kantor }}" {{ old('kantor') == $kantor->nama_kantor ? 'selected' : '' }}>
                                            {{ $kantor->nama_kantor }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('kantor')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group-modern">
                            <label for="password">Password</label>
                            <div class="input-with-icon">
                                <i class="bi bi-lock-fill icon-left"></i>
                                <input id="password" type="password" name="password" required autocomplete="new-password"
                                       placeholder="Minimal 8 karakter">
                                <span class="password-toggle" onclick="togglePassword('password')">
                                    <i class="bi bi-eye" id="password-icon"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group-modern">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <div class="input-with-icon">
                                <i class="bi bi-shield-lock icon-left"></i>
                                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                       placeholder="Ulangi password">
                                <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                                    <i class="bi bi-eye" id="password_confirmation-icon"></i>
                                </span>
                            </div>
                            @error('password_confirmation')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group-modern">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <div class="input-with-icon">
                                <i class="bi bi-calendar-event icon-left"></i>
                                <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                            </div>
                            @error('tanggal_lahir')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-top: 22px; display: grid; gap: 10px;">
                        <button type="submit" class="btn-modern">
                            <i class="bi bi-person-check me-2"></i>{{ __('Daftar') }}
                        </button>
                        <div class="link-inline">
                            Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>

    @vite(['resources/js/app.js'])
</body>
</html>
