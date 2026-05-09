<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class SortFilter extends Filter
{
    protected string $filterName = 'sort';

    protected function apply(Builder $query, string $value): void
    {
        if ($value === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }
    }
}
