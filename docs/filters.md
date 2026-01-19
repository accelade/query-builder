# Filters

Query Builder filters allow you to filter Eloquent queries based on request parameters.

## Basic Usage

```php
use Accelade\QueryBuilder\QueryBuilder;
use Accelade\QueryBuilder\Filters\Filter;

$users = QueryBuilder::for(User::class)
    ->allowedFilters([
        Filter::exact('status'),
        Filter::partial('name'),
        Filter::scope('active'),
    ])
    ->get();
```

## Filter Types

### Exact Filter

Matches exact values:

```php
Filter::exact('status')
// ?filter[status]=active
```

### Partial Filter

Matches partial values (LIKE):

```php
Filter::partial('name')
// ?filter[name]=john
```

### Scope Filter

Uses model scopes:

```php
Filter::scope('active')
// ?filter[active]=true
```

### Custom Filter

```php
Filter::custom('price_range', function ($query, $value) {
    [$min, $max] = explode(',', $value);
    return $query->whereBetween('price', [$min, $max]);
})
// ?filter[price_range]=100,500
```
