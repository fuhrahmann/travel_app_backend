<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class LogoutMutation extends Mutation
{
    protected $attributes = [
        'name' => 'logout',
        'description' => 'Logout a user',
    ];

    public function type(): Type
    {
        return GraphQL::type('AuthPayload');
    }

    public function resolve($root, $args, $context)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            throw new \GraphQL\Error\Error('Unauthenticated');
        }

        // Revoke all tokens
        $user->tokens()->delete();

        return [
            'user' => $user,
            'token' => null,
            'message' => 'Logout successful',
        ];
    }
}
