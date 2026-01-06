<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\TaskPlanner;
use App\Models\TaskProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ScheduleController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $tasksToday = TaskPlanner::whereDate('date', $today)->get();

        $taskProgressToday = TaskProgress::with('taskPlanner')
            ->whereDate('date', $today)
            ->get();

        $taskProgressGrouped = $taskProgressToday->groupBy('status');

        $taskProgressInProgress = $taskProgressGrouped->get('in_progress', collect());
        $taskProgressInEnd = $taskProgressGrouped->get('end', collect());
        $taskProgressCompleted = $taskProgressGrouped->get('completed', collect());

        $taskProgressTaskIds = $taskProgressToday->pluck('task_planner_id')->unique();

        $tasksPending = $tasksToday->whereNotIn('id', $taskProgressTaskIds);

        $tasksTomorrow = TaskPlanner::whereDate('date', $tomorrow)->get();

        return view('tasks.index', compact(
            'tasksPending',
            'taskProgressInProgress',
            'taskProgressInEnd',
            'taskProgressCompleted',
            'tasksTomorrow'
        ));
    }

    public function show($id)
    {
        $task = TaskPlanner::findOrFail($id);

        $taskProgress = TaskProgress::where('task_planner_id', $task->id)
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        return view('tasks.show', compact('task', 'taskProgress'));
    }

    public function progressStart(Request $request)
    {
        $user = Auth::user();
        $timeNow = Carbon::now()->toTimeString();
        $currentDate = Carbon::now()->toDateString();

        $imgUrl = null;
        $imgPath = null;

        if ($request->filled('image_base64')) {

            $base64 = $request->image_base64;
            $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $base64);

            $imageData = base64_decode($base64);
            $fileName = 'progress_' . time() . '.jpg';

            $storageOption = $request->input('storage_option', 'local');

            if ($storageOption === 's3') {
                Storage::disk('s3')->put("progress/{$fileName}", $imageData);
                $imgPath = "progress/{$fileName}";
                $imgUrl = Storage::disk('s3')->url($imgPath);
            } else {
                Storage::disk('public')->put("progress/{$fileName}", $imageData);
                $imgPath = "progress/{$fileName}";
                $imgUrl = asset("storage/{$imgPath}");
            }
        }

        $taskProgress = new TaskProgress();
        $taskProgress->task_planner_id = $request->task_planner_id;
        $taskProgress->user_id = $user->id;
        $taskProgress->site_id = $user->site->id;
        $taskProgress->status = 'in_progress';
        $taskProgress->is_worked = 'not_worked';
        $taskProgress->date = $currentDate;
        $taskProgress->start_time = $timeNow;
        $taskProgress->image_before_url = $imgUrl;
        $taskProgress->image_before_path = $imgPath;
        $taskProgress->save();

        return redirect()->back()
            ->with('success', 'Task recorded successfully.');
    }

    public function updateProgressImage(Request $request)
    {
        $user = Auth::user();

        $taskProgress = TaskProgress::where('task_planner_id', $request->task_planner_id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        if (!$taskProgress) {
            return redirect()->back()->with('error', 'Progress tidak ditemukan');
        }

        $imgUrl = $taskProgress->image_progress_url;
        $imgPath = $taskProgress->image_progress_path;

        if ($request->filled('image_base64')) {

            if ($imgPath) {
                Storage::disk(
                    str_contains($imgPath, 's3') ? 's3' : 'public'
                )->delete($imgPath);
            }

            $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $request->image_base64);
            $imageData = base64_decode($base64);
            $fileName = 'progress_update_' . time() . '.jpg';

            $storageOption = $request->input('storage_option', 'local');

            if ($storageOption === 's3') {
                Storage::disk('s3')->put("progress/{$fileName}", $imageData);
                $imgPath = "progress/{$fileName}";
                $imgUrl = Storage::disk('s3')->url($imgPath);
            } else {
                Storage::disk('public')->put("progress/{$fileName}", $imageData);
                $imgPath = "progress/{$fileName}";
                $imgUrl = asset("storage/{$imgPath}");
            }
        }

        $taskProgress->image_progress_url = $imgUrl;
        $taskProgress->image_progress_path = $imgPath;
        $taskProgress->status = 'end';
        $taskProgress->progress_description = $request->description;
        $taskProgress->save();

        return redirect()->back()->with('success', 'Progress berhasil diperbarui');
    }

    public function progressEnd(Request $request)
    {
        $user = Auth::user();
        $timeNow = Carbon::now()->toTimeString();

        $taskProgress = TaskProgress::where('task_planner_id', $request->task_planner_id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        if (!$taskProgress) {
            return redirect()->back()->with('error', 'Progress tidak ditemukan');
        }

        $imgUrl = null;
        $imgPath = null;

        if ($request->filled('image_base64')) {

            $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $request->image_base64);
            $imageData = base64_decode($base64);
            $fileName = 'progress_after_' . time() . '.jpg';

            $storageOption = $request->input('storage_option', 'local');

            if ($storageOption === 's3') {
                Storage::disk('s3')->put("progress/{$fileName}", $imageData);
                $imgPath = "progress/{$fileName}";
                $imgUrl = Storage::disk('s3')->url($imgPath);
            } else {
                Storage::disk('public')->put("progress/{$fileName}", $imageData);
                $imgPath = "progress/{$fileName}";
                $imgUrl = asset("storage/{$imgPath}");
            }
        }

        $taskProgress->end_time = $timeNow;
        $taskProgress->image_after_url = $imgUrl;
        $taskProgress->image_after_path = $imgPath;
        $taskProgress->status = 'completed';
        $taskProgress->is_worked = 'worked';
        $taskProgress->progress_description = $request->description;
        $taskProgress->save();

        return redirect()->back()
            ->with('success', 'Task berhasil diselesaikan');
    }

}