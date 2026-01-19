<?php

declare(strict_types=1);

use Accelade\QueryBuilder\Sort;

it('can create a sort instance', function () {
    $sort = Sort::make('name', 'asc');

    expect($sort->getColumn())->toBe('name');
    expect($sort->getDirection())->toBe('asc');
});

it('normalizes direction to lowercase', function () {
    $sort = Sort::make('name', 'DESC');

    expect($sort->getDirection())->toBe('desc');
});

it('defaults invalid direction to asc', function () {
    $sort = Sort::make('name', 'invalid');

    expect($sort->getDirection())->toBe('asc');
});

it('can create ascending sort', function () {
    $sort = Sort::asc('name');

    expect($sort->getDirection())->toBe('asc');
});

it('can create descending sort', function () {
    $sort = Sort::desc('name');

    expect($sort->getDirection())->toBe('desc');
});
