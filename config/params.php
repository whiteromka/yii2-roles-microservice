<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',

    'redis' => [
        'port' => 6379,
    ],

    // Апи токен для проверки клиента. (Должен передаваться в заголовке 'X-Api-Token')
    'xApiToken' => '?',
];
