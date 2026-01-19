@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => false])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="query-builder-filters" :documentation="$documentation" :hasDemo="$hasDemo">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            Apply filters to your queries using the HasFilters trait.
        </p>
    </div>
</x-accelade::layouts.docs>
