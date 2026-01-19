# Sorting

Query Builder supports sorting results via request parameters.

## Basic Usage

```php
use Accelade\QueryBuilder\QueryBuilder;

$users = QueryBuilder::for(User::class)
    ->allowedSorts(['name', 'email', 'created_at'])
    ->defaultSort('name')
    ->get();
```

## Request Format

```
// Ascending
?sort=name

// Descending (prefix with -)
?sort=-created_at

// Multiple sorts
?sort=status,-created_at
```

## Custom Sort

```php
use Accelade\QueryBuilder\Sorts\Sort;

QueryBuilder::for(User::class)
    ->allowedSorts([
        Sort::field('name'),
        Sort::custom('popularity', function ($query, $direction) {
            return $query->orderByRaw('views + likes ' . $direction);
        }),
    ]);
```

## Default Sort

```php
QueryBuilder::for(User::class)
    ->defaultSort('-created_at') // Descending by default
    ->allowedSorts(['name', 'created_at']);
```
