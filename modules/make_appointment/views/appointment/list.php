<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use kartik\grid\GridView;
use app\modules\make_appointment\models\Appointment;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use yii\widgets\Pjax;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\user\models\User;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\make_appointment\models\search\AppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '预约';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';

$tabArray = array();
$tabArray[] = ['title' => '预约管理', 'url' => Url::to(['@make_appointmentAppointmentAppointmentDetail', 'type' => 3]), 'type' => 3, 'icon_img' => $public_img_path . 'make_appointment/tab_order.png'];
if (in_array(1, $appointment_type)) {
    $tabArray[] = ['title' => '医生预约时间设置', 'url' => Url::to(['@make_appointmentAppointmentTimeConfig', 'type' => 4]), 'type' => 4, 'icon_img' => $public_img_path . 'make_appointment/tab_setting.png'];
}
if (in_array(2, $appointment_type)) {
    $tabArray[] = ['title' => '科室预约设置', 'url' => Url::to(['@make_appointmentAppointmentRoomConfig', 'type' => 5]), 'type' => 5, 'icon_img' => $public_img_path . 'make_appointment/tab_setting.png'];
}
$tabData = [
    'titleData' => $tabArray,
    'activeData' => [
        'type' => 3
    ]
];
$buttonArray = array();

$buttonArray[] = ['title' => '人数统计', 'statusCode' => 0, 'url' => Url::to(['@make_appointmentAppointmentAppointmentDetail'])];
if (in_array(1, $appointment_type)) {
    $buttonArray[] = ['title' => '患者列表', 'statusCode' => 1, 'url' => Url::to(['@make_appointmentAppointmentList'])];
//     $buttonArray[]=   ['title' => '预约详情', 'statusCode' => 1, 'url' => Url::to(['@make_appointmentAppointmentDetail'])];
}
if (in_array(2, $appointment_type)) {
    $buttonArray[] = ['title' => '患者列表', 'statusCode' => 1, 'url' => Url::to(['@make_appointmentAppointmentList'])];
}

$params = [
    'searchName' => 'appointment',
    'statusName' => 'type',
    'buttons' => $buttonArray,
];

