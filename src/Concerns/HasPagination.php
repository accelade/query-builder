<?php

declare(strict_types=1);

namespace Accelade\QueryBuilder\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for components that support pagination.
 */
trait HasPagination
{
    protected int $perPage = 15;

    protected string $pageName = 'page';

    protected ?int $currentPage = null;

    /**
     * @var array<int>
     */
    protected array $perPageOptions = [10, 15, 25, 50, 100];

    protected bool $paginationEnabled = true;

    /**
     * Set the number of items per page.
     */
    public function perPage(int $perPage): static
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Get the number of items per page.
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Set the pagination page name.
     */
    public function pageName(string $pageName): static
    {
        $this->pageName = $pageName;

        return $this;
    }

    /**
     * Get the pagination page name.
     */
    public function getPageName(): string
    {
        return $this->pageName;
    }

    /**
     * Set the current page.
     */
    public function currentPage(int $page): static
    {
        $this->currentPage = $page;

        return $this;
    }

    /**
     * Get the current page.
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage ?? (int) request()->input($this->pageName, 1);
    }

    /**
     * Set the per-page options.
     *
     * @param  array<int>  $options
     */
    public function perPageOptions(array $options): static
    {
        $this->perPageOptions = $options;

        return $this;
    }

    /**
     * Get the per-page options.
     *
     * @return array<int>
     */
    public function getPerPageOptions(): array
    {
        return $this->perPageOptions;
    }

    /**
     * Enable or disable pagination.
     */
    public function paginate(bool $enabled = true): static
    {
        $this->paginationEnabled = $enabled;

        return $this;
    }

    /**
     * Disable pagination.
     */
    public function withoutPagination(): static
    {
        $this->paginationEnabled = false;

        return $this;
    }

    /**
     * Check if pagination is enabled.
     */
    public function isPaginationEnabled(): bool
    {
        return $this->paginationEnabled;
    }

    /**
     * Apply pagination to the query.
     */
    protected function applyPagination(Builder $query): LengthAwarePaginator
    {
        return $query->paginate(
            perPage: $this->getPerPage(),
            pageName: $this->getPageName(),
            page: $this->getCurrentPage()
        );
    }
}
