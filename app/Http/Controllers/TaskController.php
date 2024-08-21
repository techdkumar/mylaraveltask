<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // Fetch all tasks
    public function index()
    {
        $tasks = Task::all(); // Fetch all tasks
        return response()->json($tasks);
    }

    // Store a new task
    public function store(Request $request)
    {
       	
		$request->validate([
			'name' => 'required|unique:tasks,name|max:255',
		], [
			'name.required' => 'The task name is required.',
			'name.unique' => 'This task already exists.',
			'name.max' => 'The task name may not be greater than 255 characters.',
		]);


        // Create a new task
        $task = new Task();
        $task->name = $request->name;
        $task->is_completed = false;
        $task->save();

        return response()->json($task);
    }

 	
	
	public function update(Request $request, Task $task)
{
    $request->validate(['is_completed' => 'required|boolean']);
    
    // Update the task's completion status
    $task->is_completed = $request->input('is_completed');
    $task->save();

    return response()->json($task);
}
	

    // Delete a task
    public function destroy($id)
    {
        // Find and delete the task by ID
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}


