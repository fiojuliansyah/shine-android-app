<div class="content mt-3 text-center">
    <h4 class="color-green-dark">Tugas Selesai</h4>

    <div class="row mt-3">
        <div class="col-6">
            <p class="font-12">Foto Awal</p>
            <img src="{{ $taskProgress->image_before_url }}" class="img-fluid rounded">
        </div>
        <div class="col-6">
            <p class="font-12">Foto Akhir</p>
            <img src="{{ $taskProgress->image_after_url }}" class="img-fluid rounded">
        </div>
    </div>

    <div class="mt-3 font-12">
        Mulai: {{ $taskProgress->start_time }} <br>
        Selesai: {{ $taskProgress->end_time }}
    </div>
</div>
