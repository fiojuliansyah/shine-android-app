@extends('layouts.app')

@section('title', 'Berita Acara')
@section('content')
<div class="page-content">
        
    <div class="content mt-0 mb-0">
        <div class="list-group list-custom-large">
            @foreach ($minutes as $minute)    
                <a href="{{ route('minute.show', $minute->id) }}" target="_blank">
                    <i class="fas fa-file-alt font-20 color-green-dark"></i>
                    <span>{{ $minute->type }}</span>
                    <strong>{{ $minute->remark }}</strong>
                    <i class="fa fa-angle-right"></i>
                </a>
            @endforeach
        </div>
    </div>
    <div class="ad-300x50 ad-300x50-fixed">
        <a href="{{ route('minute.create') }}" target="_blank" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight">
            <i class="fas fa-plus">&nbsp;</i>Buat Berita Acara
        </a>
    </div>
</div>  
@endsection