<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class TodoUpdatedTransformer extends TransformerAbstract
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
            'message' => 'To-Do updated',
            'todo' => [
                'title' => $todo['title'],
                'description' => $todo['description']
            ]
        ];
    }
}
