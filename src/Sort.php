<?php

declare(strict_types=1);

namespace Accelade\QueryBuilder;

use Accelade\QueryBuilder\Contracts\SortInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Sort implementation for query builder.
 */
class Sort implements SortInterface
{
    protected string $column;

    protected string $direction;

    /**
     * Create a new sort instance.
     */
    public function __construct(string $column, string $direction = 'asc')
    {
        $this->column = $column;
        $this->direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
    }

    /**
     * Create a new sort instance.
     */
    public static function make(string $column, string $direction = 'asc'): static
    {
        return new static($column, $direction);
    }

    /**
     * Get the sort column.
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * Get the sort direction.
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * Apply the sort to the query.
     */
    public function apply(Builder $query): Builder
    {
        if (str_contains($this->column, '.')) {
            return $this->applyRelationSort($query);
        }

        return $query->orderBy($this->column, $this->direction);
    }

    /**
     * Apply sort on a relationship column.
     */
    protected function applyRelationSort(Builder $query): Builder
    {
        [$relation, $column] = explode('.', $this->column, 2);

        $relatedTable = $query->getModel()->{$relation}()->getRelated()->getTable();
        $parentTable = $query->getModel()->getTable();

        return $query
            ->join($relatedTable, "{$parentTable}.{$relation}_id", '=', "{$relatedTable}.id")
            ->orderBy("{$relatedTable}.{$column}", $this->direction)
            ->select("{$parentTable}.*");
    }

    /**
     * Create ascending sort.
     */
    public static function asc(string $column): static
    {
        return new static($column, 'asc');
    }

    /**
     * Create descending sort.
     */
    public static function desc(string $column): static
    {
        return new static($column, 'desc');
    }
}
