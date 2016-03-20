<?php
use \Yii;
use yii\helpers\Html;
use yii\grid\GridView;
use modules\wechat\models\ReplyRule;
use modules\wechat\widgets\PagePanel;

$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'reply-index']]) ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a($addTitle, [$addUrl], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-hover'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'options' => [
                    'width' => 75
                ]
            ],
            'name',
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($model) {
                    return Html::tag('span', ReplyRule::$statuses[$model->status], [
                        'class' => 'label label-' . ($model->status == ReplyRule::STATUS_ACTIVE ? 'success' : 'info')
                    ]);
                },
                'filter' => ReplyRule::$statuses,
                'options' => [
                    'width' => 90
                ]
            ],
            [
                'attribute' => 'keywords',
                'format' => 'html',
                'value' => function($model) {
                    return implode(' ', array_map(function($model) {
                        return Html::tag('code', $model->keyword);
                    }, $model->keywords));
                },
            ],
            [//此格式有排序功能
                'attribute' => 'created_at',
                'format' => ['date','php:Y-m-d H:i:s'],
            ],
            [//此格式有排序功能
                'attribute' => 'updated_at',
                'format' => ['date','php:Y-m-d H:i:s'],
            ],
            [
                'attribute' => 'priority',
                'options' => [
                    'width' => 60
                ]
            ],
            [
                'class' => 'modules\wechat\widgets\ActionColumn',
                'template' => '{update} {delete}',
                'header' => '操作',
                'headerOptions' => ['width' => '80'],
//                'options' => [
//                    'width' => 80
//                ],
                'buttons' => [
                    'update' => function($url, $model, $key){
                        return Html::a('<i class="fa fa-file"></i> 更新',
                            [Yii::$app->params['editUrl'], 'id' => $key],
                            ['class' => 'btn btn-default btn-xs']);
                    },
                    'delete' => function($url, $model, $key){
                        return Html::a('<i class="fa fa-ban"></i> 删除',
                            [Yii::$app->params['delUrl'], 'id' => $key],
                            [
                                'class' => 'btn btn-default btn-xs',
                                'data' => ['confirm' => '你确定要删除吗？',]
                            ]
                        );
                    },

                ],
            ],
        ],
    ]); ?>

<?php PagePanel::end() ?>