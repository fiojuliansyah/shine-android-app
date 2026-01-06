@extends('layouts.master')

@section('title','Jadwal Pekerjaan')

@section('content')
<div class="content" id="tab-group-1">
    <div class="tab-controls tabs-small tabs-rounded" data-highlight="bg-highlight">
        <a href="#" data-active data-bs-toggle="collapse" data-bs-target="#pending">Pending</a>
        <a href="#" data-bs-toggle="collapse" data-bs-target="#in_progress">Berlangsung</a>
        <a href="#" data-bs-toggle="collapse" data-bs-target="#end">Selesaikan</a>
        <a href="#" data-bs-toggle="collapse" data-bs-target="#completed">Selesai</a>
        <a href="#" data-bs-toggle="collapse" data-bs-target="#tomorrow">Besok</a>
    </div>

    <div class="clearfix mb-3"></div>

    <div data-bs-parent="#tab-group-1" class="collapse show" id="pending">
        <div class="content mb-0">
            <h4>Pending</h4>
            <div class="list-group list-custom-large me-2">
                @forelse ($tasksPending as $task)
                    <a href="{{ route('schedule.show', $task->id) }}" target="_blank">
                        <span>{{ $task->name }}</span>
                        <strong>{{ $task->work_type }} - Mulai {{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }}</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>
                @empty
                    <div class="text-center opacity-60 font-13">Tidak ada tugas pending</div>
                @endforelse
            </div>
        </div>
    </div>

    <div data-bs-parent="#tab-group-1" class="collapse" id="in_progress">
        <div class="content mb-0">
            <h4>Berlangsung</h4>
            <div class="list-group list-custom-large me-2">
                @forelse ($taskProgressInProgress as $taskProgress)
                    <a href="{{ route('schedule.show', $taskProgress->task_planner_id) }}" target="_blank">
                        <span>{{ $taskProgress->taskPlanner->name }}</span>
                        <strong>{{ $taskProgress->taskPlanner->work_type }} - Mulai {{ \Carbon\Carbon::parse($taskProgress->start_time)->format('H:i') }}</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>
                @empty
                    <div class="text-center opacity-60 font-13">Tidak ada tugas berlangsung</div>
                @endforelse
            </div>
        </div>
    </div>

    <div data-bs-parent="#tab-group-1" class="collapse" id="end">
        <div class="content mb-0">
            <h4>Akan Diselesaikan</h4>
            <div class="list-group list-custom-large me-2">
                @forelse ($taskProgressInEnd as $taskProgress)
                    <a href="{{ route('schedule.show', $taskProgress->task_planner_id) }}" target="_blank">
                        <span>{{ $taskProgress->taskPlanner->name }}</span>
                        <strong>{{ $taskProgress->taskPlanner->work_type }}</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>
                @empty
                    <div class="text-center opacity-60 font-13">Tidak ada tugas di tahap akhir</div>
                @endforelse
            </div>
        </div>
    </div>

    <div data-bs-parent="#tab-group-1" class="collapse" id="completed">
        <div class="content mb-0">
            <h4>Selesai</h4>
            <div class="list-group list-custom-large me-2">
                @forelse ($taskProgressCompleted as $taskProgress)
                    <a href="{{ route('schedule.show', $taskProgress->task_planner_id) }}" target="_blank">
                        <span>{{ $taskProgress->taskPlanner->name }}</span>
                        <strong>{{ $taskProgress->taskPlanner->work_type }} - Mulai {{ \Carbon\Carbon::parse($taskProgress->start_time)->format('H:i') }}</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>
                @empty
                    <div class="text-center opacity-60 font-13">Belum ada tugas selesai</div>
                @endforelse
            </div>
        </div>
    </div>

    <div data-bs-parent="#tab-group-1" class="collapse" id="tomorrow">
        <div class="content mb-0">
            <h4>Besok</h4>
            <div class="list-group list-custom-large me-2">
                @forelse ($tasksTomorrow as $task)
                    <a href="#">
                        <span>{{ $task->name }}</span>
                        <strong>{{ $task->work_type }} - Mulai {{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }}</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>
                @empty
                    <div class="text-center opacity-60 font-13">Tidak ada jadwal besok</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
