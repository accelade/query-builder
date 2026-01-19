<?php

declare(strict_types=1);

namespace Accelade\QueryBuilder\Concerns;

use Accelade\QueryBuilder\Sort;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for components that can be sorted.
 */
trait HasSorting
{
    /**
     * @var array<string, bool>
     */
    protected array $sortableColumns = [];

    protected ?string $defaultSortColumn = null;

    protected string $defaultSortDirection = 'asc';

    protected ?string $currentSortColumn = null;

    protected ?string $currentSortDirection = null;

    /**
     * Define sortable columns.
     *
     * @param  array<string>|array<string, bool>  $columns
     */
    public function sortable(array $columns): static
    {
        foreach ($columns as $key => $value) {
            if (is_int($key)) {
                $this->sortableColumns[$value] = true;
            } else {
                $this->sortableColumns[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Set the default sort column and direction.
     */
    public function defaultSort(string $column, string $direction = 'asc'): static
    {
        $this->defaultSortColumn = $column;
        $this->defaultSortDirection = $direction;

        return $this;
    }

    /**
     * Set the current sort column and direction.
     */
    public function sort(?string $column, ?string $direction = 'asc'): static
    {
        if ($column && $this->isSortable($column)) {
            $this->currentSortColumn = $column;
            $this->currentSortDirection = $direction ?? 'asc';
        }

        return $this;
    }

    /**
     * Check if a column is sortable.
     */
    public function isSortable(string $column): bool
    {
        return $this->sortableColumns[$column] ?? false;
    }

    /**
     * Get the sortable columns.
     *
     * @return array<string, bool>
     */
    public function getSortableColumns(): array
    {
        return $this->sortableColumns;
    }

    /**
     * Get the current sort column.
     */
    public function getCurrentSortColumn(): ?string
    {
        return $this->currentSortColumn ?? $this->defaultSortColumn;
    }

    /**
     * Get the current sort direction.
     */
    public function getCurrentSortDirection(): string
    {
        return $this->currentSortDirection ?? $this->defaultSortDirection;
    }

    /**
     * Apply sorting to a query.
     */
    protected function applySorting(Builder $query): Builder
    {
        $column = $this->getCurrentSortColumn();
        $direction = $this->getCurrentSortDirection();

        if ($column && $this->isSortable($column)) {
            $sort = Sort::make($column, $direction);

            return $sort->apply($query);
        }

        return $query;
    }

    /**
     * Get the sort URL for a column.
     */
    public function getSortUrl(string $column): string
    {
        $currentColumn = $this->getCurrentSortColumn();
        $currentDirection = $this->getCurrentSortDirection();

        $newDirection = ($column === $currentColumn && $currentDirection === 'asc') ? 'desc' : 'asc';

        return request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $newDirection,
        ]);
    }

    /**
     * Check if the given column is currently sorted.
     */
    public function isSorted(string $column): bool
    {
        return $this->getCurrentSortColumn() === $column;
    }

    /**
     * Check if the given column is sorted ascending.
     */
    public function isSortedAsc(string $column): bool
    {
        return $this->isSorted($column) && $this->getCurrentSortDirection() === 'asc';
    }

    /**
     * Check if the given column is sorted descending.
     */
    public function isSortedDesc(string $column): bool
    {
        return $this->isSorted($column) && $this->getCurrentSortDirection() === 'desc';
    }
}
