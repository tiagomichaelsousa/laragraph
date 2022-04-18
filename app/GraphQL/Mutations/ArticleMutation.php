<?php

namespace App\GraphQL\Mutations;

use App\Models\Article;

class ArticleMutation
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $request
     */
    public function store($_, array $request)
    {
        return request()->user()->articles()->create($request);
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $request
     */
    public function update($_, array $request)
    {
        $article =  Article::findOrFail($request['id']);

        $article->update($request);

        return $article;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function destroy($_, array $args)
    {
        $article = Article::findOrFail($args['id']);

        $article->delete();

        return $article;
    }
}
