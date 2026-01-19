# Accelade Query Builder

[![Tests](https://github.com/accelade/query-builder/actions/workflows/tests.yml/badge.svg)](https://github.com/accelade/query-builder/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/accelade/query-builder.svg?style=flat-square)](https://packagist.org/packages/accelade/query-builder)
[![License](https://img.shields.io/packagist/l/accelade/query-builder.svg?style=flat-square)](https://packagist.org/packages/accelade/query-builder)

A fluent query builder for Laravel applications built with Accelade. Build, filter, sort, and paginate Eloquent queries with a clean, expressive API.

## Features

- **Search** - Global search across multiple columns with relationship support
- **Filters** - Exact, partial, scope, and custom filters
- **Sorting** - Sortable columns with ascending/descending support
- **Pagination** - Standard, simple, and cursor pagination
- **Request Binding** - Automatically apply search, sort, and filters from HTTP request
- **Relationship Support** - Search and filter through related models

## Requirements

- PHP 8.2+
- Laravel 11.0+ or 12.0+
- Accelade ^1.0

## Installation

```bash
composer require accelade/query-builder
```

## Quick Start

```php
use Accelade\QueryBuilder\QueryBuilder;
use App\Models\User;

// Create a query builder from a model
$users = QueryBuilder::for(User::class)
    ->allowedSearch(['name', 'email'])
    ->allowedFilters(['status', 'role'])
    ->allowedSorts(['name', 'created_at'])
    ->defaultSort('-created_at')
    ->paginate();
```

## Documentation

For detailed documentation, see the [docs](docs/) folder:

- [Overview](docs/overview.md) - Getting started and basic concepts
- [Search](docs/search.md) - Global search across columns
- [Filters](docs/filters.md) - Filter types and custom filters
- [Sorting](docs/sorting.md) - Sortable columns and custom sorts
- [Pagination](docs/pagination.md) - Pagination options and configuration

## Usage Examples

### Search

Enable global search across columns:

```php
$users = QueryBuilder::for(User::class)
    ->allowedSearch(['name', 'email', 'bio'])
    ->get();

// Request: ?search=john
```

### Filters

Apply various filter types:

```php
use Accelade\QueryBuilder\Filters\Filter;

$users = QueryBuilder::for(User::class)
    ->allowedFilters([
        Filter::exact('status'),
        Filter::partial('name'),
        Filter::scope('active'),
    ])
    ->get();

// Request: ?filter[status]=active&filter[name]=john
```

### Sorting

Enable column sorting:

```php
$users = QueryBuilder::for(User::class)
    ->allowedSorts(['name', 'email', 'created_at'])
    ->defaultSort('-created_at')
    ->get();

// Request: ?sort=name or ?sort=-created_at (descending)
```

### Pagination

```php
// Standard pagination
$users = QueryBuilder::for(User::class)
    ->allowedPerPage([10, 25, 50, 100])
    ->defaultPerPage(25)
    ->paginate();

// Simple pagination (better performance)
$users = QueryBuilder::for(User::class)->simplePaginate(15);

// Cursor pagination (infinite scroll)
$users = QueryBuilder::for(User::class)->cursorPaginate(15);
```

### Combining Features

```php
$users = QueryBuilder::for(User::class)
    ->allowedSearch(['name', 'email'])
    ->allowedFilters(['status', 'role'])
    ->allowedSorts(['name', 'created_at'])
    ->defaultSort('-created_at')
    ->defaultPerPage(25)
    ->paginate();

// Request: ?search=john&filter[status]=active&sort=-created_at&per_page=50
```

## Using the Facade

```php
use Accelade\QueryBuilder\Facades\QueryBuilder;

$users = QueryBuilder::for(User::class)
    ->allowedSearch(['name', 'email'])
    ->paginate();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security Vulnerabilities

Please review our [security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Fady Mondy](https://github.com/fadymondy)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
