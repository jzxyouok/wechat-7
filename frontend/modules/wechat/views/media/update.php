<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\wechat\models\Media */

$this->title = '更新永久素材: ' . ' ' . $media->id;
$this->params['breadcrumbs'][] = ['label' => 'Media', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $media->id, 'url' => ['view', 'id' => $media->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="media-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'media' => $media,
        'news' => $news
    ]) ?>

</div>
