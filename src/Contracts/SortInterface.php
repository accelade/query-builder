<?php

declare(strict_types=1);

namespace Accelade\QueryBuilder\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface for sort implementations.
 */
interface SortInterface
{
    /**
     * Get the sort column name.
     */
    public function getColumn(): string;

    /**
     * Get the sort direction.
     */
    public function getDirection(): string;

    /**
     * Apply the sort to the query.
     */
    public function apply(Builder $query): Builder;
}
