<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Klinik GKN'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Hapus padding-top karena tidak ada top navbar */
        body { padding-top: 0; }
        .sidebar { position: fixed; top: 0; bottom: 0; left: 0; z-index: 1000; width: 240px; box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1); }
        .main-content { margin-left: 240px; }
        
        /* Enhanced Sidebar Styles with Hover Effects */
        .sidebar .nav-link {
            position: relative;
            border-radius: 8px;
            margin: 2px 8px;
            padding: 12px 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
            color: #495057;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Fix untuk button yang digunakan sebagai dropdown toggle */
        .sidebar button.nav-link {
            background: transparent;
            cursor: pointer;
        }
        .sidebar button.nav-link:hover {
            background-color: rgba(13, 110, 253, 0.08);
        }
        
        /* Hover effects for all nav links */
        .sidebar .nav-link:hover {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.08);
            border-left-color: #0d6efd;
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
        }
        
        /* Focus effects */
        .sidebar .nav-link:focus {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.12);
            border-left-color: #0d6efd;
            outline: none;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
        }
        
        /* Active state */
        .sidebar .nav-link.active {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.15);
            border-left-color: #0d6efd;
            font-weight: 600;
            box-shadow: 0 2px 12px rgba(13, 110, 253, 0.2);
        }
        
        /* Icon hover effects */
        .sidebar .nav-link:hover i {
            transform: scale(1.1);
            transition: transform 0.2s ease-in-out;
        }
        
        /* Dropdown toggle specific styles */
        .sidebar .nav-link[data-bs-toggle="collapse"] {
            position: relative;
        }
        .sidebar .nav-link[data-bs-toggle="collapse"] .bi-chevron-down {
            transition: transform 0.3s ease-in-out;
        }
        .sidebar .nav-link[data-bs-toggle="collapse"][aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }
        .sidebar .nav-link[data-bs-toggle="collapse"]:hover .bi-chevron-down {
            color: #0d6efd;
        }
        
        /* Submenu styles */
        .sidebar .collapse .nav-link {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 1px 16px 1px 24px;
            padding: 8px 16px;
            border-left: 2px solid transparent;
            border-radius: 6px;
            font-weight: 400;
        }
        .sidebar .collapse .nav-link:hover {
            color: #0d6efd;
            border-left-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
            transform: translateX(4px);
        }
        .sidebar .collapse .nav-link:focus {
            color: #0d6efd;
            border-left-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.12);
            outline: none;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.2);
        }
        .sidebar .collapse .nav-link.active {
            color: #0d6efd;
            border-left-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.15);
            font-weight: 500;
        }
        
        /* Smooth animation for sidebar container */
        .sidebar {
            transition: all 0.3s ease-in-out;
        }
        
        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Add subtle pulse animation for active items */
        @keyframes subtle-pulse {
            0% { box-shadow: 0 2px 12px rgba(13, 110, 253, 0.2); }
            50% { box-shadow: 0 2px 16px rgba(13, 110, 253, 0.3); }
            100% { box-shadow: 0 2px 12px rgba(13, 110, 253, 0.2); }
        }
        
        .sidebar .nav-link.active {
            animation: subtle-pulse 2s ease-in-out infinite;
        }
        
        /* Hover effect for entire sidebar */
        .sidebar:hover {
            box-shadow: inset -1px 0 0 rgba(13, 110, 253, 0.2), 2px 0 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Improve typography */
        .sidebar .nav-link {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 0.3px;
        }
        
        /* Add ripple effect on click */
        .sidebar .nav-link:active {
            transform: scale(0.98);
            transition: transform 0.1s ease-in-out;
        }
        
        /* Chevron animation for dropdown menus */
        .sidebar button.nav-link .bi-chevron-down {
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar button.nav-link[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }
        
        /* Smooth collapse transition */
        .sidebar .collapse {
            transition: height 0.35s ease;
        }
    </style>

    {{-- ================== PERBAIKAN DI SINI ================== --}}
    {{-- Menambahkan @stack('styles') untuk memuat CSS dari child view --}}
    @stack('styles')
    {{-- ======================================================= --}}

</head>
<body class="bg-light">
    {{-- Top navbar dihapus - tidak diperlukan --}}
    <div class="container-fluid">
        <div class="row">
            @include('layouts.navigation-sidebar')
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content pt-4">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Enhanced Sidebar Interaction Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced keyboard navigation for sidebar
            const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
            
            sidebarLinks.forEach(link => {
                // Add tabindex for better keyboard navigation
                if (!link.hasAttribute('tabindex')) {
                    link.setAttribute('tabindex', '0');
                }
                
                // Enhanced focus handling
                link.addEventListener('focus', function() {
                    this.classList.add('keyboard-focus');
                });
                
                link.addEventListener('blur', function() {
                    this.classList.remove('keyboard-focus');
                });
                
                // Enhanced keyboard support
                link.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
                
                // Add smooth scroll to active item on page load
                if (link.classList.contains('active')) {
                    setTimeout(() => {
                        link.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }, 500);
                }
            });
            
            // Enhanced dropdown behavior
            const dropdownToggles = document.querySelectorAll('.sidebar [data-bs-toggle="collapse"]');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('data-bs-target'));
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    
                    // Toggle expanded state
                    this.setAttribute('aria-expanded', !isExpanded);
                    
                    // Smooth toggle
                    if (target) {
                        if (isExpanded) {
                            target.classList.remove('show');
                        } else {
                            target.classList.add('show');
                        }
                    }
                });
            });
        });
    </script>
    
    {{-- Additional CSS for keyboard focus --}}
    <style>
        .sidebar .nav-link.keyboard-focus {
            outline: 2px solid #0d6efd;
            outline-offset: 2px;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2);
        }
        
        /* Smooth entrance animation for sidebar */
        .sidebar {
            animation: slideInLeft 0.4s ease-out;
        }
        
        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>

    {{-- Ini sudah ada dan benar, tidak perlu diubah --}}
    @stack('scripts')

    {{-- Fix untuk dropdown yang memerlukan double click --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil semua link dengan data-bs-toggle="collapse"
            const collapseToggles = document.querySelectorAll('[data-bs-toggle="collapse"]');
            
            collapseToggles.forEach(function(toggle) {
                // Hapus href yang bisa menyebabkan konflik
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Ambil target collapse
                    const targetId = this.getAttribute('data-bs-target');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        // Toggle collapse menggunakan Bootstrap Collapse API
                        const bsCollapse = new bootstrap.Collapse(targetElement, {
                            toggle: true
                        });
                    }
                });
            });
        });
    </script>

</body>
</html>
