<?php
return [
    'components' => [
        'session' => [ 'class' => 'yii\web\Session',
            'cookieParams' => ['httponly' => true, 'lifetime' => 3600],
            'timeout' => 3600,
            'useCookies' => true,
            'name' => 'dow'
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'absoluteAuthTimeoutParam' => 3600,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
    ],
];
