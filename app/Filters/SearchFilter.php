<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class SearchFilter extends Filter
{
    protected string $filterName = 'search';

    protected function apply(Builder $query, string $value): void
    {
        $query->where(function ($q) use ($value) {
            $q->where('title', 'like', '%'.$value.'%')
                ->orWhere('content', 'like', '%'.$value.'%');
        });
    }
}
