<?php

declare(strict_types=1);

use Accelade\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('test_users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->string('status')->default('active');
        $table->timestamps();
    });

    // Create some test records
    TestUser::create(['name' => 'John Doe', 'email' => 'john@example.com', 'status' => 'active']);
    TestUser::create(['name' => 'Jane Smith', 'email' => 'jane@example.com', 'status' => 'active']);
    TestUser::create(['name' => 'Bob Wilson', 'email' => 'bob@example.com', 'status' => 'inactive']);
});

afterEach(function () {
    Schema::dropIfExists('test_users');
});

// Basic creation tests
it('can create from model class string', function () {
    $builder = QueryBuilder::for(TestUser::class);

    expect($builder)->toBeInstanceOf(QueryBuilder::class);
    expect($builder->get())->toHaveCount(3);
});

it('can create from eloquent builder', function () {
    $query = TestUser::query()->where('status', 'active');
    $builder = QueryBuilder::make($query);

    expect($builder)->toBeInstanceOf(QueryBuilder::class);
    expect($builder->get())->toHaveCount(2);
});

it('can create empty instance with make', function () {
    $builder = QueryBuilder::make();

    expect($builder)->toBeInstanceOf(QueryBuilder::class);
});

it('can set query after creation', function () {
    $builder = QueryBuilder::make();
    $builder->query(TestUser::class);

    expect($builder->get())->toHaveCount(3);
});

// Search tests
it('can search across columns', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->searchable(['name', 'email'])
        ->search('john');

    expect($builder->get())->toHaveCount(1);
    expect($builder->get()->first()->name)->toBe('John Doe');
});

it('can search case insensitively', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->searchable(['name'])
        ->search('JOHN');

    expect($builder->get())->toHaveCount(1);
});

it('returns all when no search term', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->searchable(['name', 'email']);

    expect($builder->get())->toHaveCount(3);
});

it('returns searchable columns', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->searchable(['name', 'email']);

    expect($builder->getSearchColumns())->toBe(['name', 'email']);
});

it('can get and set search term', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->search('test');

    expect($builder->getSearchTerm())->toBe('test');
});

it('can check if search is active', function () {
    $builder = QueryBuilder::for(TestUser::class);

    expect($builder->hasSearch())->toBeFalse();

    $builder->search('test');

    expect($builder->hasSearch())->toBeTrue();
});

it('can configure search input name', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->searchInputName('q');

    expect($builder->getSearchInputName())->toBe('q');
});

it('can configure search minimum length', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->searchMinLength(3);

    expect($builder->getSearchMinLength())->toBe(3);
});

// Sorting tests
it('can define sortable columns', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->sortable(['name', 'email', 'created_at']);

    expect($builder->getSortableColumns())->toHaveKeys(['name', 'email', 'created_at']);
});

it('can check if column is sortable', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->sortable(['name', 'email']);

    expect($builder->isSortable('name'))->toBeTrue();
    expect($builder->isSortable('status'))->toBeFalse();
});

it('can set default sort', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->sortable(['name'])
        ->defaultSort('name', 'desc');

    expect($builder->getCurrentSortColumn())->toBe('name');
    expect($builder->getCurrentSortDirection())->toBe('desc');
});

it('can sort ascending', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->sortable(['name'])
        ->sort('name', 'asc');

    $results = $builder->get();
    expect($results->first()->name)->toBe('Bob Wilson');
});

it('can sort descending', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->sortable(['name'])
        ->sort('name', 'desc');

    $results = $builder->get();
    expect($results->first()->name)->toBe('John Doe');
});

it('ignores sort for non-sortable column', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->sortable(['name'])
        ->sort('email', 'asc');

    expect($builder->getCurrentSortColumn())->toBeNull();
});

it('can check if column is sorted', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->sortable(['name'])
        ->sort('name', 'asc');

    expect($builder->isSorted('name'))->toBeTrue();
    expect($builder->isSortedAsc('name'))->toBeTrue();
    expect($builder->isSortedDesc('name'))->toBeFalse();
});

// Pagination tests
it('can set per page', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->perPage(25);

    expect($builder->getPerPage())->toBe(25);
});

it('can paginate results', function () {
    $builder = QueryBuilder::for(TestUser::class);
    $results = $builder->paginate(2);

    expect($results)->toHaveCount(2);
    expect($results->total())->toBe(3);
});

it('can set per page options', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->perPageOptions([10, 20, 50]);

    expect($builder->getPerPageOptions())->toBe([10, 20, 50]);
});

it('can set page name', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->pageName('p');

    expect($builder->getPageName())->toBe('p');
});

it('can disable pagination', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->withoutPagination();

    expect($builder->isPaginationEnabled())->toBeFalse();
});

// Query modification tests
it('can add where clauses', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->where('status', 'active');

    expect($builder->get())->toHaveCount(2);
});

it('can eager load relations', function () {
    // This test just ensures the method exists and is chainable
    $builder = QueryBuilder::for(TestUser::class)
        ->with('posts');

    expect($builder)->toBeInstanceOf(QueryBuilder::class);
});

it('can tap into query', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->tap(fn ($query) => $query->where('status', 'active'));

    expect($builder->get())->toHaveCount(2);
});

// Result methods
it('can get all results', function () {
    $builder = QueryBuilder::for(TestUser::class);

    expect($builder->get())->toHaveCount(3);
});

it('can get first result', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->sortable(['name'])
        ->sort('name', 'asc');

    $result = $builder->first();
    expect($result->name)->toBe('Bob Wilson');
});

it('can count results', function () {
    $builder = QueryBuilder::for(TestUser::class);

    expect($builder->count())->toBe(3);
});

it('can check if results exist', function () {
    $builder = QueryBuilder::for(TestUser::class);

    expect($builder->exists())->toBeTrue();
});

it('can pluck column', function () {
    $builder = QueryBuilder::for(TestUser::class);
    $names = $builder->pluck('name');

    expect($names)->toHaveCount(3);
    expect($names->toArray())->toContain('John Doe');
});

it('can get SQL', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->where('status', 'active');

    $sql = $builder->toSql();

    expect($sql)->toContain('test_users');
    expect($sql)->toContain('status');
});

// Clone and array conversion
it('can clone builder', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->searchable(['name'])
        ->sortable(['name'])
        ->perPage(25);

    $clone = $builder->clone();

    expect($clone)->not->toBe($builder);
    expect($clone->getSearchColumns())->toBe(['name']);
    expect($clone->getPerPage())->toBe(25);
});

it('can convert to array', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->searchable(['name', 'email'])
        ->sortable(['name'])
        ->search('john')
        ->perPage(25);

    $array = $builder->toArray();

    expect($array)->toHaveKeys(['search_term', 'search_columns', 'sortable_columns', 'per_page']);
    expect($array['search_term'])->toBe('john');
    expect($array['per_page'])->toBe(25);
});

// Conditionable
it('supports conditionable when', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->when(true, fn ($b) => $b->where('status', 'active'));

    expect($builder->get())->toHaveCount(2);
});

it('supports conditionable unless', function () {
    $builder = QueryBuilder::for(TestUser::class)
        ->unless(false, fn ($b) => $b->where('status', 'active'));

    expect($builder->get())->toHaveCount(2);
});

// Exception handling
it('throws when no query set', function () {
    $builder = QueryBuilder::make();

    $builder->getQuery();
})->throws(RuntimeException::class);

/**
 * Test model for the query builder tests.
 */
class TestUser extends Model
{
    protected $table = 'test_users';

    protected $guarded = [];
}
