<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // 1. Create Task (POST /api/tasks)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => 'required|in:low,medium,high',
        ]);

        // Duplicate Check
        $exists = Task::where('title', $request->title)
            ->where('due_date', $request->due_date)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Task already exists for this date'], 422);
        }

        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    // 2. List Tasks (GET /api/tasks)
    public function index(Request $request)
    {
        $query = Task::query();
        
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $tasks = $query
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date', 'asc')
            ->get();

        return $tasks->isEmpty() 
            ? response()->json(['message' => 'No tasks found'], 200) 
            : response()->json($tasks);
    }

    // 3. Update Status (PATCH /api/tasks/{id}/status)
    public function updateStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,in_progress,done']);

        $validTransitions = [
            'pending' => 'in_progress',
            'in_progress' => 'done'
        ];

        if (!isset($validTransitions[$task->status]) || $validTransitions[$task->status] !== $request->status) {
            return response()->json(['error' => 'Invalid status transition'], 422);
        }

        $task->status = $request->status;
        $task->save();
        return response()->json($task);
    }

    // 4. Delete Task (DELETE /api/tasks/{id})
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        
        if ($task->status !== 'done') {
            return response()->json(['error' => 'Only done tasks can be deleted'], 403);
        }
        
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }

    // 5. Daily Report (GET /api/tasks/report)
    public function report(Request $request)
    {
        $date = $request->query('date');
        if (!$date) return response()->json(['error' => 'Date required'], 422);

        $tasks = Task::whereDate('due_date', $date)->get();
        $summary = [];
        
        foreach (['high', 'medium', 'low'] as $p) {
            foreach (['pending', 'in_progress', 'done'] as $s) {
                $summary[$p][$s] = $tasks->where('priority', $p)->where('status', $s)->count();
            }
        }
        return response()->json(['date' => $date, 'summary' => $summary]);
    }
}