<?php
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use app\modules\triage\models\TriageInfo;

$css = <<<CSS
    .blood_type_supplement{
        height: 50px;
        line-height: 83px;
    }
    .add-padding-left{
        padding-left: 7.5px;
    }
    .normal-BMI{
        line-height: 40px;
        color:#CACFD8;
    }
    .higher-BMI{
        color:#445064;
    }
CSS;
$this->registerCss($css);
/**
 * 体征测量
 */

?>

    <div class="tab-pane active" id="ptab1" data-type="1">
<?php $form = ActiveForm::begin(['id' => 'j_tabForm_1', 'action' => $action]); ?>
<?= $form->field($model, 'record_id')->input('hidden')->label(false) ?>
<?php $model->modal_tab = 1; ?>
<?= $form->field($model, 'modal_tab')->input('hidden')->label(false) ?>
    <!--<form class="form-horizontal" id="j_tab1Form" action="" novalidate="novalidate">-->
    <div class='row'>
        <div class='col-sm-6'>
            <?= $form->field($model, 'treatment_type')->dropDownList($model::$treatment_type, ['prompt' => '请选择']) ?>
        </div>
        <?php if (5 == $model->treatment_type): ?>
        <div class='col-sm-6 treatment_div'>
            <?php else: ?>
            <div class='col-sm-6 treatment_div' style="display:none;">
                <?php endif; ?>
                <?= $form->field($model, 'treatment')->textInput(['placeholder' => '请输入就诊方式', 'style' => 'margin-top: 5px;']) ?>

            </div>
        </div>
        <div class='row'>
            <div class='col-sm-6'>
                <?= $form->field($model, 'heightcm')->input('text', ['placeholder' => '身高的值精确到小数点后一位']) ?>
            </div>
            <div class='col-sm-3'>
                <?= $form->field($model, 'weightkg')->input('text', ['placeholder' => '体重的值精确到小数点后两位']) ?>
            </div>
            <div class="add-padding-left col-sm-3">
                <div class="form-group">
                    <label for=""></label>
                    <div id="bmiVal" class="normal-BMI">BMI值： -</div>
                </div>
            </div>
        </div>

        <div class='row'>

            <div class='col-sm-6'>
                <?= $form->field($model, 'head_circumference')->textInput(['placeholder' => '头围的值必须不小于30并且精确到小数点后一位']) ?>
            </div>
            <div class='col-sm-3'>
                <?= $form->field($model, 'bloodtype')->dropDownList($model::$bloodtype, ['prompt' => '请选择(ABO系统)']) ?>
            </div>
            <div class='col-sm-3 blood_type_supplement add-padding-left'>
                <?= $form->field($model, 'blood_type_supplement')->checkboxList(TriageInfo::$bloodTypeSupplement)->label(false) ?>
            </div>
        </div>

        <div class='row'>
            <div class='col-sm-3'>
                <?= $form->field($model, 'temperature_type')->dropDownList($model::$temperature_type) ?>
            </div>
            <div class='col-sm-3 add-padding-left'>
                <?= $form->field($model, 'temperature')->input('text', ['placeholder'=>'体温的值精确到小数点后一位','maxlength' => true])->label('　') ?>
            </div>
            <div class='col-sm-6'>
                <?= $form->field($model, 'breathing')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class='row'>
            <div class='col-sm-6'>
                <?= $form->field($model, 'pulse')->textInput() ?>
            </div>

            <div class='col-sm-3'>
                <?= $form->field($model, 'shrinkpressure')->textInput(['maxlength' => true, 'placeholder' => '收缩压（mmHg）'])->label('血压（mmHg）') ?>
            </div>
            <div class='col-sm-3 add-padding-left'>
                <?= $form->field($model, 'diastolic_pressure')->textInput(['maxlength' => true, 'placeholder' => '舒张压（mmHg）'])->label('&nbsp') ?>
            </div>

        </div>

        <div class='row'>
            <div class='col-sm-6'>
                <?= $form->field($model, 'oxygen_saturation')->textInput() ?>
            </div>


            <div class='col-sm-6'>
                <?=$form->field($model,'remark')->textInput(['maxlength'=>true])?>
            </div>
        </div>

<!--        <div class='row'>
            <div class='col-sm-6'>
                <?php // $form->field($model, 'fall_score')->textInput() ?>
            </div>
            <div class="col-sm-6">
               
            </div>
        </div>-->

        <?php if (!isset($isFormSubmit) || $isFormSubmit): ?>
            <div class='row'>
                <div class='col-sm-12'>
                    <div class="button-center">
                        <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form ', 'type' => 'button', 'data-dismiss' => 'modal']) ?>
                        <?= Html::button('保存', ['class' => 'btn btn-default btn-form ', 'type' => 'submit']) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!--</form>-->

        <?php ActiveForm::end(); ?>
    </div>

<?php
$this->registerJs("

            if($('#triageinfo-bloodtype').val() == '' || $('#triageinfo-bloodtype').val() == '0'){
                $(\"input[type=checkbox][name='TriageInfo[blood_type_supplement][]']\").attr(\"disabled\",true);
                $(\"input[type=checkbox][name='TriageInfo[blood_type_supplement][]']\").removeAttr(\"checked\");
            }
            $('#triageinfo-bloodtype').change(function () {
                var bloodtype = '';
                bloodtype = $('#triageinfo-bloodtype').val();
                if(bloodtype == ''|| bloodtype=='0'){
                    $(\"input[type=checkbox][name='TriageInfo[blood_type_supplement][]']\").attr(\"disabled\",true);
                    $(\"input[type=checkbox][name='TriageInfo[blood_type_supplement][]']\").removeAttr(\"checked\");
                }else{
                    $(\"input[type=checkbox][name='TriageInfo[blood_type_supplement][]']\").attr(\"disabled\",false);
                }
            });
            $('#triageinfo-heightcm').on(\"input propertychange\", function(){
               changeBmi();
            });
            $('#triageinfo-weightkg').on(\"input propertychange\", function(){
               changeBmi();
            });
            changeBmi();
            //初始化
            function changeBmi(){
                if(Number($('#triageinfo-heightcm').val()) && Number($('#triageinfo-weightkg').val()) >= 0){
                    var a = 'BMI值： ' + Number($('#triageinfo-weightkg').val()/($('#triageinfo-heightcm').val()/100*$('#triageinfo-heightcm').val()/100)).toFixed(2);
                    $('#bmiVal').html(a);
                    $('#bmiVal').addClass('higher-BMI');
                }else{
                    var a ='BMI值： -';
                    $('#bmiVal').html(a);
                    $('#bmiVal').removeClass('higher-BMI');
                }
            }
             $('body').on('change', '#triageinfo-treatment_type', function () {

                var type = $(this).val();
                $('#triageinfo-treatment').val('');
                if (5 == type) {
                    $('.treatment_div').show();
                } else {
                    $('.treatment_div').hide();
                }
            });

") ?>