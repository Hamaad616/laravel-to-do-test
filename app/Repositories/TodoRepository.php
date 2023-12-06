<?php

namespace App\Repositories;

use App\Interfaces\TodoRepositoryInterface;
use App\Models\ToDo;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TodoRepository implements TodoRepositoryInterface
{

    /**
     * @param int $userId
     * @param int $perPage
     * @param string|null $name
     * @return mixed
     */
    function all(int $userId, int $perPage, string $name = null)
    {
        $query = ToDo::where('user_id', $userId);

        if(!empty($name)){
            $query->where('title', 'LIKE', '%'.$name.'%');
        }

        return $query->paginate($perPage);
    }

    /**
     * @param int $userId
     * @param string $title
     * @param string $description
     * @return mixed
     */
    function create(int $userId, string $title, string $description)
    {
        return ToDo::create([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description
        ]);
    }

    /**
     * @param int $id
     * @return mixed
     */
    function find(int $id)
    {
        $todo = ToDo::find($id);

        if($todo){
            return $todo->only(['title', 'description']);
        }else{
            throw new ModelNotFoundException("To-Do not found");
        }
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $description
     * @return mixed
     */
    function update(int $id, string $title, string $description)
    {
        $todo = ToDo::find($id);

        if($todo){
            $todo->title = $title;
            $todo->description = $description;
            $todo->update();
        }else{
            throw new ModelNotFoundException("To-Do not found");
        }

        return $todo->only(['title', 'description']);
    }

    /**
     * @param int $id
     * @return mixed
     */
    function delete(int $id)
    {
        $todo = ToDo::find($id);

        if($todo){
            $todo->delete();
        }else{
            throw new ModelNotFoundException("To-Do not found");
        }

        return $todo;
    }
}
