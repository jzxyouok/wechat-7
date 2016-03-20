<?php

use yii\helpers\Html;
use modules\wechat\widgets\PagePanel;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => $label, 'url' => [$url]];
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?php PagePanel::begin(['options' => ['class' => 'reply-update']]) ?>

    <?= $this->render($formDisplay, [
        'model' => $model,
        'ruleKeyword' => $ruleKeyword,
        'ruleKeywords' => $ruleKeywords
    ]) ?>

<?php PagePanel::end() ?>
