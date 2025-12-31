@extends('layouts.app')

@section('title','Esign')
@section('content')
<div class="page-content">
    <div class="content mb-0">
        <h5 class="font-16 font-500">Silahkan masukan tanda tangan dibawah ini.</h5>

        <form id="esign-update" action="{{ route('update.esign') }}" method="POST">
            @csrf

            <div class="signature-wrapper">
                <canvas id="signatureCanvas"></canvas>
            </div>

            <input type="hidden" name="esign" id="esignData">
        </form>

        <div class="signature-actions mt-3">
            <a href="#" onclick="saveSignature()"
                class="btn btn-full bg-highlight rounded-sm shadow-xl btn-m text-uppercase font-900">
                Save Signature
            </a>

            <a href="#" onclick="clearSignature()"
                class="btn btn-full bg-red-dark rounded-sm btn-m mt-2">
                Clear
            </a>
        </div>
    </div>

    <div class="content mt-5">
        <h5 class="font-16 font-500">Tanda tangan digital anda</h5>
        <div style="width:300px">
            {!! $user->profile->esign_svg !!}
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .signature-wrapper {
        width: 100%;
        height: 200px;
        border: 1px solid #eef2f1;
        border-radius: 8px;
        background: #fff;
    }

    #signatureCanvas {
        width: 100%;
        height: 100%;
        touch-action: none;
    }
</style>
@endpush


@push('js')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('signatureCanvas')

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1)
        canvas.width = canvas.offsetWidth * ratio
        canvas.height = canvas.offsetHeight * ratio
        canvas.getContext('2d').scale(ratio, ratio)
    }

    resizeCanvas()
    window.addEventListener('resize', resizeCanvas)

    const signaturePad = new SignaturePad(canvas, {
        minWidth: 1,
        maxWidth: 2.5,
        penColor: 'black'
    })

    window.saveSignature = function () {
        if (signaturePad.isEmpty()) {
            alert('Silahkan isi tanda tangan terlebih dahulu')
            return
        }

        document.getElementById('esignData').value = signaturePad.toSVG()
        document.getElementById('esign-update').submit()
    }

    window.clearSignature = function () {
        signaturePad.clear()
    }
})
</script>
@endpush

