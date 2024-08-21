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
        // Validate the task name
       /*  $request->validate([
            'name' => 'required|unique:tasks,name|max:255',
        ]); */
		
		
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

    // Update task completion status
   /*  public function update(Request $request, $id)
    {
        // Find the task by ID
        $task = Task::findOrFail($id);
        
        // Update the completion status
        $task->is_completed = $request->is_completed;
        $task->save();

        return response()->json(['message' => 'Task updated successfully']);
    } */
	
	
	
	
	public function update(Request $request, Task $task)
{
    // Validate that is_completed is either true or false
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


