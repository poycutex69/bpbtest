<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function index()
    {        
        $user = auth()->user();  
        $data = Task::where('user_id',$user->id)->get();
        return Inertia::render('Tasks', ['data' => $data]);
    }
  
    /**
     * Create a task
     *
     * @return Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'title' => ['required'],
            'description' => ['required'],
        ])->validate();
  
        

        $user = auth()->user();        

        $task = new Task();
        $task->title        = $request->title;
        $task->description  = $request->description;
        $task->user_id      = $user->id;  
        $task->save();       
  
        return redirect()->back()
                    ->with('message', 'Task was created successfully.');
    }
  
    /**
     * update a task
     *
     * @return Response
     */
    public function update(Request $request)
    {
        Validator::make($request->all(), [
            'title' => ['required'],
            'description' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            $task = Task::find($request->input('id'));
            
            $task->title        = $request->title;
            $task->description  = $request->description;
            $task->save();

            return redirect()->back()
                    ->with('message', 'Task was updated successfully.');
        }
    }
  
    /**
     * Delete a task
     *
     * @return Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Task::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }

    /**
     * Update task status
     * 
     * @return Response
     */
    public function updatestatus(Request $request){
        if ($request->has('id')) {
            $task = Task::find($request->input('id'));
            
            $task->status = $request->status;
            $task->task_done = $request->status == 1 ? Carbon::now() : null;
            $task->save();

            return redirect()->back()
                    ->with('message', 'Task status was updated successfully.');
        }
    }

    public function chartdata(Request $request){
        $user = auth()->user();  
        $data = Task::where('user_id',$user->id)->get();
        return response()->json($data);
    }
}
