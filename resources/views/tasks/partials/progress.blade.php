@extends('layouts.app')

@section('title', 'Update Progress Tugas')

@section('content')
<div class="content mb-0 mt-3">

    <h4 class="text-center color-theme">Update Progress Tugas</h4>

    <div class="camera-wrapper text-center mt-3">
        <div class="camera-frame">
            <video id="video" autoplay playsinline></video>
            <img id="preview" class="camera-preview">
        </div>

        <canvas id="canvas" style="display:none;"></canvas>

        <div class="camera-actions">
            <button type="button" onclick="takePhoto()" class="btn btn-action bg-blue-dark">
                📷 Ambil Foto
            </button>

            <button type="button" onclick="deletePhoto()" id="btnDelete"
                class="btn btn-action bg-red-dark" style="display:none;">
                🗑 Hapus Foto
            </button>
        </div>
    </div>

    <div class="mt-3">
        <textarea id="description"
            class="form-control rounded-m"
            rows="4"
            placeholder="Update progres pekerjaan..."
            required></textarea>
    </div>

    <form method="POST" action="{{ route('progress.image') }}" class="mt-3">
        @csrf
        <input type="hidden" name="task_planner_id" value="{{ $task->id }}">
        <input type="hidden" name="image_base64" id="imageBase64Progress">
        <input type="hidden" name="description" id="descProgress">

        <button type="submit" id="btnUpdate"
            class="btn btn-full btn-action bg-yellow-dark"
            disabled>
            Update Progress
        </button>
    </form>

    <form method="POST" action="{{ route('progress.end') }}" class="mt-3">
        @csrf
        <input type="hidden" name="task_planner_id" value="{{ $task->id }}">
        <input type="hidden" name="image_base64" id="imageBase64End">
        <input type="hidden" name="description" id="descEnd">

        <button type="submit" id="btnEnd"
            class="btn btn-full btn-action bg-green-dark"
            disabled>
            Selesaikan Pekerjaan
        </button>
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

.camera-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 16px;
}

.btn-action {
    width: 100%;
    padding: 14px 0;
    border-radius: 16px;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 14px;
}
</style>
@endpush

@push('js')
<script>
const video = document.getElementById('video')
const canvas = document.getElementById('canvas')
const preview = document.getElementById('preview')

const imageProgress = document.getElementById('imageBase64Progress')
const imageEnd = document.getElementById('imageBase64End')

const descInput = document.getElementById('description')
const descProgress = document.getElementById('descProgress')
const descEnd = document.getElementById('descEnd')

const btnDelete = document.getElementById('btnDelete')
const btnUpdate = document.getElementById('btnUpdate')
const btnEnd = document.getElementById('btnEnd')

navigator.mediaDevices.getUserMedia({
    video: { facingMode: "environment" },
    audio: false
}).then(stream => video.srcObject = stream)

function takePhoto() {
    canvas.width = video.videoWidth
    canvas.height = video.videoHeight
    canvas.getContext('2d').drawImage(video, 0, 0)
    const dataURL = canvas.toDataURL('image/jpeg', 0.85)

    preview.src = dataURL
    preview.style.display = 'block'
    video.style.display = 'none'

    imageProgress.value = dataURL
    imageEnd.value = dataURL

    btnDelete.style.display = 'block'
    btnUpdate.disabled = false
    btnEnd.disabled = false
}

function deletePhoto() {
    preview.src = ''
    preview.style.display = 'none'
    video.style.display = 'block'

    imageProgress.value = ''
    imageEnd.value = ''

    btnDelete.style.display = 'none'
    btnUpdate.disabled = true
    btnEnd.disabled = true
}

descInput.addEventListener('input', () => {
    descProgress.value = descInput.value
    descEnd.value = descInput.value
})
</script>
@endpush
