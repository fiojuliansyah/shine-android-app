@extends('layouts.app')

@section('title', 'Detail Tugas')

@section('content')
    <div class="content">
        @if (!$taskProgress)
            @include('tasks.partials.progress-start')
        @elseif ($taskProgress->status === 'in_progress')
            @include('tasks.partials.progress-image')
        @else
            @include('tasks.partials.progress-end')
        @endif
    </div>
@endsection
