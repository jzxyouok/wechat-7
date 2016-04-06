<?php

namespace modules\wechat\controllers;

use yii;
use yii\web\Controller;
use modules\wechat\models\Wechat;
use components\MpWechat;

class ApiController extends Controller
{
    //关闭Csrf
    public $enableCsrfValidation = false;
    public $MpWechat;
    public $wid;
    public $wechat;
    public function init()
    {
        $this->wid = Yii::$app->request->get('wid');
        if (!$this->wid || ($this->wechat = Wechat::findOne($this->wid)) === null) {
            return false;
        }

        $this->MpWechat = Yii::createObject(MpWechat::className(), [$this->wechat]);
    }

    /**
    *   微信服务器请求签名检测
    */
    public function actionIndex()
    {
        $status=$this->MpWechat->checkSignature();

        if($status){
            $echostr=Yii::$app->request->get('echostr');

            $this->wechat->status = '1';
            $this->wechat->save();
            return $echostr;
        }

    }

    /**
    *   获取AccessToken
    */
    public function actionGetAccessToken()
    {
        $access_token=$this->MpWechat->getAccessToken();
        return $access_token;
    }

    /**
    *   获取微信服务器IP地址
    */
    public function actionIp()
    {
        $ip=$this->MpWechat->getIp();
        return json_encode($ip,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   创建自定义菜单
    *
    */
    public function actionCreateMenu()
    {
        $menuArr=[
            [
                "type"=>"click",
                "name"=>"今日歌曲",
                "key"=>"今日歌曲"
            ],
            [
                "name"=>"扫码",
                "sub_button"=>[
                    [
                        "type"=>"scancode_waitmsg",
                        "name"=>"扫码带提示",
                        "key"=>"rselfmenu_0_0",
                        "sub_button"=>[ ]
                    ],
                    [
                        "type"=>"scancode_push",
                        "name"=>"扫码推事件",
                        "key"=>"rselfmenu_0_1",
                        "sub_button"=>[ ]
                    ]
                ]
             ],
             [
                "name"=>"菜单",
                "sub_button"=>[
                    [
                       "type"=>"view",
                       "name"=>"搜索",
                       "url"=>"http://www.soso.com/"
                    ],
                    [
                       "type"=>"view",
                       "name"=>"视频",
                       "url"=>"http://v.qq.com/"
                    ],
                    [
                       "type"=>"click",
                       "name"=>"赞一下我们",
                       "key"=>"V1001_GOOD"
                    ]
                ]
            ]
        ];

        $menu=$this->MpWechat->createMenu($menuArr);
        return $menu;
    }

    /**
    *   查询自定义菜单
    *
    */
    public function actionGetMenu()
    {
        $menu=$this->MpWechat->getMenu();
        return json_encode($menu,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   删除自定义菜单
    *
    */
    public function actionDeleteMenu()
    {
        $menu=$this->MpWechat->deleteMenu();
        return $menu;
    }

    /**
    *   获取自定义菜单配置接口
    *   返回menuid
    */
    public function actionCreateDiyMenu()
    {
        $menuArr=[
            [
                "type"=>"click",
                "name"=>"今日歌曲",
                "key"=>"V1001_TODAY_MUSIC"
            ]
        ];

        $matchruleArr=[
            "group_id"=>"0",
            "sex"=>"1",
            "country"=>"中国",
            "province"=>"广东",
            "city"=>"东莞",
            "client_platform_type"=>"0",
            "language"=>"zh_CN"
        ];

        $menu=$this->MpWechat->createDiyMenu($menuArr,$matchruleArr);
        return json_encode($menu,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   删除自定义菜单配置接口
    *
    */
    public function actionDelDiyMenu()
    {
        $menuid=[
            "menuid"=>"403941742"
        ];

        $menu=$this->MpWechat->deleteDiyMenu($menuid);
        return json_encode($menu,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   测试个性化菜单匹配结果
    *
    */
    public function actionMatchDiyMenu()
    {
        $user_id=[
            "user_id"=>"oltsUs2NdBNgta73EEvflMLr5V_Q"
        ];

        $menu=$this->MpWechat->matchDiyMenu($user_id);
        return json_encode($menu,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   创建分组
    *
    */
    public function actionCreateGroup()
    {
        $groupArr=[
            "name"=>"test"
        ];

        $group=$this->MpWechat->createGroup($groupArr);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   修改分组名
    *
    */
    public function actionUpdateGroup()
    {
        $groupArr=[
            "id"=>"108",
            "name"=>"test108"
        ];

        $group=$this->MpWechat->updateGroup($groupArr);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   查询所有分组
    *
    */
    public function actionGetGroupList()
    {
        $group=$this->MpWechat->getGroupList();
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   删除分组
    *
    */
    public function actionDeletGroup()
    {
        $groupId='111';
        $group=$this->MpWechat->deletGroup($groupId);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   查询用户所在分组
    *
    */
    public function actionGetUserGroupId()
    {
        $openId='oltsUs9k90PwptLiG_daPZ6Ho8pQ';
        $group=$this->MpWechat->getUserGroupId($openId);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   移动用户分组
    *
    */
    public function actionUpdateUserGroup()
    {
        $data=[
            "openid"=>"oltsUs9k90PwptLiG_daPZ6Ho8pQ",
            "to_groupid"=>"0"
        ];
        $group=$this->MpWechat->updateUserGroup($data);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   批量移动用户分组
    *
    */
    public function actionUpdateUsersGroup()
    {
        $data=[
            "openid_list"=>["oltsUs9k90PwptLiG_daPZ6Ho8pQ","oltsUs2NdBNgta73EEvflMLr5V_Q"],
            "to_groupid"=>"0"
        ];
        $group=$this->MpWechat->updateUsersGroup($data);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   设置备注名
    *
    */
    public function actionUpdateUserMark()
    {
        $data=[
            "openid"=>"oltsUs9k90PwptLiG_daPZ6Ho8pQ",
            "remark"=>"测试"
        ];
        $group=$this->MpWechat->updateUserMark($data);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取用户基本信息(UnionID机制)
    *
    */
    public function actionGetUserInfo()
    {
        $openId="oltsUs9k90PwptLiG_daPZ6Ho8pQ";
        $group=$this->MpWechat->getUserInfo($openId);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   批量获取用户基本信息
    *
    */
    public function actionGetUsersInfo()
    {
        $user_list=[
            ["openid"=>"oltsUs9k90PwptLiG_daPZ6Ho8pQ","lang"=>"zh-CN"],
            ["openid"=>"oltsUs2NdBNgta73EEvflMLr5V_Q","lang"=>"zh-CN"]
        ];
        $group=$this->MpWechat->getUsersInfo($user_list);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取用户列表
    *
    */
    public function actionGetUserList()
    {
        $next_openid="oltsUs9k90PwptLiG_daPZ6Ho8pQ";
        $group=$this->MpWechat->getUserList($next_openid);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   创建二维码ticket
    *
    */
    public function actionCreateQrCode()
    {
        //临时二维码
        // $data=[
        //     'expire_seconds'=>604800,
        //     'action_name'=>'QR_SCENE',
        //     'action_info'=>[
        //         'scene'=>['scene_id'=>'123']
        //     ],
        // ];

        //永久二维码
        //方式1
        // $data=[
        //     'action_name'=>'QR_LIMIT_SCENE',
        //     'action_info'=>[
        //         'scene'=>['scene_id'=>'123']
        //     ],
        // ];

        //方式2
        $data=[
            'action_name'=>'QR_LIMIT_STR_SCENE',
            'action_info'=>[
                'scene'=>['scene_str'=>'123']
            ],
        ];
        $group=$this->MpWechat->createQrCode($data);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   通过ticket换取二维码
    *
    */
    public function actionGetQrCodeserList()
    {
        $getCode=$this->actionCreateQrCode();
        $ticket=json_decode($getCode,1);
        $ticket=$ticket['ticket'];

        $group=$this->MpWechat->getQrCode($ticket);
        return $group;
    }

    /**
    *   长链接转短链接接口
    *
    */
    public function actionGetShortUrl()
    {
        $longUrl='http://wap.koudaitong.com/v2/showcase/goods?alias=128wi9shh&spm=h56083&redirect_count=1';

        $group=$this->MpWechat->getShortUrl($longUrl);
        return $group;
    }

    /**
    *   获取用户增减数据
    *
    */
    public function actionGetUserSummary()
    {
        $dataCube=$this->MpWechat->getDataCube();

        $data=[
            "begin_date"=>"2016-01-20",
            "end_date"=>"2016-01-26"
        ];
        $group=$dataCube->getUserSummary($data);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取累计用户数据
    *
    */
    public function actionGetUserCumulate()
    {
        $dataCube=$this->MpWechat->getDataCube();

        $data=[
            "begin_date"=>"2016-01-20",
            "end_date"=>"2016-01-26"
        ];
        $group=$dataCube->getUserCumulate($data);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   新增临时素材(上传临时多媒体文件)
    *
    */
    public function actionUploadMedia()
    {
        $mediaPath='C:\Users\Gardennet\Desktop\0.png';
        $type='image';//图片（image）、语音（voice）、视频（video）和缩略图（thumb）

        $group=$this->MpWechat->uploadMedia($mediaPath, $type);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取临时素材(下载多媒体文件)
    *
    */
    public function actionGetMedia()
    {
        $mediaId="N43Uboy825h1gEVwUHcSv6nTL0bvJUGcZSAWQeAUHc_3LCY0TNVKFUH2wOS5uYWH";

        $group=$this->MpWechat->getMedia($mediaId);

        $im = imagecreatefromstring($group);
        if ($im !== false) {
            header('Content-Type: image/png');
            imagepng($im);
            imagedestroy($im);
        }
        else {
            echo 'An error occurred.';
        }
    }

    /**
    *   新增永久图文素材
    *
    */
    public function actionAddNewsMaterial()
    {
        $articles=[
           "title"=>'测试',
           "thumb_media_id"=>'2-X-khPdWeqc7it477GoXdU90gYne27PWT847XAddr0',
           "author"=>'Gardenent',
           "digest"=>'digest',
           "show_cover_pic"=>'0',
           "content"=>'content',
           "content_source_url"=>'yii2.lwp8800.com'
        ];

        $group=$this->MpWechat->addNewsMaterial($articles);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   新增其他类型永久素材
    *
    */
    public function actionAddMaterial()
    {
        $mediaPath='C:\Users\Gardennet\Desktop\0.png';
        $type='image';//图片（image）、语音（voice）、视频（video）和缩略图（thumb）
        $data=[];
        //上传视频素材时
        // $data=[
        //   'description'=>[
        //     "title"=>'VIDEO_TITLE',
        //     "introduction"=>'INTRODUCTION'
        //   ]
        // ];

        $group=$this->MpWechat->addMaterial($mediaPath, $type, $data);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取永久素材
    *
    */
    public function actionGetMaterial()
    {
        $mediaId='yTeiakNS5IWUhR4K4kU4KnBiZKoyowTAQdVquwqUyH0';

        $group=$this->MpWechat->getMaterial($mediaId);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   删除永久素材
    *
    */
    public function actionDeleteMaterial()
    {
        $mediaId='yTeiakNS5IWUhR4K4kU4KuZk-rREVXv--mgOEi4SHO4';

        $group=$this->MpWechat->deleteMaterial($mediaId);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   修改永久图文素材
    *
    */
    public function actionUpdateNewsMaterial()
    {
        $data=[
            "media_id"=>'yTeiakNS5IWUhR4K4kU4KuZk-rREVXv--mgOEi4SHO4',
            "index"=>'0',//要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义），第一篇为0
            "articles"=> [
                "title"=>'测试',
                "thumb_media_id"=>'xxx',
                "author"=>'Gardenent',
                "digest"=>'digest',//图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
                "show_cover_pic"=>'1',//是否显示封面，0为false，即不显示，1为true，即显示
                "content"=>'content',
                "content_source_url"=>'yii2.lwp8800.com'
            ]
        ];

        $group=$this->MpWechat->updateNewsMaterial($data);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取素材总数
    *
    */
    public function actionGetMaterialCount()
    {
        $group=$this->MpWechat->getMaterialCount();
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取素材列表
    *
    */
    public function actionGetMaterialList()
    {
        $data=[
            "type"=>'image',//图片（image）、视频（video）、语音 （voice）、图文（news）
            "offset"=>0,
            "count"=>20
        ];
        $group=$this->MpWechat->getMaterialList($data);
        return json_encode($group,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   添加客服账号
    *
    */
    public function actionAddAccount()
    {
        $accountArr=[
            "kf_account"=>"test1@gh_3b9b50e1588f",//格式为：账号前缀@公众号微信号
            "nickname"=>"客服1",
            "password"=>"96e79218965eb72c92a549dd5a330112"
        ];

        $customService=$this->MpWechat->getCustomService();

        $account=$customService->addAccount($accountArr);
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   修改客服帐号
    *
    */
    public function actionUpdateAccount()
    {
        $accountArr=[
            "kf_account"=>"test1@gh_3b9b50e1588f",//格式为：账号前缀@公众号微信号
            "nickname"=>"客服1",
            "password"=>"96e79218965eb72c92a549dd5a330112"
        ];

        $customService=$this->MpWechat->getCustomService();

        $account=$customService->updateAccount($accountArr);
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   删除客服帐号
    *
    */
    public function actionDeleteAccount()
    {
        $accountArr="test1@gh_3b9b50e1588f";//格式为：账号前缀@公众号微信号

        $customService=$this->MpWechat->getCustomService();

        $account=$customService->deleteAccount($accountArr);
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   设置客服账号头像
    *
    */
    public function actionSetAccountAvatar()
    {
        $accountName="test1@gh_3b9b50e1588f";//格式为：账号前缀@公众号微信号
        $avatarPath='C:\Users\Gardennet\Desktop\0.png';

        $customService=$this->MpWechat->getCustomService();

        $account=$customService->setAccountAvatar($accountName, $avatarPath);
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取客服聊天记录
    *
    */
    public function actionGetMessageRecord()
    {
        $data=[
            "endtime"=>'1453882218',
            "pageindex"=>'1',
            "pagesize"=>'10',
            "starttime"=>'1453880218'
        ];

        $customService=$this->MpWechat->getCustomService();

        $account=$customService->getMessageRecord($data);
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取客服基本信息
    *
    */
    public function actionGetAccountList()
    {
        $customService=$this->MpWechat->getCustomService();

        $account=$customService->getAccountList();
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取在线客服接待信息
    *
    */
    public function actionGetAccountOnlineKflist()
    {
        $customService=$this->MpWechat->getCustomService();

        $account=$customService->getAccountOnlinekfList();
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   创建会话
    *
    */
    public function actionCreateKfsession()
    {
        $data=[
            "kf_account"=>"test1@gh_3b9b50e1588f",//格式为：账号前缀@公众号微信号
            "openid"=>"oltsUs9k90PwptLiG_daPZ6Ho8pQ",//客户openid
            "text"=>"这是一段附加信息"
        ];

        $customService=$this->MpWechat->getCustomService();

        $account=$customService->createKfsession($data);
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   关闭会话
    *
    */
    public function actionCloseKfsession()
    {
        $data=[
            "kf_account"=>"test1@gh_3b9b50e1588f",//格式为：账号前缀@公众号微信号
            "openid"=>"oltsUs9k90PwptLiG_daPZ6Ho8pQ",//客户openid
            "text"=>"这是一段附加信息"
        ];

        $customService=$this->MpWechat->getCustomService();

        $account=$customService->closeKfsession($data);
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取客户的会话状态
    *
    */
    public function actionGetKfsession()
    {
        $openid="oltsUs9k90PwptLiG_daPZ6Ho8pQ";//客户openid;

        $customService=$this->MpWechat->getCustomService();

        $account=$customService->getKfsession($openid);
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取客服的会话列表
    *
    */
    public function actionGetListKfsession()
    {
        $kf_account="test1@gh_3b9b50e1588f";//格式为：账号前缀@公众号微信号

        $customService=$this->MpWechat->getCustomService();

        $account=$customService->getListKfsession($kf_account);
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }

    /**
    *   获取未接入会话列表
    *
    */
    public function actionGetWaitKfsession()
    {
        $kf_account="test1@gh_3b9b50e1588f";//格式为：账号前缀@公众号微信号

        $customService=$this->MpWechat->getCustomService();

        $account=$customService->getWaitKfsession($kf_account);
        return json_encode($account,JSON_UNESCAPED_UNICODE);
    }
}
