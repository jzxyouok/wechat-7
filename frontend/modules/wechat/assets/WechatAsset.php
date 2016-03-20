<?php

namespace modules\wechat\assets;

use yii\web\AssetBundle;

/**
 * 微信模块Asset
 * @package modules\wechat\assets
 */
class WechatAsset extends AssetBundle
{
    public $sourcePath = '@modules/wechat/web';
    public $css = [
        'css/wechat.css'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'modules\wechat\assets\FontAwesomeAsset',
    ];
}