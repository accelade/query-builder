# Query Builder Package

This package provides the core query building engine for Accelade tables and grids.

## Package Overview

- **Namespace**: `Accelade\QueryBuilder`
- **Service Provider**: `QueryBuilderServiceProvider`
- **Facade**: `QueryBuilder`

## Key Components

### QueryBuilder Class
The main class for building, filtering, sorting, and paginating Eloquent queries.

```php
use Accelade\QueryBuilder\QueryBuilder;

$builder = QueryBuilder::for(User::class)
    ->searchable(['name', 'email'])
    ->sortable(['name', 'created_at'])
    ->defaultSort('created_at', 'desc')
    ->fromRequest()
    ->paginate();
```

### Traits
- `HasFilters` - Filter management
- `HasSorting` - Sortable columns
- `HasPagination` - Pagination support
- `HasSearch` - Global search

### Contracts
- `QueryBuilderInterface` - Main interface
- `FilterInterface` - Filter contract
- `SortInterface` - Sort contract

## Testing
```bash
cd packages/query-builder
composer test
```

## Integration
This package is used by `accelade/tables` and `accelade/grids` for data querying.
