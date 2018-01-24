<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\modules\patient\models\PatientRecord;
use app\modules\pharmacy\models\PharmacyRecord;
use app\modules\patient\models\Patient;
use yii\helpers\Url;
use dosamigos\datepicker\DatePickerAsset;
use dosamigos\datepicker\DatePickerLanguageAsset;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\grid\GridGroupAsset;
GridGroupAsset::register($this);
CrudAsset::register($this);
DatePickerAsset::register($this);
DatePickerLanguageAsset::register($this)->js[] = 'bootstrap-datepicker.'.Yii::$app->language.'.min.js';
/* @var $this yii\web\View */
/* @var $searchModel app\modules\outpatient\models\search\OutpatientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '药房管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$params = [
    'searchName' => 'PharmacyRecordSearch',
    'statusName' => 'status',
    'buttons' => [
        [
            'title' => '全部',
            'statusCode' => 0,
        ],
        [
            'title' => '待发药',
            'statusCode' => 3,
        ],
        [
            'title' => '待退药',
            'statusCode' => 4,
        ],
        
        [
            'title' => '已发药',
            'statusCode' => 1,
        ],
        [
            'title' => '已退药',
            'statusCode' => 5,
        ]
    ]
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/highRisk.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/pharmacy/pharmacy.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
<div class="recipe-record-index col-xs-12">
    <?php // echo $this->render('_top_tab.php'); ?>
    <div class = "box delete_gap">
            <div class = 'row search-margin'>
                <div class = 'col-sm-6 col-md-6'>
                    <?= $this->render(Yii::getAlias('@searchStatusCommon'),$params) ?>
                </div>
                <div class = 'col-sm-6 col-md-6'>
                    <?= $this->render('_search', ['model' => $searchModel]); ?>
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
//                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'username',
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'score-warn'],
                    'headerOptions' => ['class' => 'col-md-2'],
                    'value' => function ($model) {
                        $user_sex = Patient::$getSex[$model->sex];
                        $dateDiffage = Patient::dateDiffage($model->birthday, time());
                        $isTexu = $model->create_time < mktime(0,0,0,date("m"),date("d"),date("Y")) ? true : false;
                        $texu = $isTexu ? '<span class="text-red-mine"> (特需) </span>' : '';
                        $userName = Html::encode($model->username);
                        return $userName . '(' . $user_sex . ' ' . $dateDiffage . ')'.$texu.Patient::getUserScore($model->pain_score,$model->fall_score);
                    },
                ],
                [
                    'attribute' => 'recipe_name',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'format' => 'raw',
                    'value' => function ($model)use($info) {
                        return implode(',',$info['name'][$model->record_id]);
                    }
                ],
                [
                    'attribute' => 'doctor_name',
                    'headerOptions' => ['class' => 'col-md-1'],
                ],
                [

                    'attribute' => 'pharmacyRecordTime',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'format' => 'datetime'
                ],
                [
                    'attribute' => 'type_description',
                    'headerOptions' => ['class' => 'col-md-1'],
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{dispense}{prebatch}{complete}{endbatch}',
                    'headerOptions' => ['style' => 'width:360px'],
                    'buttons' => [
                        'dispense' => function ($url, $model, $key)use($info) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/dispense', $this->params['permList'])) {
                                return false;
                            }
                            $dispenseNum = $info['status'][$model->record_id][3];
                            $btn_title = "待发药" . $dispenseNum;
                            $options = [
                                'class' => 'btn btn-delete',
                                'data-toggle' => 'modal',
                                'data-pjax' => 0
                            ];
                            if ($dispenseNum) {
                                return Html::a($btn_title, Url::to(['@pharmacyIndexDispense', 'id' => $model->record_id]), $options);
                             } else {
                                    return '';
                                }
                       },
                       'prebatch' => function ($url, $model, $key)use($info) {
                           if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/prebatch', $this->params['permList'])) {
                               return false;
                           }
                           $dispenseNum = $info['status'][$model->record_id][4];
                           $btn_title = "待退药" . $dispenseNum;
                           $options = [
                               'class' => 'btn btn-delete',
                               'data-toggle' => 'modal',
                               'data-pjax' => 0
                           ];
                           if ($dispenseNum) {
                               return Html::a($btn_title, Url::to(['@pharmacyIndexPrebatch', 'id' => $model->record_id]), $options);
                           } else {
                               return '';
                           }
                       },
                       'endbatch' => function ($url, $model, $key)use($info) {
                           if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/endbatch', $this->params['permList'])) {
                               return false;
                           }
                           $options = [
                               'class' => 'btn btn-default',
                               'data-pjax' => 0
                           ];
                           $dispenseNum = $info['status'][$model->record_id][5];
                           $btn_title = "已退药" . $dispenseNum;
                           if ($dispenseNum) {
                               return Html::a($btn_title, Url::to(['@pharmacyIndexEndbatch', 'id' => $model->record_id]), $options);
                           } else {
                               return '';
                           }
                       },
                       'complete' => function ($url, $model, $key)use($info) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/complete', $this->params['permList'])) {
                                 return false;
                            }
                            $options = [
                                'class' => 'btn btn-default',
                                'data-pjax' => 0
                            ];
                            $dispenseNum = $info['status'][$model->record_id][1];
                            $btn_title = "已发药" . $dispenseNum;
                            if ($dispenseNum) {
                                return Html::a($btn_title, Url::to(['@pharmacyIndexComplete', 'id' => $model->record_id]), $options);
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
        </div>
        <?php Pjax::end();?>
        <?php $this->endBlock(); ?>
        <?php $this->beginBlock('renderJs'); ?>

        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
