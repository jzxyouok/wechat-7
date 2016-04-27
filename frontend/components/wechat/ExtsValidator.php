<?php
namespace components\wechat;

use Yii;
use yii\validators\Validator;
use yii\helpers\Json;
use yii\validators\ValidationAsset;
use yii\base\Model;

class ExtsValidator extends Validator
{
    public $extensions;

    public $wrongExtension;

    public $uploadRequired;

    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '图片不能为空');
        }
        if (!is_array($this->extensions)) {
            $this->extensions = preg_split('/[\s,]+/', strtolower($this->extensions), -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $this->extensions = array_map('strtolower', $this->extensions);
        }
        if ($this->wrongExtension === null) {
            $this->wrongExtension = Yii::t('yii', 'Only files with these extensions are allowed: {extensions}.');
        }
    }

    protected function validateValue($value)
    {
        if (empty($value) && $this->skipOnEmpty==1) {
            return [$this->message, []];
        }

        return null;
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        ValidationAsset::register($view);
        $label = $model->getAttributeLabel($attribute);
        if (!empty($this->extensions) && !$this->validateExtension($model->$attribute)) {
            $this->wrongExtension = Yii::$app->getI18n()->format($this->wrongExtension, [
                'attribute' => $label,
                'extensions' => implode(', ', $this->extensions),
            ], Yii::$app->language);
            return "
            messages.push(".json_encode($this->wrongExtension, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).");
            ";
        }
    }

    protected function validateExtension($value)
    {
        $extension=substr(strrchr($value, '.'), 1);
        if (!in_array($extension, $this->extensions, true)) {
            return false;
        }
        return true;
    }

}