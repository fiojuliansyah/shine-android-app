@extends('layouts.app')

@section('title', 'Tugas Hari Ini')

@section('content')
    <div class="content pt-5">
        <form id="formStore" method="POST" action="{{ route('progress.start') }}">
            @csrf

            <input type="hidden" name="task_planner_id" value="{{ $task->id }}">
            <input type="hidden" name="image_base64" id="imageBase64">

            <div class="content mb-0 mt-3">
                <span class="color-highlight font-300 d-block text-uppercase font-10 pt-3">
                    {{ $task->date }}
                </span>

                <strong class="color-theme font-20 d-block mt-n2 mb-n2">
                    {{ $task->name }}
                </strong>

                <span class="font-11 color-theme opacity-30 d-block pb-2 pt-2">
                    <i class="fa fa-map-marker pe-2"></i>{{ $task->floor->name }}
                </span>

                <div class="divider mt-3 mb-3"></div>

                <div class="camera-wrapper text-center">
                    <div class="camera-frame">
                        <video id="video" autoplay playsinline></video>
                        <img id="preview" class="camera-preview">
                    </div>

                    <canvas id="canvas" style="display:none;"></canvas>

                    <div class="camera-actions mt-3">
                        <button type="button" id="btnTake" onclick="takePhoto()"
                            class="btn btn-full btn-m bg-blue-dark font-700 text-uppercase">
                            📷 Ambil Foto
                        </button>

                        <button type="button" id="btnDelete" onclick="deletePhoto()"
                            class="btn btn-full btn-m bg-red-dark font-700 text-uppercase mt-2" style="display:none;">
                            🗑 Hapus Foto
                        </button>

                        <button type="button" id="btnSubmit" onclick="submitForm()"
                            class="btn btn-full btn-m bg-green-dark font-700 text-uppercase mt-2" disabled>
                            ✔ Mulai
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('css')
    <style>
        .camera-wrapper {
            max-width: 420px;
            margin: auto;
        }

        .camera-frame {
            border-radius: 16px;
            overflow: hidden;
            background: #000;
        }

        #video,
        .camera-preview {
            width: 100%;
            border-radius: 16px;
        }

        .camera-preview {
            display: none;
        }

        .camera-actions .btn {
            border-radius: 14px;
        }
    </style>
@endpush
@push('js')
    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const preview = document.getElementById('preview');
        const imageBase64 = document.getElementById('imageBase64');

        const btnDelete = document.getElementById('btnDelete');
        const btnSubmit = document.getElementById('btnSubmit');

        navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment"
                },
                audio: false
            })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(() => {
                alert('Kamera tidak bisa diakses');
            });

        function takePhoto() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            const dataURL = canvas.toDataURL('image/jpeg', 0.85);

            preview.src = dataURL;
            preview.style.display = 'block';
            video.style.display = 'none';

            imageBase64.value = dataURL;

            btnDelete.style.display = 'block';
            btnSubmit.disabled = false;
        }

        function deletePhoto() {
            preview.src = '';
            preview.style.display = 'none';
            video.style.display = 'block';

            imageBase64.value = '';

            btnDelete.style.display = 'none';
            btnSubmit.disabled = true;
        }

        function submitForm() {
            if (!imageBase64.value) {
                alert('Ambil foto terlebih dahulu');
                return;
            }
            document.getElementById('formStore').submit();
        }
    </script>
@endpush
