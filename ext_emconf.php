<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Typo3 Vite',
    'description' => 'Vite for Typo3',
    'category' => 'fe',
    'author' => 'crazy252',
    'author_email' => 'crazy252.cg@gmail.com',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
