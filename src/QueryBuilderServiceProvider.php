<?php

declare(strict_types=1);

namespace Accelade\QueryBuilder;

use Accelade\Docs\DocsRegistry;
use Illuminate\Support\ServiceProvider;

class QueryBuilderServiceProvider extends ServiceProvider
{
    /**
     * Documentation sections configuration.
     *
     * @var array<int, array<string, mixed>>
     */
    private const DOCUMENTATION_SECTIONS = [
        // Main entry - no subgroup
        ['id' => 'query-builder-overview', 'label' => 'Overview', 'icon' => '🔍', 'markdown' => 'overview.md', 'description' => 'Query builder for filtering, sorting, and paginating data', 'keywords' => ['query', 'builder', 'filter', 'sort', 'paginate'], 'view' => 'query-builder::docs.sections.overview'],
        // Operations
        ['id' => 'query-builder-filters', 'label' => 'Filters', 'icon' => '🎯', 'markdown' => 'filters.md', 'description' => 'Apply filters to queries', 'keywords' => ['filter', 'where', 'condition', 'search'], 'view' => 'query-builder::docs.sections.filters', 'subgroup' => 'operations'],
        ['id' => 'query-builder-sorting', 'label' => 'Sorting', 'icon' => '↕️', 'markdown' => 'sorting.md', 'description' => 'Sort query results', 'keywords' => ['sort', 'order', 'asc', 'desc'], 'view' => 'query-builder::docs.sections.sorting', 'subgroup' => 'operations'],
        ['id' => 'query-builder-search', 'label' => 'Search', 'icon' => '🔎', 'markdown' => 'search.md', 'description' => 'Global search across columns', 'keywords' => ['search', 'global', 'text', 'find'], 'view' => 'query-builder::docs.sections.search', 'subgroup' => 'operations'],
        // Results
        ['id' => 'query-builder-pagination', 'label' => 'Pagination', 'icon' => '📄', 'markdown' => 'pagination.md', 'description' => 'Paginate query results', 'keywords' => ['paginate', 'page', 'per page', 'limit'], 'view' => 'query-builder::docs.sections.pagination', 'subgroup' => 'results'],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/query-builder.php',
            'query-builder'
        );

        $this->app->singleton('accelade.query-builder', function () {
            return new QueryBuilder;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'query-builder');

        // Register documentation sections
        $this->registerDocumentation();

        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__.'/../config/query-builder.php' => config_path('query-builder.php'),
            ], 'query-builder-config');
        }
    }

    /**
     * Register documentation sections with Accelade's DocsRegistry.
     */
    protected function registerDocumentation(): void
    {
        if (! $this->app->bound('accelade.docs')) {
            return;
        }

        /** @var DocsRegistry $registry */
        $registry = $this->app->make('accelade.docs');

        $registry->registerPackage('query-builder', __DIR__.'/../docs');
        $registry->registerGroup('query-builder', 'Query Builder', '🔍', 45);

        // Register sub-groups within Query Builder
        $registry->registerSubgroup('query-builder', 'operations', '⚙️ Operations', '', 10);
        $registry->registerSubgroup('query-builder', 'results', '📄 Results', '', 20);

        foreach ($this->getDocumentationSections() as $section) {
            $this->registerSection($registry, $section);
        }
    }

    /**
     * Register a single documentation section.
     *
     * @param  array<string, mixed>  $section
     */
    protected function registerSection(DocsRegistry $registry, array $section): void
    {
        $builder = $registry->section($section['id'])
            ->label($section['label'])
            ->icon($section['icon'])
            ->markdown($section['markdown'])
            ->description($section['description'])
            ->keywords($section['keywords'])
            ->view($section['view'])
            ->package('query-builder')
            ->inGroup('query-builder');

        if (isset($section['subgroup'])) {
            $builder->inSubgroup($section['subgroup']);
        }

        $builder->register();
    }

    /**
     * Get documentation section definitions.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getDocumentationSections(): array
    {
        return self::DOCUMENTATION_SECTIONS;
    }
}
