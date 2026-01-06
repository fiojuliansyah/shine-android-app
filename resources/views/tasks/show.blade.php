@extends('layouts.app')

@section('title', 'Detail Tugas')

@section('content')
<div class="content">
    @if (!$taskProgress)
        @include('tasks.partials.start')

    @elseif ($taskProgress->status === 'in_progress' && !request()->has('end'))
        @include('tasks.partials.progress')

    @elseif ($taskProgress->status === 'in_progress' && request()->has('end'))
        @include('tasks.partials.progress-end')

    @elseif ($taskProgress->status === 'completed')
        @include('tasks.partials.completed')
    @endif
</div>
@endsection
