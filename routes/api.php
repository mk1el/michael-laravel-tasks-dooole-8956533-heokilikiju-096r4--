<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;

// Laravel 11 automatically adds 'api/' prefix
Route::prefix('tasks')->group(function () {
    // 1. Create Task: POST /api/tasks
    Route::post('/', [TaskController::class, 'store']); 
    
    // 2. List Tasks: GET /api/tasks
    Route::get('/', [TaskController::class, 'index']); 
    
    // 3. Daily Report: GET /api/tasks/report
    Route::get('/report', [TaskController::class, 'report']);
    
    // 4. Update Status: PATCH /api/tasks/{id}/status
    Route::patch('/{id}/status', [TaskController::class, 'updateStatus']);
    
    // 5. Delete Task: DELETE /api/tasks/{id}
    Route::delete('/{id}', [TaskController::class, 'destroy']);
});