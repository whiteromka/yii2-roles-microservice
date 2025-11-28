<?php

use app\repositories\RbacRepository;
use app\services\RbacService;
use yii\symfonymailer\Mailer;

$params = require __DIR__ . '/params.php';
$paramsLocal = __DIR__ . '/params_local.php';
if (file_exists($paramsLocal)) {
    $params = require $paramsLocal;
}

$db = require __DIR__ . '/db.php';
$dbLocal = __DIR__ . '/db_local.php';
if (file_exists($dbLocal)) {
    $db = require $dbLocal;
}

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'container' => [
        'singletons' => [
            RbacService::class => RbacService::class,
            RbacRepository::class => RbacRepository::class
        ],
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => '1KqyLnSdptTsmKKPwSRNkmoK3ZYvsjmr',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => 'rbac-redis',
                'port' => $params['redis']['port'],
                'database' => 0,
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // APIs
                'GET api/rbac/user-permissions/<userId:\d+>' => 'api/rbac/user-permissions',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '172.18.0.*', '172.*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '172.18.0.*', '172.*'],
    ];
}

return $config;
