<?php

$config = [
    'components' => [
        'session' => [ 'class' => 'yii\web\Session',
            'cookieParams' => ['httponly' => true, 'lifetime' => 8200],
            'timeout' => 8200,
            'useCookies' => true,
            'name' => 'advanta'
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'absoluteAuthTimeoutParam' => 8200,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
