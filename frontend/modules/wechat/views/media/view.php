<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use modules\wechat\assets\AudioAsset;
AudioAsset::register($this);

/* @var $this yii\web\View */
/* @var $model modules\wechat\models\Media */

$this->title = $model->getByType($model->type).'-'.$model->getByMaterial($model->material);
$this->params['breadcrumbs'][] = ['label' => 'Media', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<script>
    audiojs.events.ready(function() {
        var as = audiojs.createAll();
    });
</script>
<style>
    .audiojs{
        width: 345px;
    }
    .audiojs .scrubber{
        width: 150px;
    }
    .audiojs .error-message{
        width: 280px;
    }
</style>
<div class="media-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if($model->material=='permanent' && $model->type=='news'):?>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif?>
        <?php if($model->material=='permanent'): ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id, 'mediaid' => $model->mediaId], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定删除当前素材吗?',
                'method' => 'post',
            ],
        ]) ?>
        <?php else: ?>
            <?= Html::a('删除', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => '确定删除当前素材吗?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'mediaId',
            ['label'=>'素材','value'=>$model->getByFile($model->type,$model->file),'format'=>'raw'],
            ['label'=>'媒体类型','value'=>$model->getByType($model->type)],
            ['label'=>'素材类别','value'=>$model->getByMaterial($model->material)],
            ['label'=>'创建时间','value'=>date("Y-m-d H:i:s",$model->created_at)],
            ['label'=>'更新时间','value'=>date("Y-m-d H:i:s",$model->updated_at)],
        ],
    ]) ?>

</div>
