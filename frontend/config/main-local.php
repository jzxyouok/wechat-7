<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'LryfIH4yD_jeOWAdVxeV_zGwPA2YYD9y',
        ],
        'authManager' => [
            //'class' => 'yii\rbac\PhpManager',
            'class' => 'yii\rbac\DbManager',
        ],
    ],
    'modules' => [
        'wechat' => [
            'class' => 'modules\wechat\Module',
            'adminId' => 1, // 这里填写管理员ID(拥有wechat最高管理权限), 默认为第一个用户
        ],
        'redactor' => [
            'class' => 'yii\redactor\RedactorModule',
            'imageAllowExtensions'=>['jpg','png','gif']
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
