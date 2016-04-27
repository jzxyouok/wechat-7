<?php

use yii\helpers\Html;
use modules\wechat\models\Media;
use modules\wechat\widgets\GridView;
use modules\wechat\widgets\PagePanel;

$this->title = '素材管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'media-index']]) ?>
    <p>
        <?= Html::a('添加素材', ['create'], [
            'class' => 'btn btn-success'
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'mediaId',
            ],
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    $type = Media::$types;
                    return $type[$model->type];
                },
                'filter' => Media::$types,
                'options' => [
                    'width' => 120
                ]
            ],
            [
                'attribute' => 'material',
                'value' => function ($model) {
                    $material = Media::$materialTypes;
                    return $material[$model->material];
                },
                'filter' => Media::$materialTypes,
                'options' => [
                    'width' => 120
                ]
            ],
            [
                'attribute' => 'created_at',
                'format' => 'html',
                //'format' => ['date','php:Y-m-d H:i:s'],
                'value' => function($model) {
                        return Html::tag('span', date('Y-m-d H:i:s',$model->created_at), [
                            'class' => 'label label-'.($model->material=='tomporary' && $model->created_at+259200>time() ? 'success' : $model->material=='permanent' ? 'success':'danger')
                        ]);


                },
            ],
            [
                'class' => 'modules\wechat\widgets\ActionColumn',
                'template' => '{view} {update} {delete}',
                'header' => '操作',
//                'headerOptions' => ['width' => '80px'],
//                'contentOptions' => ['style' => 'padding-left:5px'],
//                'options' => [
//                    'width' => 80
//                ],
                'buttons' => [
                    'view' => function($url, $model, $key){
                        return Html::a('<i class="fa fa-forward"></i> 查看',
                            [Yii::$app->params['viewUrl'], 'id' => $key, 'mediaid' => $model->mediaId],
                            ['class' => 'btn btn-default btn-xs']);
                    },
                    'update' => function($url, $model, $key){
                        if($model->material=='permanent' && $model->type=='news'){
                            return Html::a('<i class="fa fa-file"></i> 更新',
                                [Yii::$app->params['editUrl'], 'id' => $key],
                                ['class' => 'btn btn-default btn-xs']);
                        }
                        return null;

                    },
                    'delete' => function($url, $model, $key){
                        if($model->material=='permanent'){
                            return Html::a('<i class="fa fa-ban"></i> 删除',
                                [Yii::$app->params['delUrl'], 'id' => $key, 'mediaid' => $model->mediaId],
                                [
                                    'class' => 'btn btn-default btn-xs',
                                    'data' => ['confirm' => '你确定要删除吗？',]
                                ]
                            );
                        }else{
                            return Html::a('<i class="fa fa-ban"></i> 删除',
                                [Yii::$app->params['delUrl'], 'id' => $key],
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

<?php PagePanel::end() ?>
