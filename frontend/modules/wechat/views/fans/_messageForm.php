<?php
use yii\helpers\Html;
use yii\helpers\Url;
use modules\wechat\models\Message;
use modules\wechat\widgets\ActiveForm;
use modules\wechat\assets\MessageAsset;
use modules\wechat\assets\WechatAsset;
use modules\wechat\widgets\FileApiInputWidget;
use modules\wechat\assets\FileApiAsset;

FileApiAsset::register($this);
WechatAsset::register($this);
MessageAsset::register($this);
?>
<?php $form = ActiveForm::begin([
    'id' => 'messageForm',
    'layout' => 'horizontal'
]); ?>
<?= Html::activeHiddenInput($model, 'toUser') ?>

<?= $form->field($model, 'msgType')->inline()->radioList(Message::$types) ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'musicUrl')->widget(FileApiInputWidget::className(), [
    'jsOptions' => [
        'url' => Url::toRoute(['media/ajax-upload','filename'=>'histoeryMusic'])
    ]
]) ?>

<?= $form->field($model, 'hqMusicUrl')->widget(FileApiInputWidget::className(), [
    'jsOptions' => [
        'url' => Url::toRoute(['media/ajax-upload','filename'=>'histoeryMusic'])
    ]
]) ?>

<?= $form->field($model, 'content')->textarea() ?>

<?= $form->field($model, 'description')->textarea() ?>

<?php $a = Html::a('浏览素材', ['media/pick'], [
    'class' => 'btn btn-default',
    'id' => 'btn-type',
    'data' => [
        'toggle' => 'modal',
        'target' => '#mediaModal'
    ]
]) ?>

<?php $thumb_a = Html::a('浏览素材', ['media/pick'], [
    'class' => 'btn btn-default',
    'id' => 'btn-thumbType',
    'data' => [
        'toggle' => 'modal',
        'target' => '#mediaModal'
    ]
]) ?>

<?= $form->field($model, 'mediaId')->widget(FileApiInputWidget::className(), [
    'template' => "\n<div id=\"{id}\" class=\"input-group\">\n<div class=\"input-group-btn\">\n{fields}\n</div>\n{input}\n<div class=\"input-group-btn\">\n{$a}\n</div></div>\n",
    'fields' => Html::hiddenInput('mediaType'),
    'jsOptions' => [
        'url' => $uploadUrl
    ]
]) ?>

<?= $form->field($model, 'thumbMediaId')->widget(FileApiInputWidget::className(), [
    'template' => "\n<div id=\"{id}\" class=\"input-group\">\n<div class=\"input-group-btn\">\n{fields}\n</div>\n{input}\n<div class=\"input-group-btn\">\n{$thumb_a}\n</div></div>\n",
    'fields' => Html::hiddenInput('mediaType'),
    'jsOptions' => [
        'url' => $uploadUrl
    ]
]) ?>

    <div class="form-group submit-button">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton('发送', ['class' => 'btn btn-block btn-primary']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs(<<<EOF
    $('#messageForm').message();
EOF
);