<?php

use App\Models\Article;
use App\Models\User;
use Laravel\Sanctum\Sanctum;


it('can create an article', function () {
    Sanctum::actingAs(User::factory()->create());
    $article = Article::factory()->make();

    $this->assertDatabaseCount('articles', 0);

    $this->graphQL(
        /** @lang GraphQL */
        '
        mutation ($slug: String!, $title: String!, $body: String!) {
            createArticle(input: { slug: $slug, title: $title, body: $body }) {
                title
                slug
            }
        }',
        [
            'slug' => $article->slug,
            'title' => $article->title,
            'body' => $article->body,
        ]
    )->assertJson([
        'data' => [
            'createArticle' => [
                'title' => $article->title,
                'slug' => $article->slug,
            ],
        ],
    ]);

    $this->assertDatabaseHas('articles', [
        'title' => $article->title,
    ]);
})->only();

it('cannot create an article if not authenticated', function () {
    $article = Article::factory()->make();

    $this->assertDatabaseCount('articles', 0);

    $this->graphQL(
        /** @lang GraphQL */
        '
        mutation ($slug: String!, $title: String!, $body: String!) {
            createArticle(input: { slug: $slug, title: $title, body: $body }) {
                title
                slug
            }
        }',
        [
            'slug' => $article->slug,
            'title' => $article->title,
            'body' => $article->body,
        ]
    )->assertGraphQLErrorMessage('Unauthenticated.');

    $this->assertDatabaseCount('articles', 0);
});

it('can edit an article', function () {
    Sanctum::actingAs($user = User::factory()->create());
    $existentArticle = Article::factory()->for($user)->create();
    $newArticle = Article::factory()->make();

    $this->assertModelExists($existentArticle);

    $this->graphQL(
        /** @lang GraphQL */
        '
        mutation ($id: ID!, $title: String!) {
            updateArticle(input: { id: $id, title: $title }) {
                title
                slug
            }
        }',
        [
            'id' => $existentArticle->id,
            'title' => $newArticle->title,
        ]
    )->assertJson([
        'data' => [
            'updateArticle' => [
                'title' => $newArticle->title,
                'slug' => $existentArticle->slug,
            ],
        ],
    ]);

    $this->assertDatabaseCount('articles', 1);
    $this->assertDatabaseHas('articles', [
        'title' => $newArticle->title,
    ]);
});


it('cannot edit an article that doesnt belong to the user', function () {
    Sanctum::actingAs(User::factory()->create());
    $existentArticle = Article::factory()->create();
    $newArticle = Article::factory()->make();

    $this->assertModelExists($existentArticle);

    $this->graphQL(
        /** @lang GraphQL */
        '
        mutation ($id: ID!, $title: String!) {
            updateArticle(input: { id: $id, title: $title }) {
                title
                slug
            }
        }',
        [
            'id' => $existentArticle->id,
            'title' => $newArticle->title,
        ]
    )->assertGraphQLErrorMessage('This action is unauthorized.');
});

it('can delete an article', function () {
    Sanctum::actingAs($user = User::factory()->create());
    $article = Article::factory()->for($user)->create();

    $this->assertModelExists($article);

    $this->graphQL(
        /** @lang GraphQL */
        '
        mutation ($id: ID!) {
            deleteArticle(id: $id) {
                title
            }
        }
    ',
        ['id' => $article->id]
    )->assertJson([
        'data' => [
            'deleteArticle' => [
                'title' => $article->title,
            ],
        ],
    ]);

    $this->assertModelMissing($article);
});

it('cannot delete an article that doesnt belong to the user', function () {
    Sanctum::actingAs(User::factory()->create());
    $article = Article::factory()->create();

    $this->assertModelExists($article);

    $this->graphQL(
        /** @lang GraphQL */
        '
        mutation ($id: ID!) {
            deleteArticle(id: $id) {
                title
            }
        }
    ',
        ['id' => $article->id]
    )->assertGraphQLErrorMessage('This action is unauthorized.');

    $this->assertModelExists($article);
});
