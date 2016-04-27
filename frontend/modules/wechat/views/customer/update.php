<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\wechat\models\Media */

$this->title = '修改客服: ' . ' ' . $model->kf_account;
$this->params['breadcrumbs'][] = ['label' => '多客服管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->kf_account, 'url' => ['update', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="media-update">
    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
