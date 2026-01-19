@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => false])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="query-builder-overview" :documentation="$documentation" :hasDemo="$hasDemo">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            Query Builder is a backend package that provides a fluent API for building, filtering, sorting, and paginating Eloquent queries.
        </p>
    </div>
</x-accelade::layouts.docs>
