<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AuthPayloadType extends GraphQLType
{
    protected $attributes = [
        'name' => 'AuthPayload',
        'description' => 'The authentication response payload',
    ];

    public function fields(): array
    {
        return [
            'user' => [
                'type' => GraphQL::type('User'),
                'description' => 'The authenticated user',
            ],
            'token' => [
                'type' => Type::string(),
                'description' => 'The authentication token',
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Response message',
            ],
        ];
    }
}
