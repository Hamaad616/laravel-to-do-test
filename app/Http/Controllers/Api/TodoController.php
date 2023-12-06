<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TodoStoreRequest;
use App\Http\Resources\TodoApiResource;
use App\Models\ToDo;
use App\Repositories\TodoRepository;
use App\Transformers\TodoCreationTransformer;
use App\Transformers\TodoListTransformer;
use App\Transformers\TodoNotFoundTransformer;
use App\Transformers\TodoRemovedTransformer;
use App\Transformers\TodoTransformer;
use App\Transformers\TodoUpdatedTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Spatie\Fractal\Fractal;

class TodoController extends Controller
{
    private TodoRepository $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
        $this->middleware('auth:api');
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 10;
        $name = $request->name;

        $todos = $this->todoRepository->all(\Auth::user()->id, $perPage, $name);

        $transformedTodoList = Fractal::create()->item($todos)->transformWith(new TodoListTransformer())->toArray();
        return response()->json($transformedTodoList);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(TodoStoreRequest $request)
    {
        $todo = $this->todoRepository->create(\Auth::user()->id, $request->title, $request->description);

        $transformedTodo = Fractal::create()->item($todo)->transformWith(new TodoCreationTransformer())->toArray();

        return response()->json($transformedTodo);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $todo = $this->todoRepository->find($id);
            $transformedTodo = Fractal::create()->item($todo)->transformWith(new TodoTransformer())->toArray();
            return response()->json($transformedTodo);
        } catch (ModelNotFoundException $exception) {
            $transformedResult = Fractal::create()->item($exception)->transformWith(new TodoNotFoundTransformer())->toArray();
            return response()->json($transformedResult, 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TodoStoreRequest $request, string $id)
    {
        try {
            $todo = $this->todoRepository->update($id, $request->title, $request->description);
            $transformedTodo = Fractal::create()->item($todo)->transformWith(new TodoUpdatedTransformer())->toArray();

            return response()->json($transformedTodo);
        } catch (ModelNotFoundException $exception) {

            $transformedResult = Fractal::create()->item($exception)->transformWith(new TodoNotFoundTransformer())->toArray();
            return response()->json($transformedResult, 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $todo = $this->todoRepository->delete($id);

            $transformedResult = Fractal::create()->item($todo)->transformWith(new TodoRemovedTransformer())->toArray();

            return response()->json($transformedResult);
        } catch (ModelNotFoundException $exception) {
            $transformedResult = Fractal::create()->item($exception)->transformWith(new TodoNotFoundTransformer())->toArray();
            return response()->json($transformedResult, 404);
        }
    }
}
