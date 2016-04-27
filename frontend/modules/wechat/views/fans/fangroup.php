<?php

use yii\helpers\Html;
use yii\helpers\Url;
use modules\wechat\models\FansGroups;
use modules\wechat\widgets\GridView;
use modules\wechat\widgets\PagePanel;
use modules\wechat\assets\WechatAsset;

$wechatAsset = WechatAsset::register($this);

$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'fans-update']]) ?>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<p>
    <?= Html::a($addTitle, [$addUrl], ['class' => 'btn btn-success','data' => [
        'toggle' => 'modal',
        'target' => '#groupModal'
    ]]) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'id',
            'options' => [
                'width' => 75
            ]
        ],
        [
            'attribute' => 'groupid',
            'options' => [
                'width' => 200
            ]
        ],
        [
            'attribute' => 'name',
            'options' => [
                'width' => 200
            ]
        ],
        [
            'attribute' => 'count',
            'options' => [
                'width' => 200
            ]
        ],
        [
            'class' => 'modules\wechat\widgets\ActionColumn',
            'header' => '操作',
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function($url, $model, $key){
                    if($model->isdefault==0){
                        return Html::a('<i class="fa fa-file"></i> 更新',
                            [Yii::$app->params['editUrl'], 'id' => $key],
                            ['class' => 'btn btn-default btn-xs','data' => [
                                'toggle' => 'modal',
                                'target' => '#groupUpdateModal'
                            ]]);
                    }else{
                        return Html::tag('span',
                            '系统分组不能修改',
                            ['class' => 'btn btn-default btn-xs']);
                    }
                    return null;

                },
                'delete' => function($url, $model, $key){
                    if($model->isdefault==0){
                        return Html::a('<i class="fa fa-ban"></i> 删除',
                            [Yii::$app->params['delUrl'], 'id' => $key,'groupid'=>$model->groupid],
                            [
                                'class' => 'btn btn-default btn-xs',
                                'data' => ['confirm' => '你确定要删除吗？',]
                            ]
                        );
                    }

                },

            ],
        ],
    ],
]); ?>

<button type="button" onclick="checkFansGroup();" class="btn btn-primary">同步分组</button>
<?php PagePanel::end() ?>
<script type="text/javascript">
    function checkFansGroup(){
        $.post('<? echo Url::to(['fans/check-fansgroup']); ?>',
            function (data) {
                if(data.status==1){
                    alert(data.info);
                    window.location.reload();
                }else{
                    alert(data.info);
                }

            },
            "json")
    }
</script>
