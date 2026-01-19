<?php

declare(strict_types=1);

namespace Accelade\QueryBuilder\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Interface for query builders.
 */
interface QueryBuilderInterface
{
    /**
     * Set the base query.
     */
    public function query(Builder $query): static;

    /**
     * Get the base query.
     */
    public function getQuery(): Builder;

    /**
     * Apply all registered filters, sorts, and constraints.
     */
    public function apply(): Builder;

    /**
     * Get paginated results.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all results.
     */
    public function get(): Collection;

    /**
     * Get the total count.
     */
    public function count(): int;
}
