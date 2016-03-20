<?php
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
//自定义命名空间
Yii::setAlias('modules',dirname(dirname(__DIR__)).'/frontend/modules');
Yii::setAlias('components',dirname(dirname(__DIR__)).'/frontend/components/wechat');


