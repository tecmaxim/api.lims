<?php
return [
    'components' => [
        'session' => [ 'class' => 'yii\web\Session',
            'cookieParams' => ['httponly' => true, 'lifetime' => 1200],
            'timeout' => 1200,
            'useCookies' => true,
            'name' => 'dow'
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'absoluteAuthTimeoutParam' => 1200,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
    ],
];
