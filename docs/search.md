# Search

Query Builder provides a search feature that searches across multiple columns.

## Basic Search

```php
use Accelade\QueryBuilder\QueryBuilder;

$users = QueryBuilder::for(User::class)
    ->allowedSearch(['name', 'email', 'bio'])
    ->get();
```

## Request Format

```
?search=john
```

This will search for "john" in name, email, and bio columns.

## Relationship Search

Search in related models:

```php
QueryBuilder::for(Post::class)
    ->allowedSearch([
        'title',
        'content',
        'author.name',
        'tags.name',
    ]);
```

## Search Configuration

```php
QueryBuilder::for(User::class)
    ->allowedSearch(['name', 'email'])
    ->searchParameter('q') // Use ?q= instead of ?search=
    ->get();
```

## Combining with Filters

Search can be combined with filters:

```php
QueryBuilder::for(User::class)
    ->allowedSearch(['name', 'email'])
    ->allowedFilters(['status', 'role'])
    ->get();

// ?search=john&filter[status]=active
```
