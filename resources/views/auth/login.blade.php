<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - WA Blast</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-indigo-50 min-h-screen">
    <!-- Background Animation -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute top-40 left-40 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo and Title -->
            <div class="text-center fade-in">
                
                
            </div>

            <!-- Login Card -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 p-8 slide-in">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">
                        Selamat Datang Kembali
                    </h3>
                    <p class="text-gray-600">
                        Masuk ke akun Anda untuk melanjutkan
                    </p>
                </div>

                <!-- Login Form -->
                <form class="space-y-6" method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                value="{{ old('email') }}"
                                required 
                                class="input-focus block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                placeholder="Masukkan email Anda"
                            >
                        </div>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                class="input-focus block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                placeholder="Masukkan password Anda"
                            >
                            <button 
                                type="button" 
                                id="togglePassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200"
                            >
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Ingat saya
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-purple-600 hover:text-purple-500 transition-colors duration-200">
                                Lupa password?
                            </a>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <div>
                        <button 
                            type="submit" 
                            class="btn-modern group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200"
                            id="loginBtn"
                        >
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt text-purple-300 group-hover:text-purple-200 transition-colors duration-200"></i>
                            </span>
                            <span id="loginBtnText">Masuk ke Sistem</span>
                            <div class="loading-spinner hidden ml-2" id="loginSpinner"></div>
                        </button>
                    </div>
                </form>

                <!-- Demo Credentials -->
                <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <span class="text-sm font-medium text-blue-800">Demo Credentials</span>
                    </div>
                    <div class="text-xs text-blue-700 space-y-1">
                        <div><strong>Email:</strong> admin@wablast.com</div>
                        <div><strong>Password:</strong> admin123</div>
                    </div>
                </div>

                <!-- Social Login (Optional) -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Atau masuk dengan</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button type="button" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-200">
                            <i class="fab fa-google text-red-500 mr-2"></i>
                            Google
                        </button>
                        <button type="button" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-200">
                            <i class="fab fa-github text-gray-800 mr-2"></i>
                            GitHub
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center fade-in">
                <p class="text-sm text-gray-600">
                    Belum punya akun? 
                    <a href="#" class="font-medium text-purple-600 hover:text-purple-500 transition-colors duration-200">
                        Daftar sekarang
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle functionality
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });

            // Form submission with loading state
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const loginBtnText = document.getElementById('loginBtnText');
            const loginSpinner = document.getElementById('loginSpinner');

            loginForm.addEventListener('submit', function() {
                loginBtn.disabled = true;
                loginBtnText.textContent = 'Memproses...';
                loginSpinner.classList.remove('hidden');
            });

            // Input focus animations
            const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-purple-500', 'ring-opacity-50');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-purple-500', 'ring-opacity-50');
                });
            });

            // Floating animation for background elements
            const blobs = document.querySelectorAll('.animate-blob');
            blobs.forEach((blob, index) => {
                blob.style.animationDelay = `${index * 2}s`;
            });
        });
    </script>

    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        .slide-in {
            animation: slideIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
</body>
</html> 