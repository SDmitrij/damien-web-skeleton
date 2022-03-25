<?php

return [
    'config' => [
        'env' => getenv('APP_ENV') ?: 'prod',
        'debug' => (bool)getenv('APP_DEBUG')
    ]
];
