<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Klinik GKN</title>

    @vite(['resources/css/app.css'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Modern Login Design with Glass Morphism */
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
        
        /* Overlay dengan gradient lebih soft */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 51, 102, 0.25) 100%);
            z-index: 0;
        }
        
        /* Glass Morphism Card - Fully Transparent */
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 900px;
            padding: 20px;
            animation: slideIn 0.6s ease-out;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            display: grid;
            grid-template-columns: 400px 1fr;
            min-height: 550px;
        }
        
        /* Logo Header - Transparent with gradient overlay */
        .login-header {
            text-align: center;
            padding: 60px 40px;
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.4) 0%, rgba(33, 136, 56, 0.5) 100%);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-right: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .logo-circle {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .logo-circle i {
            font-size: 50px;
            color: #28a745;
        }
        
        .login-header h2 {
            color: white;
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 12px;
            text-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        }
        
        .login-header p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 15px;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        /* Form Body - Transparent with white overlay */
        .login-body {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }
        
        /* Input Groups with Icons */
        .input-group-modern {
            position: relative;
            margin-bottom: 24px;
        }
        
        .input-group-modern label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 18px;
            z-index: 1;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .input-with-icon input:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.1);
        }
        
        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 2;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.3s ease;
            width: 32px;
            height: 32px;
        }
        
        .password-toggle i {
            font-size: 18px;
            line-height: 1;
        }
        
        .password-toggle:hover {
            color: #28a745;
            background: rgba(40, 167, 69, 0.08);
        }
        
        /* Remember & Forgot */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
        }
        
        .form-check-modern {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-check-modern input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #28a745;
        }
        
        .form-check-modern label {
            margin: 0;
            cursor: pointer;
            font-weight: 500;
            color: #555;
        }
        
        .forgot-link {
            color: #28a745;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .forgot-link:hover {
            color: #218838;
            text-decoration: underline;
        }
        
        /* Modern Button */
        .btn-modern {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(40, 167, 69, 0.4);
        }
        
        .btn-modern:active {
            transform: translateY(0);
        }
        
        /* Alert Styles */
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            border: none;
            animation: slideDown 0.4s ease;
        }
        
        .alert-danger {
            background: #fee;
            color: #c33;
        }
        
        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .login-card {
                grid-template-columns: 1fr;
                min-height: auto;
            }
            
            .login-header {
                padding: 40px 30px;
            }
            
            .login-body {
                padding: 40px 30px;
            }
        }
        
        @media (max-width: 576px) {
            .login-container {
                padding: 10px;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-body {
                padding: 30px 24px;
            }
            
            .logo-circle {
                width: 80px;
                height: 80px;
            }
            
            .logo-circle i {
                font-size: 40px;
            }
            
            .login-header h2 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header with Logo -->
            <div class="login-header">
                <div class="logo-circle">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h2>Klinik GKN</h2>
                <p>Admin & Dokter</p>
            </div>
            
            <!-- Form Body -->
            <div class="login-body">
                <h3 style="text-align: left; margin-bottom: 8px; font-size: 26px; color: #333; font-weight: 700;">Login Admin/Dokter</h3>
                <p style="text-align: left; color: #666; margin-bottom: 35px; font-size: 15px;">Akses khusus staff pengadaan dan medis</p>

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

                <form action="{{ route('admin.login') }}" method="POST">
                    @csrf
                    
                    <!-- Login Input -->
                    <div class="input-group-modern">
                        <label for="login">NIP atau Email</label>
                        <div class="input-with-icon">
                            <i class="bi bi-person-fill"></i>
                            <input type="text" id="login" name="login" value="{{ old('login') }}" 
                                   placeholder="Masukkan NIP atau Email" required autofocus>
                        </div>
                    </div>
                    
                    <!-- Password Input -->
                    <div class="input-group-modern">
                        <label for="password">Password</label>
                        <div class="input-with-icon">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" id="password" name="password" 
                                   placeholder="Masukkan password" required>
                            <span class="password-toggle" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="password-icon"></i>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Remember & Forgot -->
                    <div class="form-options">
                        <div class="form-check-modern">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">Ingat Saya</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Lupa Password?</a>
                        @endif
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-modern">
                        <i class="bi bi-shield-check me-2"></i>Login Sebagai Admin
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Floating Feedback Button -->
    <div class="floating-feedback-container">
        <button type="button" class="btn-floating-feedback" id="btnFeedback" title="Akses Feedback Pasien">
            <i class="bi bi-chat-heart-fill"></i>
            <span class="feedback-text d-none d-md-inline ms-2">Feedback Pasien</span>
        </button>
        
        <!-- Dropdown Lokasi Klinik -->
        <div class="feedback-dropdown" id="feedbackDropdown" style="display: none;">
            <div class="dropdown-header">
                <i class="bi bi-hospital me-2"></i>
                <strong>Pilih Lokasi Klinik:</strong>
            </div>
            <div class="dropdown-body">
                @php
                    // Ambil dari tabel lokasi_klinik (Klinik GKN 1, Klinik GKN 2)
                    $lokasiKlinik = \App\Models\LokasiKlinik::orderBy('id')->get();
                @endphp
                
                @forelse($lokasiKlinik as $lok)
                <a href="{{ route('feedback.form', ['lokasi' => $lok->id]) }}" 
                   class="dropdown-item-custom"
                   target="_blank">
                    <i class="bi bi-building-add me-2"></i>
                    <span>{{ $lok->nama_lokasi ?? 'Klinik GKN ' . $lok->id }}</span>
                    <i class="bi bi-box-arrow-up-right ms-auto"></i>
                </a>
                @empty
                <div class="dropdown-item-custom text-muted">
                    <i class="bi bi-info-circle me-2"></i>
                    Belum ada lokasi klinik
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
    /* Floating Button Container */
    .floating-feedback-container {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999;
    }

    /* Floating Button */
    .btn-floating-feedback {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 15px 25px;
        font-size: 16px;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: pulse-feedback 2s infinite;
    }

    .btn-floating-feedback:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.6);
        background: linear-gradient(135deg, #218838 0%, #28a745 100%);
    }

    .btn-floating-feedback i {
        font-size: 1.2em;
    }

    /* Pulse Animation */
    @keyframes pulse-feedback {
        0% {
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
        50% {
            box-shadow: 0 4px 25px rgba(40, 167, 69, 0.8);
        }
        100% {
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
    }

    /* Dropdown Lokasi */
    .feedback-dropdown {
        position: absolute;
        bottom: 70px;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        min-width: 320px;
        max-height: 400px;
        overflow: hidden;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-header {
        padding: 15px 20px;
        border-bottom: 2px solid #f0f0f0;
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
        display: flex;
        align-items: center;
        border-radius: 12px 12px 0 0;
    }

    .dropdown-body {
        padding: 10px 0;
        max-height: 320px;
        overflow-y: auto;
    }

    .dropdown-item-custom {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
        gap: 8px;
    }

    .dropdown-item-custom:hover {
        background: linear-gradient(90deg, #f0f9f4 0%, #fff 100%);
        border-left-color: #28a745;
        padding-left: 25px;
    }

    .dropdown-item-custom i:first-child {
        color: #28a745;
        font-size: 1.1em;
    }

    .dropdown-item-custom .bi-box-arrow-up-right {
        font-size: 0.9em;
        color: #999;
    }

    .dropdown-item-custom span {
        flex: 1;
    }

    /* Scrollbar Custom */
    .dropdown-body::-webkit-scrollbar {
        width: 6px;
    }

    .dropdown-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .dropdown-body::-webkit-scrollbar-thumb {
        background: #28a745;
        border-radius: 10px;
    }

    .dropdown-body::-webkit-scrollbar-thumb:hover {
        background: #218838;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .floating-feedback-container {
            bottom: 20px;
            right: 20px;
        }
        
        .btn-floating-feedback {
            padding: 12px 20px;
            font-size: 14px;
            border-radius: 40px;
        }
        
        .feedback-dropdown {
            min-width: 280px;
            right: -10px;
        }
        
        .feedback-text {
            display: none !important;
        }
    }

    /* Mobile - Full Width Dropdown */
    @media (max-width: 480px) {
        .feedback-dropdown {
            right: 20px;
            left: 20px;
            min-width: auto;
        }
    }
    </style>

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
    
    document.addEventListener('DOMContentLoaded', function() {
        const btnFeedback = document.getElementById('btnFeedback');
        const feedbackDropdown = document.getElementById('feedbackDropdown');
        
        if (btnFeedback && feedbackDropdown) {
            // Toggle dropdown
            btnFeedback.addEventListener('click', function(e) {
                e.stopPropagation();
                const isVisible = feedbackDropdown.style.display !== 'none';
                feedbackDropdown.style.display = isVisible ? 'none' : 'block';
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!feedbackDropdown.contains(e.target) && e.target !== btnFeedback && !btnFeedback.contains(e.target)) {
                    feedbackDropdown.style.display = 'none';
                }
            });
            
            // Prevent dropdown close when clicking inside
            feedbackDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
    </script>

    @vite(['resources/js/app.js'])
</body>
</html>
