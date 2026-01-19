<?php

declare(strict_types=1);

namespace Accelade\QueryBuilder\Concerns;

use Accelade\QueryBuilder\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for components that can have filters.
 */
trait HasFilters
{
    /**
     * @var array<string, FilterInterface>
     */
    protected array $filters = [];

    /**
     * @var array<string, mixed>
     */
    protected array $filterValues = [];

    /**
     * Add a filter.
     */
    public function filter(FilterInterface $filter): static
    {
        $this->filters[$filter->getName()] = $filter;

        return $this;
    }

    /**
     * Add multiple filters.
     *
     * @param  array<FilterInterface>  $filters
     */
    public function filters(array $filters): static
    {
        foreach ($filters as $filter) {
            $this->filter($filter);
        }

        return $this;
    }

    /**
     * Get all registered filters.
     *
     * @return array<string, FilterInterface>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Get active filters (filters with values set).
     *
     * @return array<string, FilterInterface>
     */
    public function getActiveFilters(): array
    {
        return array_filter($this->filters, fn (FilterInterface $filter) => $filter->isActive());
    }

    /**
     * Set filter values from request or array.
     *
     * @param  array<string, mixed>  $values
     */
    public function setFilterValues(array $values): static
    {
        $this->filterValues = $values;

        foreach ($values as $name => $value) {
            if (isset($this->filters[$name])) {
                $this->filters[$name]->setValue($value);
            }
        }

        return $this;
    }

    /**
     * Get all filter values.
     *
     * @return array<string, mixed>
     */
    public function getFilterValues(): array
    {
        return $this->filterValues;
    }

    /**
     * Apply all active filters to a query.
     */
    protected function applyFilters(Builder $query): Builder
    {
        foreach ($this->getActiveFilters() as $filter) {
            $query = $filter->apply($query, $filter->getValue());
        }

        return $query;
    }

    /**
     * Clear all filter values.
     */
    public function clearFilters(): static
    {
        $this->filterValues = [];

        foreach ($this->filters as $filter) {
            $filter->setValue(null);
        }

        return $this;
    }

    /**
     * Check if any filters are active.
     */
    public function hasActiveFilters(): bool
    {
        return ! empty($this->getActiveFilters());
    }
}
