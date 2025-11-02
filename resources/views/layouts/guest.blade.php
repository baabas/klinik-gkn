<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Klinik GKN') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            html, body { height: 100%; }
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #f8f9fa; /* Warna latar abu-abu muda */
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div>
            {{ $slot }}
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 25px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: pulse-feedback 2s infinite;
        }

        .btn-floating-feedback:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-floating-feedback i {
            font-size: 1.2em;
        }

        /* Pulse Animation */
        @keyframes pulse-feedback {
            0% {
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            }
            50% {
                box-shadow: 0 4px 25px rgba(102, 126, 234, 0.8);
            }
            100% {
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(90deg, #f8f9ff 0%, #fff 100%);
            border-left-color: #667eea;
            padding-left: 25px;
        }

        .dropdown-item-custom i:first-child {
            color: #667eea;
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
            background: #667eea;
            border-radius: 10px;
        }

        .dropdown-body::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
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
    </body>
</html>
