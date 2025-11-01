<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WA Blast')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @include('components.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Mobile Header -->
            @include('components.mobile-header')
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="fade-in">
                    @yield('content')
                </div>
            </main>
            
            <!-- Flash Messages -->
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Decode HTML entities in the message
                        const message = @json(session('success'));
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: message,
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    });
                </script>
            @endif
            
            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Decode HTML entities in the message
                        const message = @json(session('error'));
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message,
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: true
                        });
                    });
                </script>
            @endif
            
            @if(session('warning'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Decode HTML entities in the message
                        const message = @json(session('warning'));
                        
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan!',
                            text: message,
                            timer: 4000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    });
                </script>
            @endif
            
            @if(session('info'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Decode HTML entities in the message
                        const message = @json(session('info'));
                        
                        Swal.fire({
                            icon: 'info',
                            title: 'Informasi!',
                            text: message,
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    });
                </script>
            @endif
        </div>
    </div>
    
    <script>
        // Add loading states to buttons
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('button[type="submit"], button[onclick*="create"], button[onclick*="delete"], button[onclick*="retry"]');
            
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    if (!this.classList.contains('loading')) {
                        this.classList.add('loading');
                        const originalText = this.innerHTML;
                        this.innerHTML = '<div class="loading-spinner inline-block mr-2"></div>Loading...';
                        this.disabled = true;
                        
                        // Reset after 3 seconds if no response
                        setTimeout(() => {
                            if (this.classList.contains('loading')) {
                                this.classList.remove('loading');
                                this.innerHTML = originalText;
                                this.disabled = false;
                            }
                        }, 3000);
                    }
                });
            });
        });
        
        // Add smooth transitions to cards
        const cards = document.querySelectorAll('.bg-white.rounded-xl');
        cards.forEach(card => {
            card.classList.add('card-hover');
        });
        
        // Add button animations
        const actionButtons = document.querySelectorAll('button:not([type="submit"])');
        actionButtons.forEach(button => {
            button.classList.add('btn-animate');
        });
        
        // Modal animations
        const modals = document.querySelectorAll('[id$="Modal"]');
        modals.forEach(modal => {
            modal.addEventListener('show', function() {
                this.classList.add('modal-enter');
            });
        });
        
        // Status badge animations
        const statusBadges = document.querySelectorAll('.inline-flex.items-center.px-3.py-1.rounded-full');
        statusBadges.forEach(badge => {
            badge.classList.add('status-badge');
        });
        
        // Progress bar animations
        const progressBars = document.querySelectorAll('.bg-gradient-to-r.from-blue-500.to-blue-600');
        progressBars.forEach(bar => {
            bar.classList.add('progress-animate');
        });
    </script>
</body>
</html> 