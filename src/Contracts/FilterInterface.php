<?php

declare(strict_types=1);

namespace Accelade\QueryBuilder\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface for filter implementations.
 */
interface FilterInterface
{
    /**
     * Get the filter name/key.
     */
    public function getName(): string;

    /**
     * Apply the filter to the query.
     */
    public function apply(Builder $query, mixed $value): Builder;

    /**
     * Check if the filter is active.
     */
    public function isActive(): bool;

    /**
     * Get the current filter value.
     */
    public function getValue(): mixed;

    /**
     * Set the filter value.
     */
    public function setValue(mixed $value): static;
}
