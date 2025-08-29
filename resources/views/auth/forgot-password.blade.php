<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>

    @vite(['resources/css/app.css'])

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
        .forgot-card {
            width: 100%;
            max-width: 450px;
        }
    </style>
</head>
<body>
    <div class="card shadow-sm forgot-card">
        <div class="card-body p-5">
            <h2 class="card-title text-center mb-3">Lupa Password</h2>
            <p class="text-center text-muted small mb-4">
                Masukkan alamat email Anda dan kami akan mengirimkan link untuk mereset password Anda.
            </p>

            <x-auth-session-status class="alert alert-success mb-4" :status="session('status')" />

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Kirim Link Reset Password</button>
                </div>
                <div class="text-center mt-3">
                    <small><a href="{{ route('login') }}">Kembali ke Login</a></small>
                </div>
            </form>
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>
</html>
