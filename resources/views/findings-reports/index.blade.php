@extends('layouts.app')

@section('title','Temuan (SOS)')

@section('content')
<div class="page-content pt-5">
    <div class="content mt-0 mb-0">
        <div class="list-group list-custom-large">
            @foreach ($reports as $report)
                <a href="{{ route('findings-reports.show', $report->id) }}" target="_blank">
                    <i class="fas fa-exclamation-circle font-20 
                        @if($report->type == 'low') color-green-dark 
                        @elseif($report->type == 'medium') color-yellow-dark 
                        @else color-red-dark @endif"></i>
                    <span>{{ ucfirst($report->type) }} - {{ ucfirst($report->status) }}</span>
                    <strong>{{ $report->title }}</strong>
                    <i class="fa fa-angle-right"></i>
                </a>
            @endforeach
        </div>
    </div>

    <div class="ad-300x50 ad-300x50-fixed">
        <a href="{{ route('findings-reports.create') }}" target="_blank" class="btn btn-full btn-m rounded-s text-uppercase font-900 shadow-xl bg-highlight">
            <i class="fas fa-plus">&nbsp;</i>Buat Temuan (SOS)
        </a>
    </div>
</div>
@endsection
