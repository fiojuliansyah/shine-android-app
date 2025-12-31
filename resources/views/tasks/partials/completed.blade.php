<div class="content mt-3 text-center">
    <h4 class="color-green-dark mb-2">Tugas Selesai</h4>

    <div class="row mt-3">
        <div class="col-6">
            <p class="font-12 opacity-70 mb-1">Foto Awal</p>
            <img src="{{ $taskProgress->image_before_url }}"
                class="img-fluid rounded shadow-sm">
        </div>

        <div class="col-6">
            <p class="font-12 opacity-70 mb-1">Foto Akhir</p>
            <img src="{{ $taskProgress->image_after_url }}"
                class="img-fluid rounded shadow-sm">
        </div>
    </div>

    <div class="divider mt-3 mb-2"></div>

    <div class="font-12 opacity-80 text-start">
        <div class="d-flex justify-content-between">
            <span>Mulai</span>
            <strong>{{ \Carbon\Carbon::parse($taskProgress->start_time)->format('H:i') }}</strong>
        </div>

        <div class="d-flex justify-content-between mt-1">
            <span>Selesai</span>
            <strong>{{ \Carbon\Carbon::parse($taskProgress->end_time)->format('H:i') }}</strong>
        </div>
    </div>

    @if ($taskProgress->progress_description)
        <div class="divider mt-3 mb-2"></div>

        <div class="text-start">
            <p class="font-12 opacity-70 mb-1">Keterangan</p>
            <div class="font-13">
                {{ $taskProgress->progress_description }}
            </div>
        </div>
    @endif
</div>
