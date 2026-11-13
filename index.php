<?php

require_once __DIR__ . '/src/tmdb.php';
require_once __DIR__ . '/src/render.php';

Kirby::plugin('mirthe/tvshowblock', [
    'options' => [
        'apiKey' => option('themoviedb.apiKey'),
        'cache' => true
    ],
    'tags' => [
        'tvshowblock' => [
            'attr' => [
                'tmdb'
            ],
            'html' => function($tag) {
                return mirthe_tvshowblock_render((string) $tag->tmdb);
            }
        ]
    ]
]);