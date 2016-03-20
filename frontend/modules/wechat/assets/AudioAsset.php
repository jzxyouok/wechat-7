<?php

namespace modules\wechat\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * 音乐播放模块Asset
 * @package modules\wechat\assets
 */
class AudioAsset extends AssetBundle
{
    public $sourcePath = '@modules/wechat/web';
    public $css = [
    ];

    public $js = [
        'js/audiojs/audio.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    public $jsOptions = [
        'position'=>View::POS_HEAD
    ];
}