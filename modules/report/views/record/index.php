<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\patient\models\Patient;
use app\modules\report\models\Report;
use app\modules\make_appointment\models\Appointment;
use app\modules\patient\models\PatientRecord;
use yii\helpers\Url;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\report\models\search\PatientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '报到';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$versionNumber = Yii::getAlias("@versionNumber");
$tabData = [
    'titleData' => [
        ['title' => '今日预约待报到', 'url' => Url::to(['@reportRecordAppointment']), 'icon_img' => $public_img_path . '/tab/tab_paiban.png'],
        ['title' => '报到记录', 'url' => Url::to(['@reportRecordIndex']), 'icon_img' => $public_img_path . '/tab/tab_paiban.png']
    ],
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/check/print.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="patient-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
    <div class = "box delete_gap">
        <div class = 'row no-gap'>
            <div class = 'col-sm-2 col-md-2'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/create', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", ['create', 'url' => 'index'], ['class' => 'btn btn-default font-body2', 'data-pjax' => 0]) ?>
                <?php endif ?>
            </div>
            <div class = 'col-sm-10 col-md-10'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
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
                    'attribute' => 'username',
                    'format' => 'raw',
                    'value' => function ($searchModel)use($cardInfo) {
                        $user_sex = Patient::$getSex[$searchModel->sex];
                        $dateDiffage = Patient::dateDiffage($searchModel->birthday, time());
                        $firstRecord = Patient::getFirstRecord($searchModel->firstRecord);
                        return '　' . Html::encode($searchModel->username) . '(' . $user_sex . ' ' . $dateDiffage . ')' . Patient::getUserVipInfo($cardInfo[$searchModel->iphone]) . $firstRecord;
                    }
                ],
                [
                    'attribute' => 'iphone',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2']
                ],
                [
                    'attribute' => 'birthday',
                    'value' => function ($searchModel) {
                        if ($searchModel->birthday) {
                            return date('Y-m-d H:i', $searchModel->birthday);
                        }
                        return '';
                    },
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'type_description',
                ],
                [
                    'attribute' => 'status',
                    'value' => function($searchModal) {
                        return PatientRecord::$getStatus[$searchModal->status];
                    }
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
                    'template' => '{view}{update}{delete}{print}{close}',
                    'buttons' => [
                        'view' => function($url, $model, $key) {
                            if ((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/view', $this->params['permList']))) {
                                return false;
                            }
                            $options = array_merge([
                                'data-pjax' => '0',
                            ]);
                            return Html::a('查看', ['@reportRecordView', 'id' => $key], $options);
                        },
                                'update' => function($url, $model, $key) {
                            if ((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/update', $this->params['permList'])) || $model->status != 2) {
                                return false;
                            }
                            $options = array_merge([
                                'data-pjax' => '0',
                            ]);
                            return Html::a('修改', $url, $options);
                        },
                                'delete' => function($url, $model, $key) {
                            if ((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/delete', $this->params['permList'])) || $model->status != 2) {
                                return false;
                            }
                            if ($model->status != 1) {
                                return false;
                            }
                            $options = array_merge([
                                'data-confirm' => false,
                                'data-method' => false,
                                'data-request-method' => 'post',
                                'role' => 'modal-remote',
                                'data-delete' => false,
                                'data-confirm-message' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            ]);
                            return Html::a('删除', ['@reportRecordDelete', 'id' => $key, 'record_id' => $model->record_id], $options);
                        },
                                'print' => function($url, $model, $key) {
                            $options = [
                                'style' => 'display: inline-block;',
                                'user_name' => $model->username,
                                'sex' => $model->sex,
                                'phone' => $model->iphone,
                                'birthday' => date('Y-m-d', $model->birthday),
                                'patient_number' => $model->patient_number,
                            ];
                            return Html::a('<span class=" print_label">标签</span>', 'javascript:void(0)', $options);
                        },
                                'close' => function($url, $model, $key) {
                            if ((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/close', $this->params['permList'])) || in_array($model->status, [4, 5]) || $model->status == 9) {
                                return false;
                            }
                            $options = [
                                'data-confirm' => false,
                                'data-method' => false,
                                'data-request-method' => 'post',
                                'role' => 'modal-remote',
                                'data-delete' => false,
                                'data-confirm-title' => '系统提示',
                                'data-confirm-message' => Yii::t('yii', '确定关闭吗？'),
                            ];
                            return Html::a('关闭', ['@reportRecordClose', 'id' => $key, 'record_id' => $model->record_id], $options);
                        },
                            ],
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>

        <div id='print-show-none'>
            <div id='print-view'>
        <!--        <div><span class="print-label-l ver-bottom">Name:</span><span class="print_label_r text-overflow">哈哈哈很长很长很长很长很长很长很长很长</span></div>
                <div><span class="print-label-l">Sex:</span><span class="print_label_r">Female 女</span></div>
                <div><span class="print-label-l">DOB:</span><span class="print_label_r">2014-02-12</span></div>
                <div><span class="print-label-l">Tel:</span><span class="print_label_r">18576617065</span></div>
                <div><span class="print-label-l">MRN:</span><span class="print_label_r">123456789012</span></div>-->
            </div>
        </div>
        <?php $this->registerJs("
            var baseUrl = '$baseUrl';
            var versionNumber = '$versionNumber';
            require([baseUrl+'/public/js/report/record/index.js?v='+versionNumber],function(main){
		        main.init();
	        })
        ") ?>
        <?php Pjax::end(); ?>

        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
