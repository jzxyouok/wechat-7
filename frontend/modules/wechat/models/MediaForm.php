<?php
namespace modules\wechat\models;

use yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\validators\Validator;

/**
 * 素材上传(表单)验证类
 * @package modules\wechat\models
 */
class MediaForm extends Model
{
    /**
     * 素材类型
     * @var string
     */
    public $type;
    /**
     * 素材类别
     * @var string
     */
    public $material = Media::MATERIAL_TEMPORARY;
    /**
     * 上传文件
     * @var UploadedFile
     */
    public $file;
    /**
     * 永久视频素材标题
     * @var string
     */
    public $title;
    /**
     * 永久视频素材描述
     * @var string
     */
    public $introduction;
    /**
     * 公众号
     * @var Wechat
     */
    protected $wechat;

    /**
     * @inhertdoc
     */
    public function __construct(Wechat $wechat, $config = [])
    {
        $this->wechat = $wechat;
        parent::__construct($config);
    }

    /**
     * @inhertdoc
     */
    public function rules()
    {
        return [
            [['type', 'material', 'file'], 'required','message'=>'{attribute} 不能为空'],
            [['type'], 'in', 'range' => array_keys(Media::$types)],
            [['material'], 'in', 'range' => array_keys(Media::$materialTypes)],
            ['title', 'checkTitle', 'skipOnEmpty' => false, 'skipOnError' => false],
            ['introduction', 'checkIntroduction', 'skipOnEmpty' => false, 'skipOnError' => false],
            ['file', 'checkFile']
        ];
    }


    /**
     * 标题验证
     * @param $attribute
     * @param $params
     */
    public function checkTitle($attribute, $params)
    {
        if ($this->material == 'permanent' && $this->type == 'video' && $this->title=='') {
            $this->addError($attribute, '标题 不能为空');
        }
    }

    /**
     * 描述验证
     * @param $attribute
     * @param $params
     */
    public function checkIntroduction($attribute, $params)
    {
        if ($this->material == 'permanent' && $this->type == 'video' && $this->introduction=='') {
            $this->addError($attribute, '描述 不能为空');
        }
    }

    /**
     * 各类型上传文件验证
     * @param $attribute
     * @param $params
     */
    public function checkFile($attribute, $params)
    {
        // 按照类型 验证上传
        if (!$this->hasErrors()) {
            switch ($this->type) {
                case Media::TYPE_IMAGE:
                    $rule = [[$attribute], 'file', 'skipOnEmpty' => false, 'extensions' => 'bmp,png,jpeg,jpg,gif', 'maxSize' => 2097152]; // 2MB
                    break;
                case Media::TYPE_THUMB:
                    $rule = [[$attribute], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg', 'maxSize' => 65536]; // 64KB
                    break;
                case Media::TYPE_VOICE:
                    $rule = [[$attribute], 'file', 'skipOnEmpty' => false, 'extensions' => 'amr, mp3', 'maxSize' =>  2097152]; // 2MB
                    break;
                case Media::TYPE_VIDEO:
                    $rule = [[$attribute], 'file', 'skipOnEmpty' => false, 'extensions' => 'mp4', 'maxSize' =>  10485760]; // 10MB
                    break;
                default:
                    $rule = [[$attribute], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg', 'maxSize' => 65536]; // 64KB
            }
            $validator = Validator::createValidator($rule[1], $this, (array)$rule[0], array_slice($rule, 2));
            $validator->validateAttributes($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => '素材类型',
            'material' => '素材类别',
            'file' => '素材',
            'title' => '标题',
            'introduction' => '描述'
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        if ($this->material == Media::MATERIAL_TEMPORARY) {//临时素材
            $method = 'uploadMedia';
        } elseif ($this->material == Media::MATERIAL_PERMANENT) {//永久素材
            $method = 'addMaterial';
        } else {
            $this->addError('material', '错误的素材类别');
            return false;
        }
        $sdk = $this->wechat->getSdk();

        $ext = $this->file->getExtension();//后缀名
        $imageName = time().'_'.rand(1000,9999).'.'.$ext;//文件名
        $path='uploads/'.$this->type.'/'.$this->wechat->id.'/'.date('Ymd',time());//保存路径

        FileHelper::createDirectory($path);
        $file=$path.'/'.$imageName;//文件完整路径
        $this->file->saveAs($file);

        $data = [];

        if($this->material == Media::MATERIAL_PERMANENT && $this->type==Media::TYPE_VIDEO){
            //上传视频素材时
            $description=json_encode(["title"=>$this->title,"introduction"=>$this->introduction],JSON_UNESCAPED_UNICODE);
            $data=['description' =>$description];
        }


        if (!($result = call_user_func_array([$sdk, $method], [$file, $this->type, $data]))) {
            $this->addError('file', json_encode($sdk->lastError));
            return false;
        }
        //由于微信接口media_id返回信息不一样，缩略图返回的是thumb_media_id，所以分情况处理
        if($this->material == Media::MATERIAL_TEMPORARY){//临时素材
            switch ($this->type) {
                case Media::TYPE_THUMB:
                    $mediaId=$result['thumb_media_id'];
                    break;
                default:
                    $mediaId=$result['media_id'];
            }
        }else{
            $mediaId=$result['media_id'];
        }



        $media = Yii::createObject(Media::className());
        $media->setAttributes([
            'wid' => $this->wechat->id,
            'mediaId' => $mediaId,
            'file' => $file,
            'type' => $this->type,
            'material' => $this->material,
            'result' => $result,
            'url' => empty($result['url'])?'':$result['url'],
            'created_at' => empty($result['created_at'])?time():$result['created_at']
        ]);
        return $media->save();
    }
}