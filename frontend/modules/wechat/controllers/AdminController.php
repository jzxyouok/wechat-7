<?php

namespace modules\wechat\controllers;

use Yii;
use yii\filters\AccessControl;
use modules\wechat\helpers\User;
use modules\wechat\models\Wechat;

/**
 * 微信管理后台控制器基类
 * 后台管理类需继承此类
 *
 * @package components
 */
class AdminController extends BaseController
{
    /**
     * 存储管理微信的session key
     */
    const SESSION_MANAGE_WECHAT_KEY = 'session_manage_wechat';

    /**
     * 默认后台主视图
     * @var string
     */
    public $layout = '@modules/wechat/views/layouts/main';

    /**
     * 开启设置公众号验证
     * @var bool
     */
    public $enableCheckWechat = true;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => array_merge([
                    [
                        'allow' => true,
                        'roles' => ['@'], // 登录才能操作后台
                        'matchCallback' => function() {
                            // 是否设置应用公众号
                            if ($this->enableCheckWechat && !$this->getWechat()) {
                                $this->flash('未设置管理公众号, 请先选则需要管理的公众号', 'error', ['/wechat/index']);
                                exit;
                            }
                            return User::can('manage-wechat');
                        }
                    ]
                ], Yii::$app->getModule('wechat')->adminAccessRule ?: []) // 自定义验证
                // 'denyCallback' => function ($rule, $action) {// 自定义无权限返回信息
                //     throw new \Exception('You are not allowed to access this page');
                // }
            ]
        ];
    }

    /**
     * @var Wechat
     */
    private $_wechat;

    /**
     * 设置当前需要管理的公众号
     * @param Wechat $wechat
     */
    public function setWechat(Wechat $wechat)
    {
        Yii::$app->session->set(self::SESSION_MANAGE_WECHAT_KEY, $wechat->id);
        $this->_wechat = $wechat;
    }

    /**
     * 获取当前管理的公众号
     * @return Wechat|null
     * @throws InvalidConfigException
     */
    public function getWechat()
    {
        if ($this->_wechat === null) {
            $wid = Yii::$app->session->get(self::SESSION_MANAGE_WECHAT_KEY);
            if (!$wid || ($wechat = Wechat::findOne($wid)) === null) {
                return false;
            }
            $this->setWechat($wechat);
        }
        return $this->_wechat;
    }
}
