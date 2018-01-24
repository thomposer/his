<?php
use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\make_appointment\models\Appointment;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\ActiveForm;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\make_appointment\models\search\AppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '预约';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$versionNumber = Yii::getAlias("@versionNumber");
$tabArray=array();
$tabArray[]=['title' => '预约管理', 'url' => Url::to(['@make_appointmentAppointmentAppointmentDetail', 'type' => 3]), 'type' => 3,'icon_img' => $public_img_path . 'make_appointment/tab_order.png'];
if(in_array(1, $appointment_type)){
    $tabArray[]=['title' => '医生预约设置', 'url' => Url::to(['@make_appointmentAppointmenDoctortConfig', 'type' => 4]), 'type' => 4,'icon_img' => $public_img_path . 'make_appointment/tab_setting.png'];
}
if(in_array(2, $appointment_type)){
    $tabArray[]=['title' => '科室预约设置', 'url' => Url::to(['@make_appointmentAppointmentRoomConfig', 'type' => 5]), 'type' => 5,'icon_img' => $public_img_path . 'make_appointment/tab_setting.png'];
}

$tabData = [
    'titleData' => $tabArray,
    'activeData' => [
        'type' => 4
    ]
];
$params = [
    'searchName' => 'appointment',
    'statusName' => 'type',
    'buttons' => [
        [
            'title' => '预约权限设置',
            'statusCode' => 0,
            'url' => Url::to(['@make_appointmentAppointmentDoctorConfig']),
            'hasDot'=>false
        ],
        [
            'title' => '预约时间设置',
            'statusCode' => 1,
            'url' => Url::to(['@make_appointmentAppointmentTimeConfig']),
            'hasDot'=>false
        ]
    ]
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php AppAsset::addCss($this, '@web/public/plugins/easyhincalendar/easyhincalendar.css')?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/appointmentConfig.css')?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="appointment-index col-xs-12">
    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>

    <div class = 'row search-margin padding-left'>
        <div class = 'col-sm-4 col-md-4'>
            <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/doctor-config', $this->params['permList'])):?>
                <!--           //加载日历表格button-->
                <?= $this->render(Yii::getAlias('@searchStatusSkip'),$params) ?>
            <?php endif?>
        </div>
    </div>

        <div class = "box box-form">
        <?php
        $form = ActiveForm::begin([
            'id' => 'cure-record',
            'options' => ['data' => ['pjax' => true]],
        ])
        ?>
        <?=
        GridView::widget([
            'dataProvider' => $doctorInfo,
            'options' => ['class' => 'grid-view table-responsive add-table-padding doctor-form'],
            'tableOptions' => ['class' => 'table table-hover table-border'],
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
                        'attribute' => 'doctor_name',
                    ],
                    [
                        'attribute' => 'department_name',
                    ],
                [
                    'attribute' => 'appointment_status',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        $html = Html::encode($model->status);
                        $hiddenHtml="<input type='hidden' name='User[id]' class='checkitemid' value='$model->doctor_id'>";
                        if($html==1){
                            $text  = '<span class="remark" name="1">是</span>';
                            $text .=$hiddenHtml;
                        }elseif($html==2){
                            $text = '<span class="remark" name="2">否</span>';
                            $text .=$hiddenHtml;
                        }
                        return $text;
                    }
                ],
            ],
        ]);
        ?>

        <div class="form-group doctor-confing">
                <?= Html::button('编辑', ['class' => 'btn btn-default btn-form update-config']) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>

<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var dispensingUrl = '<?= Yii::$app->getRequest()->absoluteUrl ?>';
    require(["<?= $baseUrl ?>" + "/public/js/make_appointment/doctorConfig.js?v="+'<?= $versionNumber ?>'], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
