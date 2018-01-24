<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\inspect\models\Inspect;
use app\modules\patient\models\Patient;
use app\modules\make_appointment\models\Appointment;
use app\modules\patient\models\PatientRecord;
use rkit\yii2\plugins\ajaxform\Asset;

CrudAsset::register($this);
Asset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\inspect\models\search\InspectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '实验室检查';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tab = [
    'titleData' => [
        ['title' => '今日病人', 'url' => Url::to(['@inspectIndexIndex', 'type' => 3]), 'type' => 3, 'icon_img' => $public_img_path . '/tab/tab_experiment.png'],
        ['title' => '特需病人', 'url' => Url::to(['@inspectIndexIndex', 'type' => 4]), 'type' => 4, 'icon_img' => $public_img_path . '/tab/tab_experiment.png'],
        ['title' => '检查历史', 'url' => Url::to(['@inspectIndexIndex', 'type' => 5]), 'type' => 5, 'icon_img' => $public_img_path . '/tab/tab_experiment.png'],
    ],
    'activeData' => [
        'type' => 3
    ]
];

$params = [
    'searchName' => 'InspectSearch',
    'statusName' => 'status',
    'buttons' => [
        [
            'title' => '全部',
            'statusCode' => 0,
        ],
        [
            'title' => '待检验',
            'statusCode' => 3,
        ],
        [
            'title' => '检验中',
            'statusCode' => 2,
        ],
        [
            'title' => '已完成',
            'statusCode' => 1,
        ],
        [
            'title' => '已取消',
            'statusCode' => 4,
        ]
    ]
];
$type=Yii::$app->request->get('type');
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/inspect/inspect.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/commonPrint.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/check/print.css'); ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="inspect-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tab]) ?>

    <div class = "box delete_gap">
        <div class = 'row search-margin'>
            <div class = 'col-sm-6 col-md-6'>
                <?php if ($type != 5): ?>
                    <?= $this->render(Yii::getAlias('@searchStatus'), $params) ?>
                <?php endif; ?>
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

                [
                    'attribute' => 'username',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'score-warn'],
                    'value' => function ($searchModel) {
                        $user_sex = Patient::$getSex[$searchModel->sex];
                        $dateDiffage = Patient::dateDiffage($searchModel->birthday, time());
                        return Html::encode($searchModel->username) . '(' . $user_sex . ' ' . $dateDiffage . ')' . Patient::getUserScore($searchModel->pain_score, $searchModel->fall_score);
                    },
                ],
                [
                    'attribute' => 'patient_number'
                ],
                [
                    'attribute' => 'room_name',
                ],
                [
                    'attribute' => 'inspect_name',
                    'headerOptions' => ['class' => 'col-xs-3 col-sm-3 col-md-3']
                ],
                [
                    'attribute' => 'doctor_name',
                ],
                [
                    'attribute' => 'type_description',
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{on-inspect}{under-inspect}{complete}{cancel-inspect}',
                    'headerOptions' => ['style' => 'width:360px'],
                    'buttons' => [
                        'on-inspect' => function ($url, $model, $key) use($inspectStatusCount) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/on-inspect', $this->params['permList'])) {
                                return false;
                            }
                            $inspectNum = $inspectStatusCount[$model->record_id][3];
                            $btn_title = "待检验" . $inspectNum;
                            $options = [
                                'class' => 'btn btn-delete',
                                'role' => 'modal-remote',
                                'data-toggle' => 'tooltip',
                            ];
                            if ($inspectNum) {
                                return Html::a($btn_title, Url::to(['@pharmacyIndexOnInspect', 'id' => $model->record_id]), $options);
                            } else {
                                return '';
                            }
                        },
                                'under-inspect' => function ($url, $model, $key) use($inspectStatusCount) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/under-inspect', $this->params['permList'])) {
                                return false;
                            }
                            $inspectNum = $inspectStatusCount[$model->record_id][2];
                            $btn_title = "检验中" . $inspectNum;
                            $options = [
                                'class' => 'btn btn-success',
                                'role' => 'modal-update',
                                'data-toggle' => 'tooltip',
                                'data-pjax' => 0,
                            ];
                            if ($inspectNum) {
                                return Html::a($btn_title, Url::to(['@inspectIndexUnderInspect', 'id' => $model->record_id]), $options);
                            } else {
                                return '';
                            }
                        },
                                'complete' => function ($url, $model, $key) use($inspectStatusCount) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/complete', $this->params['permList'])) {
                                return false;
                            }
                            $inspectNum = $inspectStatusCount[$model->record_id][1];
                            $btn_title = "已完成" . $inspectNum;
                            $options = [
                                'class' => 'btn btn-default',
                                'role' => 'modal-update',
                                'data-toggle' => 'tooltip',
                                'data-pjax' => 0,
                            ];
                            if ($inspectNum) {
                                return Html::a($btn_title, Url::to(['@inspectIndexComplete', 'id' => $model->record_id]), $options);
                            } else {
                                return '';
                            }
                        },
                        'cancel-inspect' => function ($url, $model, $key) use($inspectStatusCount) {
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/complete', $this->params['permList'])) {
                            return false;
                        }
                        $inspectNum = $inspectStatusCount[$model->record_id][4];
                        $btn_title = "已取消" . $inspectNum;
                        $options = [
                            'class' => 'btn btn-back',
                            'role' => 'modal-update',
                            'data-toggle' => 'tooltip',
                            'data-pjax' => 0,
                        ];
                        if ($inspectNum) {
                            return Html::a($btn_title, Url::to(['@inspectIndexComplete', 'id' => $model->record_id,'type' => 1]), $options);
                        } else {
                            return '';
                        }
                        }
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
        <?php AppAsset::addScript($this, '@web/public/js/lib/JsBarcode.all.min.js'); ?>
        <script type="text/javascript">
            $('.wrapper').after('<div id="specimen-print-container" class="common-print-container specimen-print-container" style="display:none;"> </div>');
        </script>
        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
