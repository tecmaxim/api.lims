<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\ApcCache',
        ],
         'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
         'i18n' => 
       [
           'translations' => 
           [
               'app' => 
               [
                   'class' => 'yii\i18n\PhpMessageSource',
                   'basePath' => '@common/messages',
                               ],
               'menu' => 
               [
                   'class' => 'yii\i18n\PhpMessageSource',
                   'basePath' => '@common/messages',
               ],
                       ],
           ],
 
    ],
];
