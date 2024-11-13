<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response()->json(Task::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $task = Task::firstOrCreate($request->validate([
            'title' => 'required|string',
        ]));
        if ($task->wasRecentlyCreated) {
            return response()->json($task, 201);
        }
        return response()->json(['message' => 'Task already exists', 'task' => $task], 409);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return response()->json(Task::find($id), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validated = $request->validate([
            'is_success' => 'boolean',
            'title' => 'string'
        ]);

        // Check if the title already exists in another row
        $existingTask = Task::where('title', $validated['title'])
            ->where('id', '!=', $id) // Exclude the current task
            ->first();

        // If the title exists, return a validation error or a custom response
        if ($existingTask) {
            return response()->json(['message' => 'Title already exists in another task.'], 409);
        }

        // Update the task
        $task = Task::where('id', $id)
            ->update([
                'is_success' => $validated['is_success'],
                'title' => $validated['title']
            ]);

        // Return the updated task as a response
        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Task::where('id', $id)->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
