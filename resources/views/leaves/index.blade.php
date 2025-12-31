@extends('layouts.app')

@section('title','Pengajuan Cuti')
@section('content')
<div class="page-content">
    <div class="content mt-0 mb-0">
        <div class="list-group list-custom-large">
            @foreach ($leaves as $leave)    
                <a href="{{ route('leave.show', $leave->id) }}" target="_blank">
                    <i class="fas fa-file-alt font-20 color-green-dark"></i>
                    <span>{{ $leave->type['name'] }} ( {{ $leave->reason }} )</span>
                    <strong>{{ $leave->start_date->format('d M Y') }} - {{ $leave->end_date->format('d M Y') }}</strong>
                    <i class="fa fa-angle-right"></i>
                </a>
            @endforeach
        </div>
    </div>
    <div class="ad-300x50 ad-300x50-fixed">
        <a href="{{ route('leave.create') }}" target="_blank" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight" target="_blank">
            <i class="fas fa-plus">&nbsp;</i>Buat Pengajuan
        </a>
    </div>
</div>  
@endsection