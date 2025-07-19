@extends('layouts.app')

@section('title', 'Dashboard - WhatsApp Blast')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                        <p class="mt-2 text-sm text-gray-600">Overview lengkap sistem WhatsApp Blast Anda</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('phonebook.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-purple-700 bg-purple-100 hover:bg-purple-200 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Kelola Kontak
                        </a>
                        <a href="{{ route('campaigns') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Buat Campaign
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Total Sessions -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-600">Total Sessions</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_sessions'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('sessions.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat semua →</a>
                    </div>
                </div>
            </div>

            <!-- Connected Sessions -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-600">Connected Sessions</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['connected_sessions'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Aktif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Total Campaigns -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM11 13.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM3 5.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM3 13.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-600">Total Campaigns</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_campaigns'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('campaigns') }}" class="text-sm text-purple-600 hover:text-purple-800 font-medium">Lihat semua →</a>
                    </div>
                </div>
            </div>

            <!-- Sent Messages -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-600">Sent Messages</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['sent_messages'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('messages') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Lihat semua →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-8">
            <!-- Total Contacts -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-teal-500 to-teal-600 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-600">Total Kontak</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_contacts'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('phonebook.index') }}" class="text-sm text-teal-600 hover:text-teal-800 font-medium">Kelola kontak →</a>
                    </div>
                </div>
            </div>

            <!-- Total Groups -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-600">Total Grup</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_groups'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('phonebook.index') }}" class="text-sm text-orange-600 hover:text-orange-800 font-medium">Lihat grup →</a>
                    </div>
                </div>
            </div>

            <!-- Success Rate -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @php
                                $successRate = $stats['success_rate_percentage'] ?? 0;
                                $iconBgClass = 'bg-gradient-to-r from-red-500 to-red-600';
                                if ($successRate >= 80) {
                                    $iconBgClass = 'bg-gradient-to-r from-green-500 to-green-600';
                                } elseif ($successRate >= 60) {
                                    $iconBgClass = 'bg-gradient-to-r from-yellow-500 to-yellow-600';
                                } elseif ($successRate >= 40) {
                                    $iconBgClass = 'bg-gradient-to-r from-orange-500 to-orange-600';
                                }
                            @endphp
                            <div class="w-12 h-12 {{ $iconBgClass }} rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-600">Success Rate</p>
                            @php
                                $successRate = $stats['success_rate_percentage'] ?? 0;
                                $textColor = 'text-red-600';
                                if ($successRate >= 80) {
                                    $textColor = 'text-green-600';
                                } elseif ($successRate >= 60) {
                                    $textColor = 'text-yellow-600';
                                } elseif ($successRate >= 40) {
                                    $textColor = 'text-orange-600';
                                }
                            @endphp
                            <p class="text-2xl font-bold {{ $textColor }}">{{ $stats['success_rate'] ?? '0%' }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $successRate = $stats['success_rate_percentage'] ?? 0;
                                $progressColor = 'bg-red-500';
                                if ($successRate >= 80) {
                                    $progressColor = 'bg-green-500';
                                } elseif ($successRate >= 60) {
                                    $progressColor = 'bg-yellow-500';
                                } elseif ($successRate >= 40) {
                                    $progressColor = 'bg-orange-500';
                                }
                            @endphp
                            <div class="{{ $progressColor }} h-2 rounded-full transition-all duration-300" style="width: {{ $successRate }}%"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-100 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('phonebook.grabber') }}" class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all duration-200">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-blue-900">Grab Kontak</h4>
                            <p class="text-xs text-blue-700">Ambil kontak dari WhatsApp</p>
                        </div>
                    </a>

                    <a href="{{ route('phonebook.create') }}" class="flex items-center p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg hover:from-green-100 hover:to-green-200 transition-all duration-200">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-green-900">Tambah Kontak</h4>
                            <p class="text-xs text-green-700">Tambah kontak manual</p>
                        </div>
                    </a>

                    <a href="{{ route('campaigns') }}" class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg hover:from-purple-100 hover:to-purple-200 transition-all duration-200">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-purple-900">Buat Campaign</h4>
                            <p class="text-xs text-purple-700">Kirim pesan blast</p>
                        </div>
                    </a>

                    <a href="{{ route('integration.index') }}" class="flex items-center p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg hover:from-orange-100 hover:to-orange-200 transition-all duration-200">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-orange-900">API Integration</h4>
                            <p class="text-xs text-orange-700">Setup API & Webhook</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Sessions -->
            <div class="bg-white shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Sessions</h3>
                        <a href="{{ route('sessions.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat semua</a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recent_sessions ?? [] as $session)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">{{ substr($session->phone_number ?? 'N/A', -2) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $session->phone_number ?? 'Unknown' }}</div>
                                    <div class="text-xs text-gray-500">{{ $session->session_id }}</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($session->status === 'connected') bg-green-100 text-green-800
                                    @elseif($session->status === 'connecting') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <p class="text-sm text-gray-500">Belum ada session yang aktif</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Campaigns -->
            <div class="bg-white shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Campaigns</h3>
                        <a href="{{ route('campaigns') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat semua</a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recent_campaigns ?? [] as $campaign)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $campaign->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $campaign->total_count ?? 0 }} recipients</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($campaign->status === 'completed') bg-green-100 text-green-800
                                    @elseif($campaign->status === 'running') bg-blue-100 text-blue-800
                                    @elseif($campaign->status === 'scheduled') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM11 13.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM3 5.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM3 13.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2z"></path>
                            </svg>
                            <p class="text-sm text-gray-500">Belum ada campaign yang dibuat</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for interactivity -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to cards
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add click effects to quick action buttons
    const quickActions = document.querySelectorAll('[href*="route"]');
    quickActions.forEach(action => {
        action.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});
</script>

<style>
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>
@endsection 