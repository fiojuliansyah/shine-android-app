@extends('layouts.app')

@section('title','Tugas Hari Ini')

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

            {{-- CAMERA --}}
            <div class="text-center">
                <video id="video" autoplay playsinline style="width:100%; border-radius:12px;"></video>
                <canvas id="canvas" style="display:none;"></canvas>

                <img id="preview" style="display:none; width:100%; border-radius:12px; margin-top:10px;">
            </div>

            <div class="row mt-3">
                <div class="col-6">
                    <button type="button" onclick="takePhoto()"
                        class="btn btn-full btn-icon rounded-sm btn-m bg-blue-dark text-uppercase font-700">
                        📷 Ambil Foto
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" onclick="submitForm()"
                        class="btn btn-full btn-icon rounded-sm btn-m bg-green-dark text-uppercase font-700">
                        ✔ Mulai
                    </button>
                </div>
            </div>

            <div class="mt-2">
                <a href="{{ route('schedule') }}"
                    class="btn btn-full btn-icon rounded-sm btn-m bg-red-dark text-uppercase font-700">
                    ✖ Batal
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
let video = document.getElementById('video');
let canvas = document.getElementById('canvas');
let preview = document.getElementById('preview');
let imageBase64 = document.getElementById('imageBase64');

navigator.mediaDevices.getUserMedia({
    video: { facingMode: "environment" },
    audio: false
})
.then(stream => {
    video.srcObject = stream;
})
.catch(err => {
    alert('Kamera tidak bisa diakses');
});

function takePhoto() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);

    const dataURL = canvas.toDataURL('image/jpeg', 0.8);
    preview.src = dataURL;
    preview.style.display = 'block';

    imageBase64.value = dataURL;
}

function submitForm() {
    if (!imageBase64.value) {
        alert('Ambil foto terlebih dahulu');
        return;
    }
    document.getElementById('formStore').submit();
}
</script>
@endsection
