# Pagination

Query Builder integrates with Laravel's pagination system.

## Basic Pagination

```php
use Accelade\QueryBuilder\QueryBuilder;

$users = QueryBuilder::for(User::class)
    ->allowedFilters(['status'])
    ->paginate(15);
```

## Request Parameters

```
// Page number
?page=2

// Per page (if allowed)
?per_page=25
```

## Configuring Per Page

```php
QueryBuilder::for(User::class)
    ->allowedPerPage([10, 25, 50, 100])
    ->defaultPerPage(25)
    ->paginate();
```

## Simple Pagination

For better performance with large datasets:

```php
QueryBuilder::for(User::class)
    ->simplePaginate(15);
```

## Cursor Pagination

For infinite scroll or API endpoints:

```php
QueryBuilder::for(User::class)
    ->cursorPaginate(15);
```
