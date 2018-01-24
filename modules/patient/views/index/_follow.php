<?php
use app\assets\AppAsset;
use app\modules\follow\models\Follow;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$params = [
    'searchName' => 'FollowSearch',
    'statusName' => 'follow_state',
    'buttons' => [
        [
            'title' => '全部',
            'statusCode' => "",
            'url' => "",
            'num' => isset($countData[0]['count']) ? $countData[0]['count'] : 0,
            "suffix" => "#follow_suffix"
        ],
        [
            'title' => '待随访',
            'statusCode' => "1",
            'url' => "",
            'num' => isset($countData[1]['count']) ? $countData[1]['count'] : 0,
            "suffix" => "#follow_suffix"
        ],
        [
            'title' => '已随访',
            'statusCode' => "2",
            'url' => "",
            "suffix" => "#follow_suffix",
            'num' => isset($countData[2]['count']) ? $countData[2]['count'] : 0
        ],
        [
            'title' => '已取消',
            'statusCode' => "4",
            'url' => "",
            "suffix" => "#follow_suffix",
            'num' => isset($countData[4]['count']) ? $countData[4]['count'] : 0
        ]
    ]
];
?>
<div class="follow-index">

        <div class='row btn-group-space'>
            <div class='col-sm-6 col-md-6'>
                <?= $this -> render(Yii ::getAlias('@searchStatusCommon'), $params) ?>
            </div>
            <div class='col-sm-6 col-md-6'>
                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <?php Pjax ::begin(['id' => 'crud-datatable-pjax', 'timeout' => 5000,'enablePushState' => false]) ?>
        <?=
        GridView ::widget([
            'dataProvider' => $followDataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table follow-table table-hover table-border'],
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'firstPageLabel' => Yii ::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii ::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii ::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii ::getAlias('@lastPageLabel'),
            ],
            'columns' => [
                [
                    'attribute' => 'complete_time',
                    'format' => 'date'
                ],
                [
                    'attribute' => 'execute_role',
                    'value' => function ($model) {
                        return Follow ::$getExecuteRole[ $model -> execute_role ];
                    }
                ],
                [
                    'attribute' => 'followPlanExecutorName',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                ],
                [
                    'attribute' => 'planCreatorName',
                ],
                [
                    'attribute' => 'create_time',
                    'format' => 'date'
                ],
                [
                    'attribute' => 'spot_name',
                ],
                [
                    'attribute' => 'follow_state',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $status = $model -> follow_state;
                        if ($model -> follow_state == 3) {
                            $status = 2;
                        }
                        $over = 2;
                        if (($model -> complete_time < strtotime(date('Y-m-d'))) && $model -> follow_state == 1) {
                            $over = 1;
                        }
                        $text = Follow ::$getFollowState[ $status ];
                        $over == 1 && $text = $text . ' (<span class="text-red-mine">已过期</span>)';
                        $model -> follow_state == 3 && $text = $text . ' (<span class="text-red-mine">失败</span>)';
                        $model -> follow_state == 2 && $text = $text . ' (<span >成功</span>)';

                        return $text;
                    }
                ],
                [
                    'attribute' => 'followExecutorName',
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{view}{execute}{update}{cancel}',
                    'headerOptions' => ['class' => 'col-xs-3 col-sm-3 col-md-3'],
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            if (!isset($this -> params['permList']['role']) && !in_array(Yii ::getAlias('@followIndexView'), $this -> params['permList'])) {
                                return false;
                            }
                            if ($model -> follow_state <= 1) {
                                return false;
                            }

                            return Html ::a('查看', Url ::to(['@followIndexView', 'id' => $model -> id]), ['class' => 'op-group-a','data-pjax' => 0]);
                        },
                        'execute' => function ($url, $model, $key) {
                            if (!isset($this -> params['permList']['role']) && !in_array(Yii ::getAlias('@followIndexExecute'), $this -> params['permList'])) {
                                return false;
                            }
                            if ($model -> follow_state == 4) {
                                return false;
                            }
                            $line = '<span style="color:#99a3b1">丨</span>';
                            $text = Html ::a('执行', Url ::to(['@followIndexExecute', 'id' => $model -> id]), ['class' => 'op-group-a','data-pjax' => 0]);

                            return $model -> follow_state != 1 ? $line . $text : $text;
                        },
                        'update' => function ($url, $model, $key) {
                            if (!isset($this -> params['permList']['role']) && !in_array(Yii ::getAlias('@followIndexUpdate'), $this -> params['permList'])) {
                                return false;
                            }
                            if ($model -> follow_state == 4) {
                                return false;
                            }

                            return '<span style="color:#99a3b1">丨</span>' . Html ::a('修改', Url ::to(['@followIndexUpdate', 'id' => $model -> id]), ['class' => 'op-group-a','data-pjax' => 0]);
                        },
                        'cancel' => function ($url, $model, $key) {
                            if (!isset($this -> params['permList']['role']) && !in_array(Yii ::getAlias('@followIndexCancel'), $this -> params['permList'])) {
                                return false;
                            }
                            if ($model -> follow_state >= 2) {
                                return false;
                            }
                            $options = [
                                'role' => 'modal-remote',
                                'data-modal-size' => 'large',
                                'data-url' => Url ::to(['@followIndexCancel', 'id' => $model -> id]),
                                'class' => 'op-group-a',
                                'data-pjax' => '0'
                            ];

                            return '<span style="color:#99a3b1">丨</span>' . Html ::a('取消', Url ::to(['@followIndexCancel', 'id' => $model -> id,'orgin' => 1]), $options);
                        }
                    ]
                ]
            ],
        ]);
        ?>
    <?php Pjax ::end() ?>
</div>
