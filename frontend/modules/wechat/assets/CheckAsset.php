<?php

namespace modules\wechat\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @package modules\wechat\assets
 */
class CheckAsset extends AssetBundle
{
    public $sourcePath = '@modules/wechat/web';
    public $css = [
    ];

    public $js = [
        'js/check.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    public $jsOptions = [
        'position'=>View::POS_HEAD
    ];
}