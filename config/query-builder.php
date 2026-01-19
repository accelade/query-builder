<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Per Page
    |--------------------------------------------------------------------------
    |
    | The default number of items per page when paginating.
    |
    */
    'per_page' => 15,

    /*
    |--------------------------------------------------------------------------
    | Per Page Options
    |--------------------------------------------------------------------------
    |
    | The available per-page options for pagination.
    |
    */
    'per_page_options' => [10, 15, 25, 50, 100],

    /*
    |--------------------------------------------------------------------------
    | Search Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for global search.
    |
    */
    'search' => [
        'input_name' => 'search',
        'min_length' => 1,
        'case_sensitive' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Sort Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for sorting.
    |
    */
    'sort' => [
        'column_param' => 'sort',
        'direction_param' => 'direction',
        'default_direction' => 'asc',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filter Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for filters.
    |
    */
    'filters' => [
        'preserve_empty' => false,
    ],
];
