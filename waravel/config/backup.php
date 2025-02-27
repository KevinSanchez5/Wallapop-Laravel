<?php

return [

    'backup' => [

        'database' => 'pgsql',

        'compression_method' => 'gzip',
        'compression_level' => 5,
        'filename_prefix' => 'backup-',
        'name' => 'backup-name',

        'source' => [
            'files' => [
                'include' => [
                    base_path(),
                ],
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],
            ],

            'databases' => [
                'pgsql'
            ],
        ],

        'destination' => [
            'disks' => [
                'local',
            ],
        ],

        'notifications' => [
            'enabled' => true,
            'slack_webhook_url' => null,
        ],
    ],
];
