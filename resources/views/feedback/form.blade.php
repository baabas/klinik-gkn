<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Feedback Pasien - Klinik GKN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        .feedback-container {
            background: white;
            border-radius: 25px;
            padding: 50px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
            max-width: 700px;
            width: 90%;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .waiting-screen {
            text-align: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        .waiting-screen .spinner-border {
            width: 5rem;
            height: 5rem;
            border-width: 0.4em;
        }

        .waiting-screen h3 {
            margin-top: 20px;
            color: #495057;
        }

        .waiting-screen p {
            color: #6c757d;
            font-size: 16px;
        }

        #feedback-form h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-weight: 700;
            font-size: 32px;
        }

        .alert-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .alert-info h5 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .alert-info small {
            font-size: 14px;
            opacity: 0.9;
        }

        .emoji-rating {
            display: flex;
            justify-content: space-around;
            margin: 40px 0;
            padding: 20px 0;
        }

        .emoji-btn {
            font-size: 70px;
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 0.4;
            padding: 15px;
            border-radius: 50%;
            user-select: none;
            position: relative;
        }

        .emoji-btn:hover {
            transform: scale(1.3);
            opacity: 1;
        }

        .emoji-btn.active {
            opacity: 1;
            transform: scale(1.4);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
        }

        .emoji-btn::after {
            content: attr(title);
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s;
            color: #666;
        }

        .emoji-btn:hover::after,
        .emoji-btn.active::after {
            opacity: 1;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 16px;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 18px;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.6);
        }

        .thank-you-screen {
            text-align: center;
            animation: bounceIn 0.8s;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .thank-you-screen h1 {
            font-size: 100px;
            margin-bottom: 20px;
        }

        .thank-you-screen h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .thank-you-screen p {
            color: #6c757d;
            font-size: 18px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 12px;
            margin-top: 15px;
            text-align: center;
            display: none;
        }
    </style>
</head>
<body>
    <div class="feedback-container">
        {{-- Waiting Screen --}}
        <div id="waiting-screen" class="waiting-screen">
            <div class="spinner-border text-primary mb-4" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h3>Menunggu Pasien Berikutnya...</h3>
            <p class="text-muted">Sistem akan otomatis menampilkan form feedback</p>
        </div>

        {{-- Feedback Form --}}
        <div id="feedback-form" style="display: none;">
            <h2>Bagaimana Pelayanan Kami?</h2>
            
            <div class="alert alert-info text-center">
                <h5 class="mb-0" id="pasien-name"></h5>
            </div>

            <form id="form-feedback">
                <input type="hidden" id="id_rekam_medis" name="id_rekam_medis">
                <input type="hidden" id="rating" name="rating">

                <div class="emoji-rating">
                    <span class="emoji-btn" data-rating="1" title="Sangat Tidak Puas">😡</span>
                    <span class="emoji-btn" data-rating="2" title="Tidak Puas">😞</span>
                    <span class="emoji-btn" data-rating="3" title="Cukup">😐</span>
                    <span class="emoji-btn" data-rating="4" title="Puas">😊</span>
                    <span class="emoji-btn" data-rating="5" title="Sangat Puas">😍</span>
                </div>

                <div class="mb-4">
                    <label class="form-label">Komentar (Opsional)</label>
                    <textarea class="form-control" name="komentar" rows="4" placeholder="Berikan masukan Anda untuk meningkatkan pelayanan kami..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg">
                    Kirim Feedback
                </button>

                <div class="error-message" id="error-message"></div>
            </form>
        </div>

        {{-- Thank You Screen --}}
        <div id="thank-you-screen" class="thank-you-screen" style="display: none;">
            <h1>😊</h1>
            <h2>Terima Kasih!</h2>
            <p class="text-muted">Feedback Anda sangat berharga bagi kami</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let selectedRating = 0;
        let checkInterval = null;

        /**
         * Auto check untuk pending feedback setiap 5 detik
         */
        function checkPendingFeedback() {
            $.ajax({
                url: '{{ route("api.feedback.check-pending") }}',
                method: 'GET',
                success: function(response) {
                    if (response.has_pending) {
                        showFeedbackForm(response.rekam_medis);
                    } else {
                        showWaitingScreen();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking pending feedback:', error);
                    // Tetap tampilkan waiting screen jika ada error
                    showWaitingScreen();
                }
            });
        }

        /**
         * Tampilkan waiting screen
         */
        function showWaitingScreen() {
            $('#waiting-screen').fadeIn(300);
            $('#feedback-form').hide();
            $('#thank-you-screen').hide();
        }

        /**
         * Tampilkan form feedback dengan data pasien
         */
        function showFeedbackForm(rekamMedis) {
            // Stop checking sementara
            if (checkInterval) {
                clearInterval(checkInterval);
                checkInterval = null;
            }

            $('#waiting-screen').hide();
            $('#feedback-form').fadeIn(300);
            $('#thank-you-screen').hide();

            // Isi data pasien - hanya tampilkan nama saja
            $('#id_rekam_medis').val(rekamMedis.id);
            $('#pasien-name').text(rekamMedis.pasien.nama);

            // Reset form
            resetForm();
        }

        /**
         * Tampilkan thank you screen
         */
        function showThankYou() {
            $('#waiting-screen').hide();
            $('#feedback-form').hide();
            $('#thank-you-screen').fadeIn(300);

            // Kembali ke waiting screen setelah 3 detik dan restart polling
            setTimeout(function() {
                startPolling();
                checkPendingFeedback();
            }, 3000);
        }

        /**
         * Reset form feedback
         */
        function resetForm() {
            selectedRating = 0;
            $('.emoji-btn').removeClass('active');
            $('#rating').val('');
            $('textarea[name="komentar"]').val('');
            $('#error-message').hide();
        }

        /**
         * Tampilkan error message
         */
        function showError(message) {
            $('#error-message').text(message).fadeIn(300);
            setTimeout(function() {
                $('#error-message').fadeOut(300);
            }, 5000);
        }

        /**
         * Start polling untuk check pending feedback
         */
        function startPolling() {
            if (!checkInterval) {
                checkInterval = setInterval(checkPendingFeedback, 5000);
            }
        }

        /**
         * Stop polling
         */
        function stopPolling() {
            if (checkInterval) {
                clearInterval(checkInterval);
                checkInterval = null;
            }
        }

        // ===== Event Handlers =====

        /**
         * Handle click pada emoji rating
         */
        $(document).on('click', '.emoji-btn', function() {
            $('.emoji-btn').removeClass('active');
            $(this).addClass('active');
            selectedRating = $(this).data('rating');
            $('#rating').val(selectedRating);
        });

        /**
         * Handle submit form feedback
         */
        $('#form-feedback').submit(function(e) {
            e.preventDefault();

            // Validasi rating dipilih
            if (selectedRating === 0) {
                showError('Silakan pilih rating terlebih dahulu');
                return;
            }

            // Kirim feedback
            $.ajax({
                url: '{{ route("feedback.store") }}',
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showThankYou();
                    } else {
                        showError(response.message || 'Terjadi kesalahan');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    showError(errorMessage);
                }
            });
        });

        // ===== Initialization =====

        /**
         * Jalankan saat dokumen ready
         */
        $(document).ready(function() {
            // Check immediately
            checkPendingFeedback();
            
            // Start polling every 5 seconds
            startPolling();
        });

        /**
         * Cleanup saat window di-unload
         */
        $(window).on('beforeunload', function() {
            stopPolling();
        });
    </script>
</body>
</html>
