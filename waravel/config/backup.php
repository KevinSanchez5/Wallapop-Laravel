<?php

return [

    'backup' => [

        'database' => 'pgsql',

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
            'compression_method' => '1',
            'compression_level' => 5,
            'filename_prefix' => 'backup-',
        ],

        'notifications' => [
            'enabled' => true,
            'slack_webhook_url' => null,
        ],
    ],
];
