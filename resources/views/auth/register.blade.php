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
            max-height: 90vh;
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
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            overflow-y: auto;
            max-height: 90vh;
        }
        
        .auth-body::-webkit-scrollbar {
            width: 8px;
        }
        
        .auth-body::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        
        .auth-body::-webkit-scrollbar-thumb {
            background: rgba(0, 102, 204, 0.3);
            border-radius: 10px;
        }
        
        .auth-body::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 102, 204, 0.5);
        }

        /* Step Wizard Styles */
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 32px;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }

        .step-indicator .progress-line {
            position: absolute;
            top: 20px;
            left: 0;
            height: 2px;
            background: linear-gradient(90deg, #0066cc 0%, #004d99 100%);
            z-index: 0;
            transition: width 0.3s ease;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: #6c757d;
            transition: all 0.3s ease;
            margin-bottom: 8px;
        }

        .step.active .step-circle {
            background: linear-gradient(135deg, #0066cc 0%, #004d99 100%);
            border-color: #0066cc;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }

        .step.completed .step-circle {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }

        .step-label {
            font-size: 11px;
            color: #6c757d;
            text-align: center;
            max-width: 80px;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #0066cc;
            font-weight: 700;
        }

        .step.completed .step-label {
            color: #28a745;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .step-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn-prev, .btn-next {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-prev {
            background: #6c757d;
            color: white;
        }

        .btn-prev:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-next {
            background: linear-gradient(135deg, #0066cc 0%, #004d99 100%);
            color: white;
            box-shadow: 0 8px 22px rgba(0, 102, 204, 0.35);
        }

        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 102, 204, 0.45);
        }

        .btn-next:disabled {
            background: #e0e0e0;
            color: #6c757d;
            cursor: not-allowed;
            box-shadow: none;
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
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .full-width,
        .col-span-2,
        .col-span-3 {
            width: 100%;
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

        /* Step Wizard Styles */
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 32px;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }

        .step-indicator .progress-line {
            position: absolute;
            top: 20px;
            left: 0;
            height: 2px;
            background: linear-gradient(90deg, #0066cc 0%, #004d99 100%);
            z-index: 0;
            transition: width 0.3s ease;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: #6c757d;
            transition: all 0.3s ease;
            margin-bottom: 8px;
        }

        .step.active .step-circle {
            background: linear-gradient(135deg, #0066cc 0%, #004d99 100%);
            border-color: #0066cc;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }

        .step.completed .step-circle {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }

        .step-label {
            font-size: 11px;
            color: #6c757d;
            text-align: center;
            max-width: 80px;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #0066cc;
            font-weight: 700;
        }

        .step.completed .step-label {
            color: #28a745;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .step-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn-prev, .btn-next {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-prev {
            background: #6c757d;
            color: white;
        }

        .btn-prev:hover:not(:disabled) {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-next {
            background: linear-gradient(135deg, #0066cc 0%, #004d99 100%);
            color: white;
            box-shadow: 0 8px 22px rgba(0, 102, 204, 0.35);
        }

        .btn-next:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 102, 204, 0.45);
        }

        .btn-prev:disabled,
        .btn-next:disabled {
            background: #e0e0e0;
            color: #6c757d;
            cursor: not-allowed;
            box-shadow: none;
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

        /* Style untuk input error */
        .input-error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15) !important;
        }

        .input-error:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.2) !important;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in-error {
            animation: slideDownError 0.3s ease-out;
        }

        @keyframes slideDownError {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

            .form-grid {
                gap: 16px;
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

                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="progress-line" id="progressLine" style="width: 0%"></div>
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-label">Data Identitas</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-label">Info Kantor</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-label">Keamanan</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-circle">4</div>
                        <div class="step-label">Data Tambahan</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <!-- STEP 1: Data Identitas -->
                    <div class="form-step active" data-step="1">
                        <div class="form-grid">
                            <div class="input-group-modern">
                                <label for="nip">NIP</label>
                                <div class="input-with-icon">
                                    <i class="bi bi-person-badge icon-left"></i>
                                    <input id="nip" type="text" name="nip" value="{{ old('nip') }}" required
                                           autocomplete="nip" inputmode="numeric" minlength="18" maxlength="18" pattern="\d{18}"
                                           placeholder="Masukkan 18 digit NIP">
                                </div>
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
                        </div>
                        <div class="step-buttons">
                            <a href="{{ route('login') }}" class="btn-prev" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">Kembali</a>
                            <button type="button" class="btn-next" onclick="nextStep(1)">Lanjut</button>
                        </div>
                    </div>

                    <!-- STEP 2: Info Kantor & Personal -->
                    <div class="form-step" data-step="2">
                        <div class="form-grid">
                            <div class="input-group-modern col-span-3">
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

                            <div class="input-group-modern col-span-3">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <div class="input-with-icon">
                                    <i class="bi bi-calendar-event icon-left"></i>
                                    <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                                </div>
                                @error('tanggal_lahir')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="input-group-modern col-span-3">
                                <label for="alamat">Alamat</label>
                                <div class="input-with-icon">
                                    <i class="bi bi-geo-alt icon-left"></i>
                                    <textarea id="alamat" name="alamat" rows="3" required placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                                </div>
                                @error('alamat')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="step-buttons">
                            <button type="button" class="btn-prev" onclick="prevStep(2)">Kembali</button>
                            <button type="button" class="btn-next" onclick="nextStep(2)">Lanjut</button>
                        </div>
                    </div>

                    <!-- STEP 3: Keamanan Akun -->
                    <div class="form-step" data-step="3">
                        <div class="form-grid">
                            <div class="input-group-modern col-span-3">
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

                            <div class="input-group-modern col-span-3">
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
                        </div>
                        <div class="step-buttons">
                            <button type="button" class="btn-prev" onclick="prevStep(3)">Kembali</button>
                            <button type="button" class="btn-next" onclick="nextStep(3)">Lanjut</button>
                        </div>
                    </div>

                    <!-- STEP 4: Data Tambahan -->
                    <div class="form-step" data-step="4">
                        <div class="form-grid">

                        <div class="input-group-modern col-span-2">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <div class="input-with-icon">
                                <i class="bi bi-person-badge icon-left"></i>
                                <select id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            @error('jenis_kelamin')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group-modern">
                            <label for="no_hp">No. HP</label>
                            <div class="input-with-icon">
                                <i class="bi bi-phone icon-left"></i>
                                <input id="no_hp" type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx">
                            </div>
                            @error('no_hp')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group-modern col-span-3">
                            <label for="alergi">Alergi (jika ada)</label>
                            <div class="input-with-icon">
                                <i class="bi bi-exclamation-triangle icon-left"></i>
                                <textarea id="alergi" name="alergi" rows="2" placeholder="Sebutkan alergi yang dimiliki (opsional)">{{ old('alergi') }}</textarea>
                            </div>
                            @error('alergi')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div style="margin-top: 22px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <button type="button" class="btn-prev" onclick="prevStep(4)">Kembali</button>
                        <button type="submit" class="btn-next">
                            <i class="bi bi-person-check me-2"></i>Daftar
                        </button>
                    </div>
                    
                    <div class="link-inline" style="text-align: center; margin-top: 10px;">
                        Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
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

        // Validasi NIP - hanya angka yang diperbolehkan
        document.getElementById('nip').addEventListener('input', function(e) {
            // Hapus semua karakter selain angka
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Mencegah paste karakter non-angka
        document.getElementById('nip').addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numbersOnly = pastedText.replace(/[^0-9]/g, '');
            this.value = numbersOnly.substring(0, 18);
        });

        // Mencegah input huruf via keypress
        document.getElementById('nip').addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                e.preventDefault();
            }
        });

        // Validasi Nama - hanya huruf, spasi, dan titik yang diperbolehkan
        document.getElementById('name').addEventListener('input', function(e) {
            // Hapus semua karakter selain huruf, spasi, dan titik
            this.value = this.value.replace(/[^a-zA-Z\s\.]/g, '');
        });

        // Mencegah paste karakter non-huruf pada nama
        document.getElementById('name').addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const lettersOnly = pastedText.replace(/[^a-zA-Z\s\.]/g, '');
            this.value = lettersOnly;
        });

        // Mencegah input angka/simbol via keypress pada nama
        document.getElementById('name').addEventListener('keypress', function(e) {
            if (!/[a-zA-Z\s\.]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                e.preventDefault();
            }
        });

        // Validasi form sebelum submit - pastikan semua field terisi
        document.querySelector('form').addEventListener('submit', function(e) {
            const fields = [
                { id: 'nip', name: 'NIP', minLength: 18 },
                { id: 'name', name: 'Nama' },
                { id: 'email', name: 'Email' },
                { id: 'kantor', name: 'Kantor' },
                { id: 'password', name: 'Password' },
                { id: 'password_confirmation', name: 'Konfirmasi Password' },
                { id: 'tanggal_lahir', name: 'Tanggal Lahir' },
                { id: 'jenis_kelamin', name: 'Jenis Kelamin' }
            ];

            let errors = [];
            
            fields.forEach(function(field) {
                const element = document.getElementById(field.id);
                const value = element.value.trim();
                
                // Hapus error sebelumnya
                element.classList.remove('input-error');
                
                if (!value || value === '') {
                    errors.push(field.name + ' wajib diisi');
                    element.classList.add('input-error');
                } else if (field.minLength && value.length < field.minLength) {
                    errors.push(field.name + ' harus ' + field.minLength + ' digit');
                    element.classList.add('input-error');
                }
            });

            // Validasi nama - hanya huruf, spasi, dan titik
            const nama = document.getElementById('name').value.trim();
            const namaRegex = /^[a-zA-Z\s\.]+$/;
            if (nama && !namaRegex.test(nama)) {
                errors.push('Nama hanya boleh berisi huruf, spasi, dan titik');
                document.getElementById('name').classList.add('input-error');
            }

            // Validasi password match
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;
            if (password && passwordConfirm && password !== passwordConfirm) {
                errors.push('Konfirmasi Password tidak cocok');
                document.getElementById('password_confirmation').classList.add('input-error');
            }

            // Validasi email format
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                errors.push('Format Email tidak valid');
                document.getElementById('email').classList.add('input-error');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Mohon lengkapi form dengan benar:\n\n• ' + errors.join('\n• '));
                
                // Focus ke field pertama yang error
                const firstErrorField = document.querySelector('.input-error');
                if (firstErrorField) {
                    firstErrorField.focus();
                }
            }
        });

        // Hapus class error saat user mulai mengisi
        document.querySelectorAll('input, select').forEach(function(element) {
            element.addEventListener('input', function() {
                this.classList.remove('input-error');
            });
            element.addEventListener('change', function() {
                this.classList.remove('input-error');
            });
        });

        // Multi-Step Form Controller
        let currentStep = 1;
        const totalSteps = 4;

        function nextStep(step) {
            // Validasi field di step saat ini
            if (!validateStep(step)) {
                return;
            }

            if (currentStep < totalSteps) {
                currentStep++;
                updateStepDisplay();
            }
        }

        function prevStep(step) {
            if (currentStep > 1) {
                currentStep--;
                updateStepDisplay();
            }
        }

        function validateStep(step) {
            const formStep = document.querySelector(`.form-step[data-step="${step}"]`);
            const requiredFields = formStep.querySelectorAll('[required]');
            let isValid = true;
            let errorMessages = [];

            // Clear previous errors
            formStep.querySelectorAll('.error-text').forEach(el => el.remove());
            formStep.querySelectorAll('input, select, textarea').forEach(el => {
                el.style.borderColor = '';
            });

            // Validasi per field
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                    
                    const label = field.closest('.input-group-modern').querySelector('label').textContent;
                    errorMessages.push(`${label} wajib diisi`);
                }
            });

            // Validasi khusus per step
            if (step === 1) {
                // Validasi NIP (18 digit)
                const nip = document.getElementById('nip');
                if (nip.value && nip.value.length !== 18) {
                    nip.style.borderColor = '#dc3545';
                    errorMessages.push('NIP harus 18 digit');
                    isValid = false;
                }

                // Validasi Email format
                const email = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email.value && !emailRegex.test(email.value)) {
                    email.style.borderColor = '#dc3545';
                    errorMessages.push('Format email tidak valid');
                    isValid = false;
                }

                // Validasi Nama (hanya huruf, spasi, titik)
                const name = document.getElementById('name');
                const nameRegex = /^[a-zA-Z\s\.]+$/;
                if (name.value && !nameRegex.test(name.value)) {
                    name.style.borderColor = '#dc3545';
                    errorMessages.push('Nama hanya boleh berisi huruf, spasi, dan titik');
                    isValid = false;
                }

                // Cek duplikasi NIP dan Email ke server (synchronous check)
                if (isValid && nip.value && email.value) {
                    // Tampilkan loading state
                    const btnNext = formStep.querySelector('.btn-next');
                    const originalText = btnNext.innerHTML;
                    btnNext.innerHTML = '<i class="bi bi-hourglass-split"></i> Memeriksa...';
                    btnNext.disabled = true;

                    // Check duplikasi via AJAX (synchronous)
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{ route("check.duplicate") }}', false); // false = synchronous
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    
                    try {
                        xhr.send(JSON.stringify({
                            nip: nip.value,
                            email: email.value
                        }));

                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            
                            if (response.nip_exists) {
                                nip.style.borderColor = '#dc3545';
                                errorMessages.push('NIP sudah terdaftar dalam sistem');
                                isValid = false;
                            }
                            
                            if (response.email_exists) {
                                email.style.borderColor = '#dc3545';
                                errorMessages.push('Email sudah terdaftar dalam sistem');
                                isValid = false;
                            }
                        }
                    } catch (e) {
                        console.error('Error checking duplicate:', e);
                    }

                    // Restore button
                    btnNext.innerHTML = originalText;
                    btnNext.disabled = false;
                }
            }

            if (step === 3) {
                // Validasi Password minimal 8 karakter
                const password = document.getElementById('password');
                if (password.value && password.value.length < 8) {
                    password.style.borderColor = '#dc3545';
                    errorMessages.push('Password minimal 8 karakter');
                    isValid = false;
                }

                // Validasi Password Confirmation match
                const passwordConf = document.getElementById('password_confirmation');
                if (password.value !== passwordConf.value) {
                    passwordConf.style.borderColor = '#dc3545';
                    errorMessages.push('Konfirmasi password tidak cocok');
                    isValid = false;
                }
            }

            if (step === 4) {
                // Validasi No HP (format Indonesia)
                const noHp = document.getElementById('no_hp');
                if (noHp.value && noHp.value.length > 0) {
                    if (!/^08\d{8,11}$/.test(noHp.value)) {
                        noHp.style.borderColor = '#dc3545';
                        errorMessages.push('No HP harus diawali 08 dan 10-13 digit');
                        isValid = false;
                    }

                    // Cek duplikasi No HP ke server
                    if (isValid) {
                        const btnNext = formStep.querySelector('button[type="submit"]');
                        const originalText = btnNext.innerHTML;
                        btnNext.innerHTML = '<i class="bi bi-hourglass-split"></i> Memeriksa...';
                        btnNext.disabled = true;

                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '{{ route("check.duplicate") }}', false);
                        xhr.setRequestHeader('Content-Type', 'application/json');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                        
                        try {
                            xhr.send(JSON.stringify({
                                no_hp: noHp.value
                            }));

                            if (xhr.status === 200) {
                                const response = JSON.parse(xhr.responseText);
                                
                                if (response.no_hp_exists) {
                                    noHp.style.borderColor = '#dc3545';
                                    errorMessages.push('Nomor HP sudah terdaftar dalam sistem');
                                    isValid = false;
                                }
                            }
                        } catch (e) {
                            console.error('Error checking duplicate:', e);
                        }

                        btnNext.innerHTML = originalText;
                        btnNext.disabled = false;
                    }
                }
            }

            // Tampilkan error messages
            if (!isValid) {
                let errorHtml = '<div class="alert alert-danger mb-3 fade-in-error"><strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Perhatian:</strong><ul class="mb-0 mt-2" style="list-style: none; padding-left: 0;">';
                errorMessages.forEach(msg => {
                    errorHtml += `<li>• ${msg}</li>`;
                });
                errorHtml += '</ul></div>';
                
                formStep.insertAdjacentHTML('afterbegin', errorHtml);
                
                // Scroll ke error
                const alertElement = formStep.querySelector('.alert');
                alertElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Hapus alert otomatis setelah 2 detik
                setTimeout(() => {
                    alertElement.style.opacity = '0';
                    alertElement.style.transition = 'opacity 0.5s ease-out';
                    setTimeout(() => {
                        alertElement.remove();
                    }, 500);
                }, 2000);
            }

            return isValid;
        }

        function updateStepDisplay() {
            // Update form steps
            document.querySelectorAll('.form-step').forEach(step => {
                step.classList.remove('active');
            });
            document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('active');

            // Update step indicator
            document.querySelectorAll('.step').forEach((step, index) => {
                const stepNum = index + 1;
                step.classList.remove('active', 'completed');
                
                if (stepNum < currentStep) {
                    step.classList.add('completed');
                    const circle = step.querySelector('.step-circle');
                    circle.innerHTML = '<i class="bi bi-check"></i>';
                } else if (stepNum === currentStep) {
                    step.classList.add('active');
                    step.querySelector('.step-circle').textContent = stepNum;
                } else {
                    step.querySelector('.step-circle').textContent = stepNum;
                }
            });

            // Update progress line
            const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
            document.getElementById('progressLine').style.width = progress + '%';

            // Scroll to top
            const authBody = document.querySelector('.auth-body');
            if (authBody) {
                authBody.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        // Auto-focus pada step pertama
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('.form-step.active input:not([type="hidden"])');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 300);
            }
        });
    </script>

    @vite(['resources/js/app.js'])
</body>
</html>
