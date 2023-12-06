<?php

namespace App\Interfaces;

use App\Http\Requests\TodoStoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface TodoRepositoryInterface
{

    function all(int $userId, int $perPage, string $name=null);

    function create(int $userId, string $title, string $description);

    function find(int $id);

    function update(int $id, string $title, string $description);

    function delete(int $id);

}
