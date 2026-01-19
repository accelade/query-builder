<?php

declare(strict_types=1);

namespace Accelade\QueryBuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Accelade\QueryBuilder\QueryBuilder make(\Illuminate\Database\Eloquent\Builder|string|null $query = null)
 * @method static \Accelade\QueryBuilder\QueryBuilder for(string $model)
 * @method static \Accelade\QueryBuilder\QueryBuilder query(\Illuminate\Database\Eloquent\Builder|string $query)
 *
 * @see \Accelade\QueryBuilder\QueryBuilder
 */
class QueryBuilder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'accelade.query-builder';
    }
}
