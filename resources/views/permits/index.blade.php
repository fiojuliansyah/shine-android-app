@extends('layouts.app')

@section('title', 'Pengajuan Ijin')
@section('content')
<div class="page-content pt-5">
    <div class="content mt-0 mb-0">
        <div class="list-group list-custom-large">
            @foreach ($permits as $permit)    
                <a href="{{ route('permit.show', $permit->id) }}" target="_blank">
                    <i class="fas fa-file-alt font-20 color-green-dark"></i>
                    <span>{{ $permit->title }} ( {{ $permit->reason }} )</span>
                    <strong>{{ $permit->start_date->format('d M Y') }} - {{ $permit->end_date->format('d M Y') }}</strong>
                    <i class="fa fa-angle-right"></i>
                </a>
            @endforeach
        </div>
    </div>
    <div class="ad-300x50 ad-300x50-fixed">
        <a href="{{ route('permit.create') }}" target="_blank" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight">
            <i class="fas fa-plus">&nbsp;</i>Buat Pengajuan
        </a>
    </div>
</div>  
@endsection