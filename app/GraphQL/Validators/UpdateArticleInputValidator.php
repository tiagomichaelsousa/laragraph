<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateArticleInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'slug' => ['sometimes', Rule::unique('articles', 'slug')->ignore($this->arg('id'), 'id')],
            'title' => ['sometimes'],
            'body' => ['sometimes'],
        ];
    }
}
