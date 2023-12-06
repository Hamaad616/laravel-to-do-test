<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class LoginTransformer extends TransformerAbstract
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
    public function transform($data)
    {
        return [
            'message' => 'Login successful',
            'access_token' => $data['access_token'],
            'token_type' => $data['token_type'],
            'expires_in' => $data['expires_in'],
            'user' => [
                'id' => $data['user']->id,
                'name' => $data['user']->name,
                'email' => $data['user']->email,
                'created_at' => $data['user']->created_at->toIso8601String(),
                'updated_at' => $data['user']->updated_at->toIso8601String(),
            ],
        ];
    }
}
