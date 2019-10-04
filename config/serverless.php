<?php

return [
    'image'         => env('KUBELESS_DOCKER_IMAGE', 'vognev/knative-php'),
    'auth'          => env('SERVERLESS_KNATIVE_REGISTRY_AUTH'),
    'namespace'     => env('KUBELESS_NAMESPACE', 'default'),
    'storage'       => storage_path('serverless'),
    'environment'   => value(function() {
        return [
            'APP_ENV'               => 'production',
            'APP_KEY'               => md5('time'),
            'APP_DEBUG'             => 'true',
            'LOG_CHANNEL'           => 'stderr',
            'QUEUE_CONNECTION'      => 'sqs',
            'AWS_ACCESS_KEY_ID'     => env('AWS_ACCESS_KEY_ID'),
            'AWS_SECRET_ACCESS_KEY' => env('AWS_SECRET_ACCESS_KEY'),
            'SQS_PREFIX'            => env('SQS_PREFIX'),
            'SQS_QUEUE'             => env('SQS_QUEUE'),
            'AWS_DEFAULT_REGION'    => env('AWS_DEFAULT_REGION')
        ];
    }),
    'queue'         => [
        'enabled'   => true,
        'url'       => env('KUBELESS_SQS_QUEUE')
    ],
    'artisan'       => [
        'minScale'  => 0,
        'maxScale'  => 2,
        'requests'  => [
            'memory'    => '128Mi',
            'cpu'       => '100m',
        ],
        'limits'    => [
            'memory'    => '512Mi',
            'cpu'       => '500m',
        ]
    ],
    'website'       => [
        'minScale'  => 0,
        'maxScale'  => 4,
        'requests'  => [
            'memory'    => '128Mi',
            'cpu'       => '100m',
        ],
        'limits'    => [
            'memory'    => '512Mi',
            'cpu'       => '500m',
        ]
    ],
    'php' => [
        'modules' => ['default', 'pcntl'],
        'presets' => [
            'default' => [
                'curl',
                'dom',
                'fileinfo',
                'filter',
                'ftp',
                'hash',
                'iconv',
                'intl',
                'json',
                'mbstring',
                'openssl',
                'opcache',
                'pdo_mysql',
                'readline',
                'session',
                'simplexml',
                'sockets',
                'tokenizer',
                'zip',
            ]
        ]
    ]
];
