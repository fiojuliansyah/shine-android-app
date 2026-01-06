@extends('layouts.app')

@section('title', 'Detail Tugas')

@section('content')
    <div class="content">
        @if (!$taskProgress)
            @include('tasks.partials.start')
        @elseif ($taskProgress->status === 'in_progress')
            @include('tasks.partials.progress')
        @else
            @include('tasks.partials.completed')
        @endif
    </div>
@endsection
