<?php

declare(strict_types=1);

namespace Accelade\QueryBuilder;

use Accelade\QueryBuilder\Concerns\HasFilters;
use Accelade\QueryBuilder\Concerns\HasPagination;
use Accelade\QueryBuilder\Concerns\HasSearch;
use Accelade\QueryBuilder\Concerns\HasSorting;
use Accelade\QueryBuilder\Contracts\QueryBuilderInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;

/**
 * Main query builder class.
 */
class QueryBuilder implements QueryBuilderInterface
{
    use Conditionable;
    use HasFilters;
    use HasPagination;
    use HasSearch;
    use HasSorting;

    protected ?Builder $query = null;

    protected bool $queryApplied = false;

    /**
     * Create a new query builder instance.
     *
     * @param  Builder|class-string<Model>|null  $query
     */
    public function __construct(Builder|string|null $query = null)
    {
        if ($query !== null) {
            $this->query($query);
        }
    }

    /**
     * Create a new query builder instance.
     *
     * @param  Builder|class-string<Model>|null  $query
     */
    public static function make(Builder|string|null $query = null): static
    {
        return new static($query);
    }

    /**
     * Create from an Eloquent model class.
     *
     * @param  class-string<Model>  $model
     */
    public static function for(string $model): static
    {
        return new static($model::query());
    }

    /**
     * Set the base query.
     *
     * @param  Builder|class-string<Model>  $query
     */
    public function query(Builder|string $query): static
    {
        if (is_string($query)) {
            $query = $query::query();
        }

        $this->query = $query;
        $this->queryApplied = false;

        return $this;
    }

    /**
     * Get the base query.
     */
    public function getQuery(): Builder
    {
        if ($this->query === null) {
            throw new \RuntimeException('No query has been set. Call query() or use for() first.');
        }

        return $this->query;
    }

    /**
     * Apply a callback to the query.
     */
    public function tap(callable $callback): static
    {
        $callback($this->getQuery());

        return $this;
    }

    /**
     * Apply a scope to the query.
     */
    public function scope(string $scope, mixed ...$arguments): static
    {
        $this->getQuery()->{$scope}(...$arguments);

        return $this;
    }

    /**
     * Add a where clause.
     */
    public function where(string $column, mixed $operator = null, mixed $value = null): static
    {
        $this->getQuery()->where($column, $operator, $value);

        return $this;
    }

    /**
     * Add eager loading.
     *
     * @param  array<string>|string  $relations
     */
    public function with(array|string $relations): static
    {
        $this->getQuery()->with($relations);

        return $this;
    }

    /**
     * Apply request data (search, filters, sort, pagination).
     */
    public function fromRequest(?array $data = null): static
    {
        $data ??= request()->all();

        // Apply search from request
        if (isset($data[$this->getSearchInputName()])) {
            $this->search($data[$this->getSearchInputName()]);
        }

        // Apply sort from request
        if (isset($data['sort'])) {
            $this->sort($data['sort'], $data['direction'] ?? 'asc');
        }

        // Apply per page from request
        if (isset($data['per_page'])) {
            $this->perPage((int) $data['per_page']);
        }

        // Apply filter values
        $this->setFilterValues($data);

        return $this;
    }

    /**
     * Apply all registered filters, sorts, and constraints.
     */
    public function apply(): Builder
    {
        if ($this->queryApplied) {
            return $this->getQuery();
        }

        $query = $this->getQuery();

        // Apply search
        $query = $this->applySearch($query);

        // Apply filters
        $query = $this->applyFilters($query);

        // Apply sorting
        $query = $this->applySorting($query);

        $this->queryApplied = true;

        return $query;
    }

    /**
     * Get paginated results.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        if ($perPage !== 15) {
            $this->perPage($perPage);
        }

        return $this->applyPagination($this->apply());
    }

    /**
     * Get all results.
     */
    public function get(): Collection
    {
        return $this->apply()->get();
    }

    /**
     * Get the first result.
     */
    public function first(): ?Model
    {
        return $this->apply()->first();
    }

    /**
     * Get the total count.
     */
    public function count(): int
    {
        return $this->apply()->count();
    }

    /**
     * Check if any results exist.
     */
    public function exists(): bool
    {
        return $this->apply()->exists();
    }

    /**
     * Pluck a column.
     *
     * @return Collection<int, mixed>
     */
    public function pluck(string $column, ?string $key = null): Collection
    {
        return $this->apply()->pluck($column, $key);
    }

    /**
     * Get the SQL query.
     */
    public function toSql(): string
    {
        return $this->apply()->toSql();
    }

    /**
     * Get the raw SQL with bindings.
     */
    public function toRawSql(): string
    {
        return $this->apply()->toRawSql();
    }

    /**
     * Clone the query builder.
     */
    public function clone(): static
    {
        $clone = new static;
        $clone->query = clone $this->query;
        $clone->filters = $this->filters;
        $clone->filterValues = $this->filterValues;
        $clone->searchColumns = $this->searchColumns;
        $clone->searchTerm = $this->searchTerm;
        $clone->sortableColumns = $this->sortableColumns;
        $clone->defaultSortColumn = $this->defaultSortColumn;
        $clone->defaultSortDirection = $this->defaultSortDirection;
        $clone->currentSortColumn = $this->currentSortColumn;
        $clone->currentSortDirection = $this->currentSortDirection;
        $clone->perPage = $this->perPage;
        $clone->paginationEnabled = $this->paginationEnabled;

        return $clone;
    }

    /**
     * Serialize to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'filters' => array_keys($this->filters),
            'active_filters' => array_keys($this->getActiveFilters()),
            'filter_values' => $this->filterValues,
            'search_term' => $this->searchTerm,
            'search_columns' => $this->searchColumns,
            'sortable_columns' => array_keys($this->sortableColumns),
            'current_sort_column' => $this->getCurrentSortColumn(),
            'current_sort_direction' => $this->getCurrentSortDirection(),
            'per_page' => $this->perPage,
            'pagination_enabled' => $this->paginationEnabled,
        ];
    }
}
