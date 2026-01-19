# Query Builder

The Query Builder package provides a fluent API for building, filtering, sorting, and paginating Eloquent queries.

## Installation

The query-builder package is included with Accelade Tables and Grids. You can also install it separately:

```bash
composer require accelade/query-builder
```

## Basic Usage

```php
use Accelade\QueryBuilder\QueryBuilder;
use App\Models\User;

// Create from a model
$builder = QueryBuilder::for(User::class);

// Or from an existing query
$builder = QueryBuilder::make(User::query()->where('active', true));

// Configure and execute
$users = $builder
    ->searchable(['name', 'email'])
    ->sortable(['name', 'email', 'created_at'])
    ->defaultSort('created_at', 'desc')
    ->fromRequest()
    ->paginate();
```

## Features

- **Search**: Global search across multiple columns
- **Filters**: Apply custom filters to queries
- **Sorting**: Sortable columns with URL generation
- **Pagination**: Built-in pagination support
- **Request Binding**: Automatically apply search, sort, and filter from request

## Creating a Query Builder

```php
// From a model class
$builder = QueryBuilder::for(User::class);

// From a query builder
$builder = QueryBuilder::make(User::where('active', true));

// Using the facade
$builder = QueryBuilder::for(User::class);
```

## Search

Enable global search across columns:

```php
$builder->searchable(['name', 'email', 'bio']);

// Set the search term
$builder->search('john');

// Or automatically from request
$builder->fromRequest(); // Uses 'search' parameter by default
```

## Sorting

Define sortable columns:

```php
$builder
    ->sortable(['name', 'email', 'created_at'])
    ->defaultSort('created_at', 'desc')
    ->sort('name', 'asc');
```

## Pagination

```php
// Paginate with default per page
$results = $builder->paginate();

// Custom per page
$results = $builder->perPage(25)->paginate();

// Get all without pagination
$results = $builder->get();
```

## Request Integration

The query builder can automatically read search, sort, and filter values from the request:

```php
$builder
    ->searchable(['name', 'email'])
    ->sortable(['name', 'created_at'])
    ->fromRequest()
    ->paginate();
```

Expected request parameters:
- `search` - Search term
- `sort` - Sort column
- `direction` - Sort direction (asc/desc)
- `per_page` - Items per page
- Any filter names defined
