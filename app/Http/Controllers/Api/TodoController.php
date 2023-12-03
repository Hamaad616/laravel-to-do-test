<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TodoApiResource;
use App\Models\ToDo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 10;
        $name = $request->name;

        if(!empty($name)){
            $todos = ToDo::where('user_id', \Auth::user()->id)->where('title', 'LIKE', '%'.$name.'%')->paginate($perPage);
        }else{
            $todos = ToDo::where('user_id', \Auth::user()->id)->paginate($perPage);
        }

        return new TodoApiResource($todos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required'
        ]);

        $todo = ToDo::create([
            'user_id' => \Auth::user()->id,
            'title' => $request->title,
            'description' => $request->description
        ]);

        return new TodoApiResource($todo);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $todo = \Auth::user()->todos->where('id', $id)->first();

        if(!$todo){
            return response()->json([
                'error' => 'Todo not found in the list'
            ], 404);
        }
        return new TodoApiResource($todo->only(['title', 'description']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required'
        ]);

        $todo = ToDo::find($id);

        if(!$todo){
            return response()->json([
                'error' => 'Todo not found in the list'
            ], 404);
        }

        $todo->title = $request->title;
        $todo->description = $request->description;
        $todo->update();

        return new TodoApiResource($todo->only(['title', 'description']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $todo = ToDo::find($id);

        if(!$todo){
            return response()->json([
                'error' => 'Todo not found in the list'
            ], 404);
        }

        $todo->delete();

        return response()->json([
            'message' => 'Item removed successfully'
        ]);
    }
}
