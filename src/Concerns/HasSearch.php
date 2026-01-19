<?php

declare(strict_types=1);

namespace Accelade\QueryBuilder\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for components that support global search.
 */
trait HasSearch
{
    /**
     * @var array<string>
     */
    protected array $searchColumns = [];

    protected ?string $searchTerm = null;

    protected string $searchInputName = 'search';

    protected int $searchMinLength = 1;

    protected bool $searchCaseSensitive = false;

    /**
     * Set the columns to search.
     *
     * @param  array<string>  $columns
     */
    public function searchable(array $columns): static
    {
        $this->searchColumns = $columns;

        return $this;
    }

    /**
     * Get the searchable columns.
     *
     * @return array<string>
     */
    public function getSearchColumns(): array
    {
        return $this->searchColumns;
    }

    /**
     * Set the search term.
     */
    public function search(?string $term): static
    {
        $this->searchTerm = $term;

        return $this;
    }

    /**
     * Get the search term.
     */
    public function getSearchTerm(): ?string
    {
        return $this->searchTerm;
    }

    /**
     * Set the search input name.
     */
    public function searchInputName(string $name): static
    {
        $this->searchInputName = $name;

        return $this;
    }

    /**
     * Get the search input name.
     */
    public function getSearchInputName(): string
    {
        return $this->searchInputName;
    }

    /**
     * Set the minimum search term length.
     */
    public function searchMinLength(int $length): static
    {
        $this->searchMinLength = $length;

        return $this;
    }

    /**
     * Get the minimum search term length.
     */
    public function getSearchMinLength(): int
    {
        return $this->searchMinLength;
    }

    /**
     * Set case-sensitive search.
     */
    public function searchCaseSensitive(bool $caseSensitive = true): static
    {
        $this->searchCaseSensitive = $caseSensitive;

        return $this;
    }

    /**
     * Check if search is case-sensitive.
     */
    public function isSearchCaseSensitive(): bool
    {
        return $this->searchCaseSensitive;
    }

    /**
     * Check if there's an active search.
     */
    public function hasSearch(): bool
    {
        $term = $this->getSearchTerm();

        return $term !== null && strlen($term) >= $this->searchMinLength;
    }

    /**
     * Apply search to a query.
     */
    protected function applySearch(Builder $query): Builder
    {
        if (! $this->hasSearch() || empty($this->searchColumns)) {
            return $query;
        }

        $term = $this->getSearchTerm();
        $columns = $this->searchColumns;

        return $query->where(function (Builder $query) use ($term, $columns) {
            foreach ($columns as $index => $column) {
                $method = $index === 0 ? 'where' : 'orWhere';

                if (str_contains($column, '.')) {
                    // Relationship column (e.g., 'user.name')
                    [$relation, $relationColumn] = explode('.', $column, 2);
                    $query->{$method.'Has'}($relation, function (Builder $query) use ($relationColumn, $term) {
                        $this->applySearchToColumn($query, $relationColumn, $term, 'where');
                    });
                } else {
                    $this->applySearchToColumn($query, $column, $term, $method);
                }
            }
        });
    }

    /**
     * Apply search to a single column.
     */
    protected function applySearchToColumn(Builder $query, string $column, string $term, string $method): Builder
    {
        if ($this->searchCaseSensitive) {
            return $query->{$method}($column, 'LIKE', "%{$term}%");
        }

        return $query->{$method.'Raw'}("LOWER({$column}) LIKE ?", ['%'.strtolower($term).'%']);
    }
}
