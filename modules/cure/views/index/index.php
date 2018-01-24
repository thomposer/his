<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\modules\cure\models\Cure;
use yii\helpers\Url;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\modules\cure\models\search\CureSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '治疗';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '今日病人', 'url' => Url::to(['@cureIndexIndex', 'type' => 3]), 'type' => 3, 'icon_img' => $public_img_path . '/tab/tab_treatment.png'],
        ['title' => '特需病人', 'url' => Url::to(['@cureIndexIndex', 'type' => 4]), 'type' => 4, 'icon_img' => $public_img_path . '/tab/tab_treatment.png'],
    ],
    'activeData' => [
        'type' => 3
    ]
];
$params = [
    'searchName' => 'CureSearch',
    'statusName' => 'status',
    'buttons' => [
        [
            'title' => '全部',
            'statusCode' => 0,
        ],
        [
            'title' => '待治疗',
//            'num' => 1,
            'statusCode' => 3,
        ],
        [
            'title' => '治疗中',
//            'num' => 3,
            'statusCode' => 2,
        ],
        [
            'title' => '已完成',
//            'num' => 2,
            'statusCode' => 1,
        ]
    ]
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/cure/cure.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="cure-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>

    <div class = "box delete_gap">
            <div class = 'row search-margin'>
                <div class = 'col-sm-6 col-md-6'>
                    <?= $this->render(Yii::getAlias('@searchStatus'),$params) ?>
                </div>
                <div class = 'col-sm-6 col-md-6'>
                    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
                </div>
            </div>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border'],
            'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
            'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
            'id'=>'crud-datatable',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            /* 'filterModel' => $searchModel, */
            'columns' => [
//                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'username',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'score-warn'],
                    'value' => function ($model) {
//                $user_sex = $model->sex == 1 ? '男' : '女';
                $user_sex = Patient::$getSex[$model->sex];
                $dateDiffage = Patient::dateDiffage($model->birthday, time());
                return Html::encode($model->username) . '(' . $user_sex . ' ' . $dateDiffage . ')'.Patient::getUserScore($model->pain_score,$model->fall_score);
            },
                ],
                [
                    'attribute' => 'room_name',
                ],
                [
                    'attribute' => 'cure_name',
                    'headerOptions' => ['class' => 'col-sm-3 col-md-3'],
                    'value' => function ($model) use($cureNameList) {
                        return $cureNameList[$model->record_id];
                    }
                ],
                [
                    'attribute' => 'doctor_name',
                ],
                [
                    'attribute' => 'type_description',
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{cure}{under-cure}{complete}',
                    'headerOptions' => ['style' => 'width:290px'],
                    'buttons' => [
                        'cure' => function ($url, $model, $key) use($cureStatusCount) {
                                if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/cure', $this->params['permList'])) {
                                    return false;
                                }
                                $cureNum = $cureStatusCount[$model->record_id][3];
                                $btn_title = "待治疗" . $cureNum;
                                $options = [
                                    'class' => 'btn btn-delete',
                                    'role' => 'modal-remote',
                                    'data-toggle' => 'tooltip',
                                ];
                                if ($cureNum) {
                                    return Html::a($btn_title, Url::to(['@cureIndexCure', 'id' => $model->record_id]), $options);
                                } else {
                                    return '';
                                }
                            },
                            'under-cure' => function ($url, $model, $key) use($cureStatusCount) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/under-cure', $this->params['permList'])) {
                                return false;
                            }
                            $cureNum = $cureStatusCount[$model->record_id][2];
                            $btn_title = "治疗中" . $cureNum;
                            $options = [
                                'class' => 'btn btn-success',
                                'role' => 'modal-update',
                                'data-toggle' => 'tooltip',
                                'data-pjax' => 0
                            ];
                            if ($cureNum) {
                                    return Html::a($btn_title, Url::to(['@cureIndexUnderCure', 'id' => $model->record_id]), $options);
                                } else {
                                    return '';
                                }
                            },
                          'complete' => function ($url, $model, $key) use($cureStatusCount){
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/complete', $this->params['permList'])) {
                                return false;
                            }
                            $cureNum = $cureStatusCount[$model->record_id][1];
                            $btn_title = "已完成" . $cureNum;
                            $options = [
                                'class' => 'btn btn-default',
                                'role' => 'modal-update',
                                'data-toggle' => 'tooltip',
                                'data-pjax' => 0
                            ];
                            if ($cureNum) {
                                    return Html::a($btn_title, Url::to(['@cureIndexComplete', 'id' => $model->record_id]), $options);
                                    } else {
                                        return '';
                                    }
                                },
                            ]
                        ],
                    ],
                ]);
                ?>
            </div>
            <?php Pjax::end() ?>
        </div>

        <?php $this->endBlock(); ?>
        <?php $this->beginBlock('renderJs'); ?>

        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
