<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use modules\wechat\widgets\GridView;
use modules\wechat\models\Media;

Yii::$app->request->getIsAjax() && $this->context->layout = false;
?>
<style>
    .audiojs{
        width: 150px;
    }
    .audiojs .scrubber{
        width: 90px;
    }
    .audiojs .error-message{
        width: 150px;
    }
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="exampleModalLabel">选择媒体素材</h4>
</div>
