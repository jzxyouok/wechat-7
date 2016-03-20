<?php
namespace components\mp;

use components\WechatComponent;

/**
 * 多客服组件
 * @package components\mp
 */
class CustomService extends WechatComponent
{
    /**
     * 添加客服帐号
     */
    const WECHAT_ACCOUNT_ADD_PREFIX = '/customservice/kfaccount/add';
    /**
     * 添加客服帐号
     * @param array $account
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function addAccount(array $account)
    {
        $result = $this->wechat->httpRaw(self::WECHAT_ACCOUNT_ADD_PREFIX, $account, [
            'access_token' => $this->wechat->getAccessToken()
        ]);
        return isset($result['errmsg']) && $result['errmsg'] == 'ok';
    }

    /**
     * 修改客服帐号
     */
    const WECHAT_ACCOUNT_UPDATE_PREFIX = '/customservice/kfaccount/update';
    /**
     * 修改客服帐号
     * @param array $account
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function updateAccount(array $account)
    {
        $result = $this->wechat->httpRaw(self::WECHAT_ACCOUNT_UPDATE_PREFIX, $account, [
            'access_token' => $this->wechat->getAccessToken()
        ]);
        return isset($result['errmsg']) && $result['errmsg'] == 'ok';
    }

    /**
     * 删除客服帐号
     */
    const WECHAT_ACCOUNT_DELETE_PREFIX = '/customservice/kfaccount/del';
    /**
     * 删除客服帐号
     * @param array $account
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function deleteAccount($account)
    {
        $result = $this->wechat->httpGet(self::WECHAT_ACCOUNT_DELETE_PREFIX, [
            'access_token' => $this->wechat->getAccessToken(),'kf_account'=>$account
        ]);
        return isset($result['errmsg']) && $result['errmsg'] == 'ok';
    }

    /**
     * 设置客服账号头像
     */
    const WECHAT_ACCOUNT_AVATAR_SET_PREFIX = '/customservice/kfaccount/uploadheadimg';
    /**
     * 设置客服账号头像
     * @param string $accountName
     * @param string $avatarPath
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function setAccountAvatar($accountName, $avatarPath)
    {
        $result = $this->wechat->httpPost(self::WECHAT_ACCOUNT_AVATAR_SET_PREFIX, [
            'media' => $this->wechat->uploadFile($avatarPath)
        ], [
            'access_token' => $this->wechat->getAccessToken(),
            'kf_account' => $accountName
        ]);
        return isset($result['errmsg']) && $result['errmsg'] == 'ok';
    }

    /**
     * 获取所有客服账号
     */
    const WECHAT_ACCOUNT_LIST_GET_PREFIX = '/cgi-bin/customservice/getkflist';
    /**
     * 获取所有客服账号
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function getAccountList()
    {
        $result = $this->wechat->httpGet(self::WECHAT_ACCOUNT_LIST_GET_PREFIX, [
            'access_token' => $this->wechat->getAccessToken()
        ]);
        return isset($result['kf_list']) ? $result['kf_list'] : false;
    }

    /**
     * 获取在线客服接待信息
     */
    const WECHAT_ACCOUNT_LIST_GET_ONLINE_PREFIX = '/cgi-bin/customservice/getonlinekflist';
    /**
     * 获取在线客服接待信息
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function getAccountOnlinekfList()
    {
        $result = $this->wechat->httpGet(self::WECHAT_ACCOUNT_LIST_GET_ONLINE_PREFIX, [
            'access_token' => $this->wechat->getAccessToken()
        ]);
        return isset($result['kf_online_list']) ? $result['kf_online_list'] : false;
    }

    /**
     * 获取客服聊天记录
     */
    const WECHAT_MESSAGE_RECORD_GET_PREFIX = '/customservice/msgrecord/getrecord';
    /**
     * 获取客服聊天记录
     * @param array $data
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function getMessageRecord(array $data)
    {
        $result = $this->wechat->httpRaw(self::WECHAT_MESSAGE_RECORD_GET_PREFIX, $data, [
            'access_token' => $this->wechat->getAccessToken()
        ]);
        return isset($result['errcode']) && !$result['errcode'] ? $result['recordlist'] : false;
    }

    /**
     * 创建会话
     */
    const WECHAT_ACCOUNT_KFSESSION_CREATE_PREFIX = '/customservice/kfsession/create';
    /**
     * 创建会话
     * @param array $data
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function createKfsession(array $data)
    {
        $result = $this->wechat->httpRaw(self::WECHAT_ACCOUNT_KFSESSION_CREATE_PREFIX, $data, [
            'access_token' => $this->wechat->getAccessToken()
        ]);
        return isset($result['errmsg']) && $result['errmsg'] == 'ok';
    }

    /**
     * 关闭会话
     */
    const WECHAT_ACCOUNT_KFSESSION_CLOSE_PREFIX = '/customservice/kfsession/close';
    /**
     * 关闭会话
     * @param array $data
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function closeKfsession(array $data)
    {
        $result = $this->wechat->httpRaw(self::WECHAT_ACCOUNT_KFSESSION_CLOSE_PREFIX, $data, [
            'access_token' => $this->wechat->getAccessToken()
        ]);
        return isset($result['errmsg']) && $result['errmsg'] == 'ok';
    }

    /**
     * 获取客户的会话状态
     */
    const WECHAT_ACCOUNT_KFSESSION_GET_PREFIX = '/customservice/kfsession/getsession';
    /**
     * 获取客户的会话状态
     * @param string $openid
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function getKfsession($openid)
    {
        $result = $this->wechat->httpGet(self::WECHAT_ACCOUNT_KFSESSION_GET_PREFIX, [
            'access_token' => $this->wechat->getAccessToken(),'openid'=>$openid
        ]);
        return isset($result['errmsg']) && $result['errmsg'] == 'ok';
    }

    /**
     * 获取客服的会话列表
     */
    const WECHAT_ACCOUNT_KFSESSION_GETLIST_PREFIX = '/customservice/kfsession/getsessionlist';
    /**
     * 获取客服的会话列表
     * @param string $kf_account
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function getListKfsession($kf_account)
    {
        $result = $this->wechat->httpGet(self::WECHAT_ACCOUNT_KFSESSION_GETLIST_PREFIX, [
            'access_token' => $this->wechat->getAccessToken(),'kf_account'=>$kf_account
        ]);
        return isset($result['errmsg']) && $result['errmsg'] == 'ok';
    }

    /**
     * 获取未接入会话列表
     */
    const WECHAT_ACCOUNT_KFSESSION_GETWAIT_PREFIX = '/customservice/kfsession/getwaitcase';
    /**
     * 获取未接入会话列表
     * @param string $kf_account
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function getWaitKfsession($kf_account)
    {
        $result = $this->wechat->httpGet(self::WECHAT_ACCOUNT_KFSESSION_GETWAIT_PREFIX, [
            'access_token' => $this->wechat->getAccessToken()
        ]);
        return isset($result['count'])?$result['count']:false;
    }
}