<?php

use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\patient\models\PatientRecord;
use app\modules\patient\models\Patient;
use app\common\Common;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\modules\make_appointment\models\Appointment;
use app\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/check/common.css');
AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css');
$count = count($dataProvider->getModels());
$this->registerCss('
    @media (min-width: 992px){
        .modal-lg {
            width: 1000px;
        }
    }    
');
?>
<?php Pjax::begin(['id' => 'appointment-detail-' . $param, 'enablePushState' => false]); ?>
<div class = 'row row-search-margin no-padding-right'>
    <?php echo $this->render('_appointmentPopupSearch', ['secondDepartmentInfo' => $secondDepartmentInfo, 'doctorInfo'=>$doctorInfo, 'spotTypeList'=>$spotTypeList]); ?>
</div>
<div class="charge-record-form">
    <div class = 'cost-bg'>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive'],
                'tableOptions' => ['class' => 'table table-hover  add-table-border'],
                'headerRowOptions' => ['class' => 'header'],
                'layout' => '{items}<div class="text-right">{pager}</div>',
                'pager' => [
                    //'options'=>['class'=>'hidden']//关闭自带分页

                    'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                    'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                    'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                    'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
                ],
                'columns' => [
                    [
                        'attribute' => 'username',
                        'value' => function($searchModel, $key, $index, $grid)use($cardInfo, $count) {
                            $birth = Patient::dateDiffage($searchModel->birthday, time());
                            $type = (($index == ($count - 1)) || ($index % 20 == 0)) ? 1 : 2;
                            $index == 0 && $type = 2;
                            return Html::encode($searchModel->username) . '( ' . Patient::$getSex[$searchModel->sex] . ' ' . $birth . ' )' . Patient::getFirstRecord($searchModel->firstRecord) . Patient::getUserVipInfo($cardInfo[$searchModel->iphone], 2, $type);
                        },
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    ],
                    [
                        'attribute' => 'iphone',
                        'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    ],
                    [
                        'attribute' => 'type_description',
                        'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    ],
                    [
                        'attribute' => 'time',
                        'value' => function($searchModel) {
                            return date("Y-m-d H:i", $searchModel->time);
                        },
                        'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    ],
                    [
                        'attribute' => 'doctorName',
                        'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    ],
                    [
                        'attribute' => 'illness_description',
                        'value' => function($searchModel) {
                        return Common::strTransfer($searchModel->illness_description, 16);
                        },
//                         'visible' => ($entrance == 2) ? 1 : 0,
                        'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    ],
                    [
                        'attribute' => 'remarks',
                        'visible' => ($entrance == 1) ? 1 : 0,
                        'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    ],
                    [
                        'attribute' => 'appointment_origin',
                        'value' => function($searchModel) {
                            return Appointment::$getAppointmentOrigin[$searchModel->appointment_origin];
                        },
                        'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    ],
                    [
                        'attribute' => 'appointmentOperator',
                        'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2','style' => 'width:100px;'],
                    ],
                    [
                        'class' => 'app\common\component\ActionTextColumn',
                        'template' => '{view}{update}{delete}',
                        'headerOptions' => ['style' => 'width:140px'],
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                if ((!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@make_appointmentAppointmentView'), $this->params['permList'])) ) {
                                    return false;
                                }
                                $options = array_merge([
                                    'data-pjax' => '0',
                                    'target' => '_blank',
                                ]);
                                return Html::a('查看', Url::to(['@make_appointmentAppointmentView', 'id' => $model->id]), $options);
                            },
                            'update' => function ($url, $model, $key)use($entrance) {
                                if ($model->status != 1 || ($model->status ==1 && strtotime(date("Y-m-d",$model->time))+86400 <= strtotime(date('Y-m-d')) ) || $entrance == 2) {
                                    return false;
                                }
                                $options = array_merge([
                                    'data-pjax' => '0',
                                    'target' => '_blank',
                                ]);
                                return Html::a('修改', Url::to(['@make_appointmentAppointmentUpdate', 'id' => $model->id]), $options);
                            },
                            'delete' => function($url,$model,$key) use ($time,$headerType,$doctorId,$entrance){
                                if($model->status != 1 || ($model->status ==1 && strtotime(date("Y-m-d",$model->time))+86400 <= strtotime(date('Y-m-d'))) || $entrance == 2){
                                    return false;
                                }
                                $options = [
                                    'data-request-method'=>'post',
                                    'role'=>'modal-remote',
                                    
                                ];
                                return Html::a('关闭',['@make_appointmentAppointmentDelete','id' => $model->record_id,'time' =>$time,'doctor_id' => $doctorId,'header_type' => $headerType,'entrance' =>$entrance],$options);

                            }
                        ]
                    ],
                ],
            ])
            ?>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
