<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;;

it('allows a user to login successfully', function () {
    $user =  User::factory()->create([
        'email' => 'foo@bar.baz',
        'password' => Hash::make('password')
    ]);

    $this->graphQL(
        /** @lang GraphQL */
        '
        mutation ($email: String!, $password: String!) {
            login(input: { email: $email, password: $password }) {
                token
            }
        }',
        [
            'email' => $user->email,
            'password' => 'password'
        ]
    )->assertJsonStructure([
        'data' => [
            'login' => [
                'token',
            ],
        ],
    ]);
});

it('throws an error when credentials are invalid', function () {
    $this->graphQL(
        /** @lang GraphQL */
        '
            mutation {
                login(input: {
                    email: "foo@bar.com",
                    password: "supersecret"
                }) {
                    token
                }
            }
        '
    )
        ->assertGraphQLErrorMessage('The provided credentials are incorrect.');
});

it('throws an error when the email field is missing', function () {
    $this->graphQL(
        /** @lang GraphQL */
        '
        mutation {
            login(input: {
                password: "supersecret"
            }) {
                token
            }
        }
    '
    )->assertGraphQLErrorMessage('Field LoginInput.email of required type String! was not provided.');
});

it('throws an error when the email field is not an email', function () {
    $this->graphQL(
        /** @lang GraphQL */
        '
            mutation {
                login(input: {
                    email: "foobar"
                    password: "supersecret"
                }) {
                    token
                }
            }
        '
    )
        ->assertGraphQLErrorMessage('Validation failed for the field [login].')
        ->assertGraphQLValidationError(
            'input.email',
            'The input.email must be a valid email address.',
        );
});

it('throws an error when the password field is missing', function () {
    $this->graphQL(
        /** @lang GraphQL */
        '
        mutation {
            login(input: {
                email: "foo@bar.baz",
            }) {
                token
            }
        }
    '
    )
        ->assertGraphQLErrorMessage('Field LoginInput.password of required type String! was not provided.');
});

it('throws an error when the password is not a string', function () {
    $this->graphQL(
        /** @lang GraphQL */
        '
            mutation {
                login(input: {
                    email: "foo@bar.baz"
                    password: 12345
                }) {
                    token
                }
            }
        '
    )
        ->assertGraphQLErrorMessage('Field "login" argument "input" requires type String!, found 12345.');
});

it('throws an error when password is wrong', function () {
    $this->graphQL(
        /** @lang GraphQL */
        '
            mutation {
                login(input: {
                    email: "foo@bar.com",
                    password: "supersecret"
                }) {
                    token
                }
            }
        '
    )
        ->assertGraphQLErrorMessage('The provided credentials are incorrect.');
});

it('retrieves logged in user information', function () {
    $user = Sanctum::actingAs(User::factory()->create());

    $this->graphQL(
        /** @lang GraphQL */
        '
        {
            me {
                email
            }
        }
    '
    )->assertJson([
        'data' => [
            'me' => [
                'email' => $user->email,
            ],
        ],
    ]);
});
