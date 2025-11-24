<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pasien - Klinik GKN</title>

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
        }
        
        /* Logo Header - Transparent with gradient overlay */
        .login-header {
            text-align: center;
            padding: 41px 35px;
            background: linear-gradient(135deg, rgba(0, 102, 204, 0.4) 0%, rgba(0, 77, 153, 0.5) 100%);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-right: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .logo-circle {
            width: 68px;
            height: 68px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .logo-circle i {
            font-size: 34px;
            color: #0066cc;
        }
        
        .login-header h2 {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px;
            text-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        }
        
        .login-header p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 13px;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        /* Form Body - Transparent with white overlay */
        .login-body {
            padding: 41px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            min-height: 374px;
        }
        
        /* Input Groups with Icons */
        .input-group-modern {
            position: relative;
            margin-bottom: 16px;
        }
        
        .input-group-modern label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 16px;
            z-index: 1;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 10px 14px 10px 42px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 13px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .input-with-icon input:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.1);
        }
        
        /* Password Toggle */
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
            border-radius: 4px;
            transition: all 0.3s ease;
            width: 28px;
            height: 28px;
        }
        
        .password-toggle i {
            font-size: 16px;
            line-height: 1;
        }
        
        .password-toggle:hover {
            color: #0066cc;
            background: rgba(0, 102, 204, 0.08);
        }
        
        /* Remember & Forgot */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            font-size: 13px;
        }
        
        .form-check-modern {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-check-modern input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #0066cc;
        }
        
        .form-check-modern label {
            margin: 0;
            cursor: pointer;
            font-weight: 500;
            color: #555;
        }
        
        .forgot-link {
            color: #0066cc;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .forgot-link:hover {
            color: #004d99;
            text-decoration: underline;
        }
        
        /* Modern Button */
        .btn-modern {
            width: 100%;
            padding: 11px;
            background: linear-gradient(135deg, #0066cc 0%, #004d99 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.3);
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 102, 204, 0.4);
        }
        
        .btn-modern:active {
            transform: translateY(0);
        }
        
        /* Divider */
        .divider {
            text-align: center;
            margin: 21px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
        }
        
        .divider span {
            background: white;
            padding: 0 16px;
            color: #999;
            font-size: 13px;
            position: relative;
            z-index: 1;
        }
        
        /* Register Link */
        .register-link {
            text-align: center;
            padding: 16px 0 0;
        }
        
        .register-link a {
            color: #0066cc;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s;
        }
        
        .register-link a:hover {
            color: #004d99;
            text-decoration: underline;
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
        
        .alert-success {
            background: #efe;
            color: #3c3;
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
                    <i class="bi bi-hospital"></i>
                </div>
                <h2>Klinik GKN</h2>
                <p>Selamat Datang</p>
            </div>
            
            <!-- Form Body -->
            <div class="login-body">
                <h3 style="text-align: left; margin-bottom: 8px; font-size: 26px; color: #333; font-weight: 700;">Login Pasien</h3>
                <p style="text-align: left; color: #666; margin-bottom: 35px; font-size: 15px;">Masuk dengan NIP Anda</p>

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

            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <!-- NIP Input -->
                <div class="input-group-modern">
                    <label for="nip">NIP</label>
                    <div class="input-with-icon">
                        <i class="bi bi-person-badge"></i>
                        <input type="text" id="nip" name="nip" value="{{ old('nip') }}" 
                               placeholder="Masukkan NIP Anda" required autofocus>
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
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
            </form>
            
            <!-- Divider -->
            <div class="divider">
                <span>Belum punya akun?</span>
            </div>
            
            <!-- Register Link -->
            <div class="register-link">
                <a href="{{ route('register') }}">
                    <i class="bi bi-person-plus-fill me-1"></i>Daftar Sekarang
                </a>
            </div>
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
