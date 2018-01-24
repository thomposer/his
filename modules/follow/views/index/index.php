<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use app\modules\follow\models\Follow;
use app\modules\patient\models\Patient;
use yii\helpers\Url;
use app\common\Common;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\follow\models\search\FollowSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '随访管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$params = [
    'searchName' => 'FollowSearch',
    'statusName' => 'follow_state',
    'buttons' => [
        [
            'title' => '全部',
            'statusCode' => 0,
            'num' => isset($countData[0]['count']) ? $countData[0]['count'] : 0,
        ],
        [
            'title' => '待随访',
            'statusCode' => 1,
            'num' => isset($countData[1]['count']) ? $countData[1]['count'] : 0,
        ],
        [
            'title' => '已随访',
            'statusCode' => 2,
            'num' => isset($countData[2]['count']) ? $countData[2]['count'] : 0,
        ],
//        [
//            'title' => '已过期',
//            'statusCode' => 5,
//            'num' => isset($countData[5]['count']) ? $countData[5]['count'] : 0,
//        ],
//        [
//            'title' => '未成功',
//            'statusCode' => 3,
//            'num' => isset($countData[3]['count']) ? $countData[3]['count'] : 0,
//        ],
        [
            'title' => '已取消',
            'statusCode' => 4,
            'num' => isset($countData[4]['count']) ? $countData[4]['count'] : 0,
        ]
    ]
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/follow/selectFollow.css')?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="follow-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

    <div class = "box">
        <div class = 'row search-margin'>

            <div class = 'col-sm-12 col-md-12'>
                <?= $this->render(Yii::getAlias('@searchStatusCommon'), $params) ?>
            </div>
        </div>
        <div class = 'row search-margin'>
            <div class = 'col-sm-12 col-md-12'>
                <?php  echo $this->render('_search', ['model' => $searchModel,'userInfo'=>$userInfo]); ?>
            </div>
        </div>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border header'],
            'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
            'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            /* 'filterModel' => $searchModel, */
            'columns' => [
                [
                    'attribute' => 'patientNumber',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                ],
                [
                    'attribute' => 'patient_id',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        $user_sex = Patient::$getSex[$model->sex];
                        $dateDiffage = Patient::dateDiffage($model->birthday, time());
        //                           $firstRecord = Patient::getFirstRecord($model->firstRecord);
                        $userName = Html::encode($model->username);
                        //                return $userName . '(' . $user_sex . ' ' . $dateDiffage . ')' . $firstRecord;
                        $text = $userName . '(' . $user_sex . ' ' . $dateDiffage . ')';
                        if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@patientIndexView'), $this->params['permList'])) {
                            return $text;
                        }
                        return Html::a($text, ['@patientIndexView', 'id' => $model->patient_id], ['data-pjax' => 0, 'target' => '_blank']);
                    }
                ],
                [
                    'attribute' => 'execute_role',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'value' => function ($model) {
                         return Follow::$getExecuteRole[$model->execute_role];
                      }
                ],
                [
                    'attribute' => 'followPlanExecutorName',
                    'headerOptions' => ['class' => 'col-md-2'],
                ],
                [
                    'attribute' => 'complete_time',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'format' => 'date'
                ],
                [
                    'attribute' => 'planCreatorName',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                ],
                [
                    'attribute' => 'diagnosis_time',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'format' => 'date'
                ],
                [
                    'attribute' => 'follow_state',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'label' => '状态',
                    'format'=>'raw',
                    'value' => function ($model) {
                        $status=$model->follow_state;
                        if($model->follow_state==3){
                            $status=2;
                        }
                        $over=2;
                        if(($model->complete_time< strtotime(date('Y-m-d')))&&$model->follow_state==1){
                            $over=1;
                        }
                        $text=Follow::$getFollowState[$status];
                        $over==1&&$text=$text.' (<span class="text-red-mine">已过期</span>)';
                        $model->follow_state==3&&$text=$text.' (<span class="text-red-mine">失败</span>)';
                        $model->follow_state==2&&$text=$text.' (<span >成功</span>)';
                        return $text;
                    }
                ],
                [
                    'attribute' => 'followExecutorName',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1']
                ],
                [
                    'attribute' => 'follow_remark',
                    'headerOptions' => ['class' => 'col-md-2'],
                    'value'=>  function ($model){
                        return Common::cutStr($model->follow_remark, 20);
                    }
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{view}{execute}{update}{cancel}',
                    'headerOptions' => ['class' => 'col-xs-3 col-sm-3 col-md-3'],
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@followIndexView'), $this->params['permList'])) {
                                return false;
                            }
                            if ($model->follow_state <= 1) {
                                return false;
                            }
                            return Html::a('查看', Url::to(['@followIndexView', 'id' => $model->id]), ['class' => 'op-group-a','data-pjax' => 0]);
                        },
                                'execute' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@followIndexExecute'), $this->params['permList'])) {
                                return false;
                            }
                            if ($model->follow_state == 4) {
                                return false;
                            }
                            $text=Html::a('执行', Url::to(['@followIndexExecute', 'id' => $model->id]), ['class' => 'op-group-a','data-pjax' => 0]);
                            return $text;
                        },
                                'update' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@followIndexUpdate'), $this->params['permList'])) {
                                return false;
                            }
                            if ($model->follow_state==4) {
                                return false;
                            }
                            return Html::a('修改', Url::to(['@followIndexUpdate', 'id' => $model->id]), ['class' => 'op-group-a','data-pjax' => 0]);
                        },
                                'cancel' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@followIndexCancel'), $this->params['permList'])) {
                                return false;
                            }
                            if ($model->follow_state >=2) {
                                return false;
                            }
                            $options = [
                                'role' => 'modal-remote',
                                'data-modal-size' => 'large',
                                'data-url' => Url::to(['@followIndexCancel', 'id' => $model->id]),
                                'class' => 'op-group-a'
                            ];
                            return Html::a('取消', Url::to(['@followIndexCancel', 'id' => $model->id]), $options);
                        }
                            ]
                        ]
                    ],
                ]);
                ?>
            </div>
            <?php 
            $this->registerJs("
            if(localStorage.followSearchState){
                if(localStorage.followSearchState == 2){
                    $('.follow-search .follow-search-line').show();
                    $('.more-word').next().attr('class','fa fa-caret-up');
                }else {
                    $('.follow-search .follow-search-line').hide();
                    $('.more-word').next().attr('class','fa fa-caret-down');
                }
                $('.more-word').unbind('click').click(function () {
                    var state = $(this).attr('state');
                    console.log('click');
                    if(localStorage.followSearchState == 1){
                        $('.follow-search .follow-search-line').show();
                        localStorage.followSearchState = 2;
                        $(this).next().attr('class','fa fa-caret-up');
                    }else {
                        $('.follow-search .follow-search-line').hide();
                        localStorage.followSearchState = 1;
                        $(this).next().attr('class','fa fa-caret-down');
                    }
                });
            }else{
                localStorage.followSearchState = 1;
            };
            ");?>
            <?php Pjax::end() ?>
        </div>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs'); ?>
<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
