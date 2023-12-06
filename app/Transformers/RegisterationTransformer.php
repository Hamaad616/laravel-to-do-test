<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class RegisterationTransformer extends TransformerAbstract
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
    public function transform($user)
    {
        return [
            'message' => 'User registered successfully. A verification email has been sent to your email address. Please follow the instructions to verify your account.',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'created_at' => $user['created_at']->toIso8601String(),
                'updated_at' => $user['updated_at']->toIso8601String(),
            ],
        ];
    }
}
