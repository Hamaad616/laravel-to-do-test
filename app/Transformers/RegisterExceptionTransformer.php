<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class RegisterExceptionTransformer extends TransformerAbstract
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
    public function transform($exception)
    {
        return [
            'error' => [
                'message' => $exception->getMessage(),
                'status_code' => $exception->getCode(),
            ],
        ];
    }
}
