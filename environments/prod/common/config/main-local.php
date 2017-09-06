<?php
return [
    'components' => [
		'urlManager' => [
            'enablePrettyUrl' => true,
			'showScriptName' => false,
        ],
		'assetManager' => [
			'bundles' => [
				'yii\web\JqueryAsset' => [
					'js'=>[]
				],
			],
		],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=advanta.lims;port=6033',
            'username' => 'root',
            'password' => 'Advanta!QAZ2wsx',
            'charset' => 'utf8',
        ],
         'dbGdbms' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=advanta.gdbms;port=6033',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
