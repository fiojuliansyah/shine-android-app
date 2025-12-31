<form method="POST" action="{{ route('progress.end') }}">
    @csrf

    <input type="hidden" name="task_planner_id" value="{{ $task->id }}">
    <input type="hidden" name="image_base64" id="imageBase64">

    <div class="content mb-0 mt-3">
        <h4 class="text-center color-theme">Selesaikan Tugas</h4>

        <div class="camera-wrapper text-center mt-3">
            <div class="camera-frame">
                <video id="video" autoplay playsinline></video>
                <img id="preview" class="camera-preview">
            </div>

            <canvas id="canvas" style="display:none;"></canvas>

            <div class="camera-actions">
                <button type="button" onclick="takePhoto()" class="btn btn-action bg-blue-dark">
                    📷 Foto Akhir
                </button>

                <button type="button" onclick="deletePhoto()" id="btnDelete" class="btn btn-action bg-red-dark"
                    style="display:none;">
                    🗑 Hapus Foto
                </button>

                <button type="submit" id="btnSubmit" class="btn btn-action bg-green-dark" disabled>
                    ✔ Selesai
                </button>
            </div>
        </div>
    </div>
</form>

@push('css')
    <style>
        .camera-wrapper {
            max-width: 420px;
            margin: auto
        }

        .camera-frame {
            border-radius: 16px;
            overflow: hidden;
            background: #000
        }

        #video,
        .camera-preview {
            width: 100%;
            border-radius: 16px
        }

        .camera-preview {
            display: none
        }

        .camera-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 16px
        }

        .btn-action {
            width: 100%;
            padding: 14px 0;
            border-radius: 16px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 14px
        }
    </style>
@endpush

@push('js')
    <script>
        const video = document.getElementById('video')
        const canvas = document.getElementById('canvas')
        const preview = document.getElementById('preview')
        const imageBase64 = document.getElementById('imageBase64')
        const btnDelete = document.getElementById('btnDelete')
        const btnSubmit = document.getElementById('btnSubmit')

        navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment"
                },
                audio: false
            })
            .then(stream => video.srcObject = stream)

        function takePhoto() {
            canvas.width = video.videoWidth
            canvas.height = video.videoHeight
            canvas.getContext('2d').drawImage(video, 0, 0)
            const dataURL = canvas.toDataURL('image/jpeg', 0.85)
            preview.src = dataURL
            preview.style.display = 'block'
            video.style.display = 'none'
            imageBase64.value = dataURL
            btnDelete.style.display = 'block'
            btnSubmit.disabled = false
        }

        function deletePhoto() {
            preview.src = ''
            preview.style.display = 'none'
            video.style.display = 'block'
            imageBase64.value = ''
            btnDelete.style.display = 'none'
            btnSubmit.disabled = true
        }
    </script>
@endpush
