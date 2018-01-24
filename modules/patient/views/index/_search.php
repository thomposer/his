<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\modules\patient\models\Patient;
use app\modules\spot\models\OrganizationType;
use yii\helpers\ArrayHelper;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use app\modules\spot\models\Spot;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\patient\models\search\PatientSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
$params = Yii::$app->request->queryParams;
$type = $params['type'];
$page = $params['page'];
$status = $params['status']?1:0;
$typeList = ArrayHelper::map(OrganizationType::getSpotType(),'id','name');
$secondDepartmentList = ArrayHelper::map(SecondDepartment::getList('1 != 0'), 'id', 'name');
$doctorList = ArrayHelper::map(User::getParentSpotDoctorList(),'id','username');
$spotList = ArrayHelper::map(Spot::getSpotList(), 'id', 'spot_name');
?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/patient/search.css') ?>
<?php $this->endBlock() ?>
<div class="patient-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['index','type' => $type,'page' => $page,'status' =>$status],
        'options' =>  ['class' => 'form-horizontal search-form patient-history-search ','data-pjax' => true],
        'fieldConfig' => [
            'template' => "<div class='labelWidth text-right'>{label}</div><div class='col-xs-9 col-sm-9'>{input}</div><div class='clear'></div> ",
        ]
    ]); ?>
    <div class="row">
        <div class="col-md-1">
            <label class="patient-info-search-title">用户信息</label>
        </div>
        <div class="col-md-11">
            <div class="row patient-history-row col-md-12">
                <div class="col-md-4">
                    <?= $form->field($model, 'patient_number')->textInput(['placeholder' => '请输入'.$attributeLabels['patient_number']]) ?>
                </div>    
                <div class="col-md-4">
                    <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入'.$attributeLabels['username']] ) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'iphone')->textInput(['placeholder' => '请输入'.$attributeLabels['iphone']] ) ?>
                </div>
            </div>
            <div class="row patient-history-row col-md-12">
                <div class="col-md-4">
                    <?= $form->field($model, 'card')->textInput(['placeholder' => '请输入'.$attributeLabels['card']] ) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'sex')->dropDownList(Patient::$getSex,['prompt'=>'请选择']) ?>
                </div>
            </div>
            <div class="row patient-history-row col-md-12">
                <div class="col-md-8">
                    <?= $form->field($model, 'start_birthday')->widget(DatePicker::className(), [
                            'addon' => false,
                            'template' => '{input}',
                            'language' => 'zh-CN',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd',
                            ],
                            'options' =>    [
                                'placeholder' => '请选择开始时间',
                            ],
                        ]) 
                    ?>
                    <?= $form->field($model, 'end_birthday')->widget(DatePicker::className(), [
                            'addon' => false,
                            'template' => '{input}',
                            'language' => 'zh-CN',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd',
                            ],
                            'options' =>    [
                                'placeholder' => '请选择结束时间',
                            ],
                        ])->label('-') 
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row"><div class="patient-info-line"></div></div>
    <div class="row">
        <div class="col-md-1">
            <label class="patient-info-search-title">就诊信息</label>
        </div>
        <div class="col-md-11">
            <div class="row patient-history-row col-md-12">
                <div class="col-md-4">
                    <?= $form->field($model, 'record_id')->textInput(['placeholder' => '请输入'.$attributeLabels['record_id']]) ?>
                </div>    
                <div class="col-md-4">
                    <?= $form->field($model, 'record_spot_id')->dropDownList($spotList,['prompt'=>'请选择']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'second_department_id')->dropDownList($secondDepartmentList,['prompt'=>'请选择']) ?>
                </div>
            </div>
            <div class="row patient-history-row col-md-12">
                <div class="col-md-4">
                    <?= $form->field($model, 'doctor_id')->dropDownList($doctorList,['prompt'=>'请选择']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'type')->dropDownList($typeList,['prompt'=>'请选择']) ?>
                </div>
            </div>
            <div class="row patient-history-row col-md-12">
                <div class="col-md-8">
                    <?= $form->field($model, 'record_start_time')->widget(DatePicker::className(), [
                            'addon' => false,
                            'template' => '{input}',
                            'language' => 'zh-CN',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd',
                            ],
                            'options' =>    [
                                'placeholder' => '请选择开始时间',
                            ],
                        ])
                    ?>
                    <?= $form->field($model, 'record_end_time')->widget(DatePicker::className(), [
                            'addon' => false,
                            'template' => '{input}',
                            'language' => 'zh-CN',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd',
                            ],
                            'options' =>    [
                                'placeholder' => '请选择结束时间',
                            ],
                        ])->label('-') 
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            
        </div>
        <div class="col-md-10"  style="margin-top: 10px;">
            <div class= 'form-group'>
                <?= Html::submitButton('搜索', ['class' => 'btn btn-default patient-history-btn btn-search']) ?>
                <?= Html::button('重置', ['class' => 'btn btn-default patient-history-btn btn-reset']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php $this->registerJs("
    var state = $('.senior-search').attr('aria-expanded');
    if(state == 'true'){
        $('.senior-search').find('.fa').removeClass('fa-angle-down');
        $('.senior-search').find('.fa').addClass('fa-angle-up');
    }else{
        $('.senior-search').find('.fa').removeClass('fa-angle-up');
        $('.senior-search').find('.fa').addClass('fa-angle-down');  
    }
        $('.senior-search').unbind('click').click(function(){
            var state = $(this).attr('aria-expanded');
            if(state == 'false' || state == undefined){
                $(this).find('.fa').removeClass('fa-angle-down');
                $(this).find('.fa').addClass('fa-angle-up');
                $('.patient-history-search input[name=\"status\"]').val(1);
            }else{
                $(this).find('.fa').addClass('fa-angle-down');
                $(this).find('.fa').removeClass('fa-angle-up');
                $('.patient-history-search input[name=\"status\"]').val(0);
            }
        });
        $('.btn-reset').unbind('click').click(function(){
            $('#patient-history-search-from input:text').val('');
            $('#patient-history-search-from select').val('');
        })
        ")
?>





