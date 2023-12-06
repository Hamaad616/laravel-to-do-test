<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class TodoCreationTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($todo): array
    {
        return [
            'message' => 'A new to-do added to the list',
            'todo' => [
                'id' => $todo->id,
                'user_id' => $todo->user_id,
                'title' => $todo->title,
                'description' => $todo->description,
                'created_at' => $todo->created_at->toIso8601String(),
                'updated_at' => $todo->updated_at->toIso8601String(),
            ]
        ];
    }
}
