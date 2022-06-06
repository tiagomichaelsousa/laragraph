<?php

namespace App\GraphQL\Mutations;

use App\Models\Article;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ArticleMutation
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $request
     */
    public function store($_, array $request)
    {
        $user = request()->user();

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = Arr::has($request, 'thumbnail') ? Storage::putFile("{$user->id}/blog", $request['thumbnail']) : null;

        return $user->articles()->create([
            ...$request, 
            'thumbnail' => $file
        ]);
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
