<?php

return [
    'sidebar' => [],
    'notification' => [
        'enable' => env('NOTIFICATION_ENABLE', true),
        'via' => env('NOTIFICATION_VIA', 'mail,database')
    ],
    'translation' => [],
];