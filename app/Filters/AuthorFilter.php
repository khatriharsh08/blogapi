<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class AuthorFilter extends Filter
{
    protected string $filterName = 'author_id';

    protected function apply(Builder $query, string $value): void
    {
        $query->where('user_id', $value);
    }
}
