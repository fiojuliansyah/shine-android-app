@extends('layouts.app')

@section('title', request()->route()->getName() === 'attendance.clockin' ? 'Clock In' : 'Clock Out')


@section('content')
    <div class="content mb-0 p-0">
        <div id="camera-container" class="camera-fullscreen">
            <video id="video-element" autoplay muted playsinline></video>
            <canvas id="face-overlay"></canvas>

            <div class="camera-overlay">
                <div class="overlay-status">
                    <div id="face-animation-guide" class="face-animation-container">
                        <div class="face-outline">
                            <div class="face-shape"></div>
                            <div class="face-text">Posisikan wajah Anda di dalam area</div>
                        </div>
                        <div class="animation-instruction">
                            <span id="animation-text">Gerakan wajah Anda perlahan</span>
                            <div class="animation-arrows">
                                <i class="fas fa-arrow-left arrow-left"></i>
                                <i class="fas fa-arrow-right arrow-right"></i>
                                <i class="fas fa-arrow-up arrow-up"></i>
                                <i class="fas fa-arrow-down arrow-down"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="face-detection-status">
                        <div id="loading-models">
                            <div class="spinner-border text-white" role="status">
                                <span class="sr-only"></span>
                            </div>
                            <p class="text-white">Memuat model AI...</p>
                        </div>
                        <div id="looking-for-face" style="display: none;">
                            <p class="text-white">Mencari wajah Anda...</p>
                        </div>
                        <div id="face-detected" style="display: none;">
                            <p class="text-white">Wajah terdeteksi! Memverifikasi...</p>
                        </div>
                    </div>

                    <div id="verification-result" style="display: none;">
                        <div id="verification-success" class="bg-success p-2 rounded text-white text-center" style="display: none;">
                            <i class="fas fa-check-circle"></i> Verifikasi wajah berhasil
                        </div>
                    </div>
                </div>

                <div class="overlay-bottom-info">
                    <div class="info-box location-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-white">Lokasi:</strong>
                            </div>
                            <div id="location-status">
                                <span class="badge bg-secondary">Mencari...</span>
                            </div>
                        </div>
                        <div id="location-name" class="font-12 text-white">-</div>
                    </div>

                    <div class="info-box time-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-white">Waktu:</strong>
                            </div>
                            <div id="current-time" class="text-white">-</div>
                        </div>
                        <div id="current-date" class="font-12 text-white">-</div>
                    </div>
                </div>
            </div>
        </div>

        <form id="attendance-form" method="POST" 
            action="{{ request()->route()->getName() == 'attendance.clockin' ? route('clockin.store') : route('clockout.store') }}">
            @csrf
            <input type="hidden" id="latlong-input" name="latlong" value="">
            <input type="hidden" id="image-input" name="image" value="">
            <button type="button" id="submit-btn" 
                class="btn btn-full btn-l rounded-sm shadow-xl text-uppercase font-900 bg-red-dark" 
                disabled style="display: none;">
                {{ request()->route()->getName() == 'attendance.clockin' ? 'CLOCK IN' : 'CLOCK OUT' }}
            </button>
        </form>

        @if (!Auth::user()->profile || !isset(Auth::user()->profile['face_descriptor']))
            <div class="faceid-warning bg-warning p-3 rounded-sm">
                <h4 class="font-16">Perhatian!</h4>
                <p>Anda belum mendaftarkan Face ID. Silakan daftarkan di halaman profil untuk menggunakan fitur absensi
                    dengan pengenalan wajah.</p>
                <a href="{{ route('profile') }}" class="btn btn-s rounded-sm font-13 mt-2 bg-highlight">Daftarkan
                    Face ID</a>
            </div>
        @endif
    </div>
@endsection

@push('css')
    <style>
        html, body {
            height: 100%;
            overflow: hidden;
        }

        .content.p-0 {
            padding: 0 !important;
            margin: 0 !important;
            height: 100vh;
            width: 100%;
            position: relative;
        }

        .camera-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #000;
            z-index: 1;
        }

        /* Video element to fill screen */
        #video-element {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            position: absolute;
            transform: scaleX(-1);
            top: 0;
            left: 0;
        }

        @media (max-width: 768px) {
            #video-element {
                object-fit: contain;
                background-color: #000;
            }
        }

        .reduce-zoom #video-element {
            transform: scale(0.8);
            transform-origin: center center;
        }

        #face-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
        }

        .camera-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 20;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 15px;
            background: linear-gradient(to bottom, 
                        rgba(0,0,0,0.5) 0%, 
                        rgba(0,0,0,0.2) 20%, 
                        rgba(0,0,0,0.1) 40%, 
                        rgba(0,0,0,0.1) 60%, 
                        rgba(0,0,0,0.2) 80%, 
                        rgba(0,0,0,0.5) 100%);
        }

        .overlay-header {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .overlay-status {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .overlay-bottom-info {
            margin-bottom: 20px;
        }

        .info-box {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .faceid-warning {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 30;
            width: 80%;
            max-width: 320px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .text-white {
            color: white !important;
        }

        .face-detection-status {
            text-align: center;
        }

        #verification-result {
            width: 90%;
            max-width: 320px;
        }

        .header-back {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 30;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 8px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
        }

        .face-animation-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 250px;
            height: 300px;
            z-index: 15;
            pointer-events: none;
        }

        .face-outline {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .face-shape {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 180px;
            height: 220px;
            border: 3px dashed rgba(255, 255, 255, 0.7);
            border-radius: 100px / 120px;
            box-shadow: 0 0 0 2000px rgba(0, 0, 0, 0.3);
        }

        .face-text {
            position: absolute;
            bottom: 10px;
            left: 0;
            width: 100%;
            color: white;
            font-size: 14px;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
        }

        .animation-instruction {
            margin-top: 10px;
            color: white;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
        }

        .animation-arrows {
            margin-top: 10px;
            font-size: 20px;
        }

        .arrow-left {
            animation: moveLeftRight 2s infinite;
            margin-right: 10px;
        }

        .arrow-right {
            animation: moveLeftRight 2s infinite reverse;
            margin-right: 10px;
        }

        .arrow-up {
            animation: moveUpDown 2s infinite;
            margin-right: 10px;
        }

        .arrow-down {
            animation: moveUpDown 2s infinite reverse;
        }

        @keyframes moveLeftRight {
            0%, 100% { transform: translateX(0); opacity: 0.7; }
            50% { transform: translateX(-10px); opacity: 1; }
        }

        @keyframes moveUpDown {
            0%, 100% { transform: translateY(0); opacity: 0.7; }
            50% { transform: translateY(-10px); opacity: 1; }
        }

        .face-shape {
            animation: pulseOutline 2s infinite;
        }

        @keyframes pulseOutline {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
    </style>
@endpush

@include('attendances.partials.face-detection')