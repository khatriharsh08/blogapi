<?php

declare(strict_types=1);

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    protected string $filterName;

    public function handle(Builder $query, Closure $next): Builder
    {
        if (! request()->has($this->filterName) || empty(request()->input($this->filterName))) {
            return $next($query);
        }

        $this->apply($query, request()->input($this->filterName));

        return $next($query);
    }

    abstract protected function apply(Builder $query, string $value): void;
}
