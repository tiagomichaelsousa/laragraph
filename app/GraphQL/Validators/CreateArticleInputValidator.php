<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class CreateArticleInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', Rule::unique('articles', 'slug')],
            'title' => ['required'],
            'body' => ['required'],
        ];
    }
}
