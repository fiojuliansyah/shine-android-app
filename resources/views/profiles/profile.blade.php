@extends('layouts.app')

@section('title','Profil')
@section('content')
    <div class="page-content">
        <div class="content mb-0">
            <form id="profile-update" class="form" action="{{ route('update.profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mt-2 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Face Recognition</span>
                        <div>
                            <button type="button" id="start-camera" class="btn btn-xs bg-highlight rounded-xl shadow-xl text-uppercase font-900 font-11">
                                <i class="fas fa-camera" style="color: white;"></i> Buka Kamera
                            </button>
                            <button type="button" id="take-photo" class="btn btn-xs bg-success rounded-xl shadow-xl text-uppercase font-900 font-11 ml-2" style="display: none;">
                                <i class="fas fa-circle" style="color: white;"></i> Ambil Foto
                            </button>
                            <button type="button" id="reset-face" class="btn btn-xs bg-danger rounded-xl shadow-xl text-uppercase font-900 font-11 ml-2" 
                                style="display: {{ $user->profile && isset($user->profile['face_id']) ? 'inline-block' : 'none' }};">
                                <i class="fas fa-trash" style="color: white;"></i>
                            </button>
                        </div>
                    </div>

                    <div id="camera-container" class="text-center mb-3" style="display: none;">
                        <video id="video-feed" autoplay muted playsinline class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover; background: #000;"></video>
                    </div>

                    <div id="face-processing" class="text-center py-3" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memproses wajah...</p>
                    </div>

                    <div id="face-preview-container" class="text-center mb-3" style="display: none;">
                        <img id="face-preview" class="img-fluid rounded" style="max-height: 200px;" />
                    </div>

                    <div id="face-result" class="mt-2" style="display: none;">
                        <div id="face-success" class="bg-success p-2 rounded text-white text-center mb-2" style="display: none;">
                            <i class="fas fa-check-circle"></i> Face ID terdaftar
                        </div>
                        <div id="face-error" class="bg-danger p-2 rounded text-white text-center mb-2" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i> <span id="error-message">Error</span>
                        </div>
                    </div>

                    <input type="hidden" id="face-descriptor" name="face_id" value="{{ $user->profile['face_descriptor'] ?? '' }}">
                    <input type="hidden" id="face-image-data" name="face_image_data" value="">
                </div>

                <div class="input-style has-borders hnoas-icon input-style-always-active mb-4">
                    <textarea name="address" class="form-control">{{ $user->profile['address'] ?? '' }}</textarea>
                    <label class="color-highlight font-400 font-13">Alamat</label>
                </div>

                <div class="input-style has-borders hnoas-icon input-style-always-active mb-4">
                    <select name="gender" class="form-select">
                        <option value="laki-laki" {{ ($user->profile['gender'] ?? '') == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="perempuan" {{ ($user->profile['gender'] ?? '') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    <label class="color-highlight font-400 font-13">Jenis Kelamin</label>
                </div>
            </form>
        </div>
        <a href="#" onclick="event.preventDefault(); document.getElementById('profile-update').submit();"
            class="btn btn-full btn-margins bg-highlight rounded-sm shadow-xl btn-m text-uppercase font-900">Save Information</a>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    const videoElement = document.getElementById('video-feed');
    const startCameraButton = document.getElementById('start-camera');
    const takePhotoButton = document.getElementById('take-photo');
    const cameraContainer = document.getElementById('camera-container');
    const facePreviewContainer = document.getElementById('face-preview-container');
    const facePreview = document.getElementById('face-preview');
    const faceDescriptorInput = document.getElementById('face-descriptor');
    const faceImageDataInput = document.getElementById('face-image-data');
    const faceProcessing = document.getElementById('face-processing');
    const faceResult = document.getElementById('face-result');
    const faceSuccess = document.getElementById('face-success');
    const faceError = document.getElementById('face-error');
    const errorMessage = document.getElementById('error-message');
    const resetFaceButton = document.getElementById('reset-face');

    let stream = null;
    let isModelLoaded = false;
    const outputSize = 400;

    async function loadModels() {
        if (isModelLoaded) return true;
        try {
            await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
            isModelLoaded = true;
            return true;
        } catch (e) {
            return false;
        }
    }

    async function startCamera() {
        const modelsReady = await loadModels();
        if (!modelsReady) {
            alert("Gagal memuat model pendeteksi.");
            return;
        }

        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === 'videoinput');
            
            let selectedDevice = videoDevices[0];
            for (const device of videoDevices) {
                const deviceName = device.label.toLowerCase();
                if (!deviceName.includes('obs') && !deviceName.includes('virtual') && 
                    !deviceName.includes('capture') && !deviceName.includes('screen')) {
                    selectedDevice = device;
                    break;
                }
            }
            
            const constraints = {
                video: {
                    facingMode: 'user',
                    width: { min: 640, ideal: 1280, max: 1920 },
                    height: { min: 480, ideal: 720, max: 1080 },
                    advanced: [{ zoom: 1.0 }]
                }
            };
            
            if (selectedDevice && selectedDevice.deviceId) {
                constraints.video.deviceId = { exact: selectedDevice.deviceId };
            }
            
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            videoElement.srcObject = stream;

            const videoTrack = stream.getVideoTracks()[0];
            if (videoTrack && typeof videoTrack.getCapabilities === 'function') {
                const capabilities = videoTrack.getCapabilities();
                if (capabilities.zoom) {
                    try {
                        await videoTrack.applyConstraints({ advanced: [{ zoom: 1.0 }] });
                    } catch (e) {
                        console.warn(e);
                    }
                }
            }

            cameraContainer.style.display = 'block';
            takePhotoButton.style.display = 'inline-block';
            startCameraButton.style.display = 'none';
            facePreviewContainer.style.display = 'none';
            faceResult.style.display = 'none';

        } catch (error) {
            console.error(error);
            errorMessage.textContent = 'Gagal mengakses kamera. Periksa izin WebView Anda.';
            faceError.style.display = 'block';
            faceResult.style.display = 'block';
        }
    }

    async function processCapture() {
        faceProcessing.style.display = 'block';
        
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = videoElement.videoWidth;
        tempCanvas.height = videoElement.videoHeight;
        tempCanvas.getContext('2d').drawImage(videoElement, 0, 0);

        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            cameraContainer.style.display = 'none';
            takePhotoButton.style.display = 'none';
            startCameraButton.style.display = 'inline-block';
        }

        try {
            const detections = await faceapi.detectAllFaces(tempCanvas)
                .withFaceLandmarks()
                .withFaceDescriptors();

            const finalCanvas = document.createElement('canvas');
            finalCanvas.width = outputSize;
            finalCanvas.height = outputSize;
            const ctx = finalCanvas.getContext('2d');

            if (detections.length > 0) {
                const box = detections[0].detection.box;
                const faceSize = Math.max(box.width, box.height);
                const cropSize = Math.min(tempCanvas.width, tempCanvas.height, Math.round(faceSize * 1.6));
                const centerX = box.x + box.width / 2;
                const centerY = box.y + box.height / 2;
                let cropX = Math.max(0, Math.min(tempCanvas.width - cropSize, centerX - cropSize / 2));
                let cropY = Math.max(0, Math.min(tempCanvas.height - cropSize, centerY - cropSize / 2));
                
                ctx.drawImage(tempCanvas, cropX, cropY, cropSize, cropSize, 0, 0, outputSize, outputSize);
                faceDescriptorInput.value = JSON.stringify(Array.from(detections[0].descriptor));
                faceSuccess.style.display = 'block';
                faceError.style.display = 'none';
            } else {
                const size = Math.min(tempCanvas.width, tempCanvas.height);
                ctx.drawImage(tempCanvas, (tempCanvas.width-size)/2, (tempCanvas.height-size)/2, size, size, 0, 0, outputSize, outputSize);
                faceError.style.display = 'block';
                faceSuccess.style.display = 'none';
                errorMessage.textContent = "Wajah tidak terdeteksi, pastikan cahaya cukup.";
            }

            const dataUrl = finalCanvas.toDataURL('image/jpeg', 0.9);
            facePreview.src = dataUrl;
            faceImageDataInput.value = dataUrl;
            
            facePreviewContainer.style.display = 'block';
            faceProcessing.style.display = 'none';
            faceResult.style.display = 'block';
            resetFaceButton.style.display = 'inline-block';

        } catch (err) {
            faceProcessing.style.display = 'none';
        }
    }

    startCameraButton.addEventListener('click', startCamera);
    takePhotoButton.addEventListener('click', processCapture);
    resetFaceButton.addEventListener('click', () => {
        faceDescriptorInput.value = '';
        faceImageDataInput.value = '';
        facePreviewContainer.style.display = 'none';
        faceResult.style.display = 'none';
        resetFaceButton.style.display = 'none';
    });
</script>
@endpush