?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/easyhincalendar/easyhincalendar.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/search.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="appointment-index col-xs-12">

    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
    <div class="box delete_gap">
        <div class='row search-margin'>
            <div class='col-sm-12 col-md-12'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/create', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增预约", ['create', 'return' => 'list'], ['class' => 'btn btn-default font-body2', 'data-pjax' => 0]) ?>
                <?php endif ?>
                <?= $this->render(Yii::getAlias('@searchStatusSkip'), $params) ?>
            </div>
        </div>
        <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
        <div class='row row-search-margin'>
            <?php echo $this->render('_search', ['model' => $searchModel, 'secondDepartmentInfo' => $secondDepartmentInfo, 'doctorInfo' => $doctorInfo,'getAppointmentOperator'=>$getAppointmentOperator, 'spotTypeList'=>$spotTypeList]); ?>
        </div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border'],
            'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
            'striped' => false,
            'bordered' => false,
            'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            /*'filterModel' => $searchModel,*/
            'columns' => [
                //病历号暂时隐藏
//            [
//                'attribute' => 'id',
//                'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
//            ],
                [
                    'attribute' => 'username',
                    'value' => function ($searchModel) use ($cardInfo) {
                        $birth = Patient::dateDiffage($searchModel->birthday, time());
                        $text = Html::encode($searchModel->username) . '( ' . Patient::$getSex[$searchModel->sex] . ' ' . $birth . ' )' . Patient::getFirstRecord($searchModel->firstRecord) . Patient::getUserVipInfo($cardInfo[$searchModel->iphone]);
                        return $text;
                    },
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'iphone',
                    'value' => function ($searchModel) {
                        return $searchModel->iphone;

                    },
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'departmentDoctorName',
                    'value' => function ($searchModel) {
                        if ($searchModel->doctorName) {
                            return $searchModel->doctorName . ' ─ ' . $searchModel->type_description;
                        }
                        return $searchModel->type_description;
                    },
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'illness_description',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'remarks',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'time',
                    'format' => 'datetime',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($searchModel) {
                        if ($searchModel->status == 1 && strtotime(date("Y-m-d", $searchModel->time)) + 86400 <= strtotime(date('Y-m-d'))) {
                            return PatientRecord::$getStatus[8];
                        }
                        return PatientRecord::$getStatus[$searchModel->status];
                    },
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                ],
                [
                    'attribute' => 'appointment_operator',
                    'value' => function ($searchModel) {
                        return User::getUserInfo($searchModel->appointment_operator, ['username'])['username'];
                    },
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
                    'template' => '{view}{update}{delete}',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                    'buttons' => [

                        'update' => function ($url, $model, $key) {
                            if ($model->status != 1 || ($model->status == 1 && strtotime(date("Y-m-d", $model->time)) + 86400 <= strtotime(date('Y-m-d'))) || !isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@make_appointmentAppointmentUpdate'), $this->params['permList'])) {
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', '修改'),
                                'aria-label' => Yii::t('yii', '修改'),
                                'data-pjax' => '0',
                            ]);
                            return Html::a('修改', $url, $options);
                        },
                        'delete' => function ($url, $model, $key) {
                            if ($model->status != 1 || ($model->status == 1 && strtotime(date("Y-m-d", $model->time)) + 86400 <= strtotime(date('Y-m-d'))) || !isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@make_appointmentAppointmentDelete'), $this->params['permList'])) {
                                return false;
                            }
                            //修改取消预约二次确认为弹窗
                            $options = [
//                           'data-confirm'=>false,
//                           'data-method'=>false,
                                'data-request-method' => 'post',
//                           'role'=>'modal-remote',
//                           'data-toggle'=>'tooltip',
//                           'data-confirm-title'=>'系统提示',
//                           'data-delete' => false,
//                           'data-confirm-message'=>Yii::t('yii', '你确定取消预约吗?'),
                                'role' => 'modal-remote'
                            ];
                            return Html::a('关闭', ['@make_appointmentAppointmentDelete', 'id' => $model->record_id, 'entrance' => 2], $options);

                        }
                    ],
                ],
            ],
        ]); ?>

        <?php $this->registerJs("
//        var state = $(this).attr('state');
if(localStorage.state){
        if(localStorage.state == 2){
            $('.field-appointmentsearch-type').show();
            $('.field-appointmentsearch-status').show();
            $('.field-appointmentsearch-appointment_operator').show();
            $('.appointment-search').find('.search-default').attr('style','height:50px;');
            $('.appointment-search').find('.form-group').removeClass('lower');
            $('.more-word').next().attr('class','fa fa-caret-up');
        }else {
            $('.field-appointmentsearch-type').hide();
            $('.field-appointmentsearch-status').hide();
            $('.field-appointmentsearch-appointment_operator').hide();
            $('.appointment-search').find('.search-default').attr('style','');
            $('.appointment-search').find('.form-group').addClass('lower');
            $('.more-word').next().attr('class','fa fa-caret-down');
        }
    $('.more-word').unbind('click').click(function () {
        var state = $(this).attr('state');
        if(localStorage.state == 1){
            $('.field-appointmentsearch-type').show();
            $('.field-appointmentsearch-status').show();
            $('.field-appointmentsearch-appointment_operator').show();
            localStorage.state = 2;
            $('.appointment-search').find('.search-default').attr('style','height:50px;');
            $('.appointment-search').find('.form-group').removeClass('lower');
            $(this).next().attr('class','fa fa-caret-up');
        }else {
            $('.field-appointmentsearch-type').hide();
            $('.field-appointmentsearch-status').hide();
            $('.field-appointmentsearch-appointment_operator').hide();
            localStorage.state = 1;
            $('.appointment-search').find('.search-default').attr('style','');
            $('.appointment-search').find('.form-group').addClass('lower');
            $(this).next().attr('class','fa fa-caret-down');
        }
    })
    }else{
        localStorage.state = 1;
    };
") ?>
        <?php Pjax::end(); ?>
    </div>
</div>
<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
