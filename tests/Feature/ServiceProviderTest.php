<?php

declare(strict_types=1);

use Accelade\QueryBuilder\QueryBuilder;

it('registers the config', function () {
    expect(config('query-builder.per_page'))->toBe(15);
});

it('registers the facade', function () {
    expect(app('accelade.query-builder'))->toBeInstanceOf(QueryBuilder::class);
});

it('can create a query builder instance', function () {
    $builder = QueryBuilder::make();

    expect($builder)->toBeInstanceOf(QueryBuilder::class);
});
