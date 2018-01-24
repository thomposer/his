<?php

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use app\modules\triage\models\TriageInfo;
use dosamigos\datetimepicker\DateTimePicker;

$attribute = $assessmentModel->attributeLabels();
$childAttribute = $childModel->attributeLabels();
$fieldConfig = ['template' => "<div class='form-group has-success'>{label}{input}{error}</div>"];
/**
 * 发育评估
 */
?>

<?php
$css = <<<CSS
   .font-0px{
        font-size: 0;
   }
   .add-del-text{
        color: #ffffff;
        background-color: #76a6ef;
        width: 47px;
        height: 33px;
        line-height: 33px;
        border-radius: 5px;
        padding: 0px;
    }
    .btn-from-delete-add-top-0{
        margin-top: 0;
    }
    .clinic-delete-score{
        background-color: white;
        border-radius: 4px;
        color: #6B798E;
        border: 1px solid #99A3B1;
        border-left: 1px solid #99A3B1 !important;
   }
   .clinic-delete-score:hover{
        color: #6B798E;
        opacity: 0.8;
   }
   .ml-10{
        margin-left:12%;
   }
   .mb-2{
        margin-bottom: 7px;
   }
CSS;
$this->registerCss($css);
?>
<div class="tab-pane" id="ptab2" data-type="2">
    <?php
    $form = ActiveForm::begin([
                'id' => 'j_tabForm_2',
                'action' => Url::to(['@triageTriageInfo']),
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "<div class='labelWidth text-right'>{label}</div><div class='col-xs-10 col-sm-10 childInput'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0'><div class = 'col-xs-2 widthLeft'></div><div class = 'col-xs-2 widthRightError'>{error}</div></div>",
                ]
    ]);
    ?>
    <?= $form->field($assessmentModel, 'record_id')->input('hidden')->label(false) ?>
    <?php $model->modal_tab = 2; ?>
    <?= $form->field($model, 'modal_tab')->input('hidden')->label(false) ?>
    <div class = 'row'>
        <div class="col-sm-6 ">
            <label class="control-label assessmentAge" for="childexaminationassessment-assessmentAge"><?= $attribute['assessmentAge'] . '：' . $assessmentModel->assessmentAge ?></label>
        </div>
    </div>
    <div class = 'row'>
        <div class="col-sm-6 asq-item">
            <span class="item-num"></span><span class="item-text"> 疼痛评估</span>
        </div>
    </div>
    <div class="assesment-content">
        <div class="row mb-2">
            <div class="col-sm-3">
                <span class="ml-10">疼痛评分(0-10)</span>
            </div>
            <div class="col-sm-4">
                评估时间
            </div>
            <div class="col-sm-3">
                <span class="ml-10">备注</span>
            </div>
        </div>
        <?php foreach ($painScore as $k => $v): ?>
            <?php
            $childModel->score = $v['score'];
            $childModel->assesment_time = $v['assesment_time'];
            $childModel->remark = $v['remark'];
            ?>
            <div class="row assesment-config">
                <div class="col-sm-3">
                    <?= $form->field($childModel, 'score', $fieldConfig)->textInput(['name' => 'ChildAssessment[score][]', 'class' => 'child-assessment-score form-control', 'placeholder' => '请输入' . $childAttribute['score']])->label(false) ?>
                </div>
                <div class="col-sm-4 bootstrap-timepicker">
                    <?php
                    echo DateTimePicker::widget([
                        'id' => 'child-assesment-time' . $k,
                        'name' => 'ChildAssessment[assesment_time][]', //当没有设置model时和attribute时必须设置name
                        'language' => 'zh-CN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd hh:ii',
                            'size' => 'lg',
                            'minuteStep' => 10,
                            'endDate' => date('Y-m-d H:i'),
                            'modalBackdrop' => true,
                            'class' => "child-assesment-time",
                        ],
                        'options' => [
                            'autocomplete' => 'off',
                            'class' => "child-assesment-time",
                            'placeholder' => '评估时间',
                        ],
                        'value' => $childModel->assesment_time == 0 ? '' : date("Y-m-d H:i", $childModel->assesment_time)
                    ]);
                    ?>
                    <div class="help-block"></div>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($childModel, 'remark', $fieldConfig)->textInput(['name' => 'ChildAssessment[remark][]', 'class' => 'child-assessment-remark form-control', 'maxlength' => 30, 'placeholder' => '请输入' . $childAttribute['remark']])->label(false) ?>
                </div>
                <div class="col-sm-2 font-0px">
                    <?= Html::a('删除', 'javascript:void(0);', ['class' => 'btn-from-delete-add add-del-text  btn clinic-delete-score btn-from-delete-add-top-0']) ?>
                    <?= Html::a('再评估', 'javascript:void(0);', ['class' => 'btn-from-delete-add add-del-text btn clinic-add-score btn-from-delete-add-top-0']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class = 'row'>
        <div class="col-sm-6 asq-item">
            <span class="item-num"></span><span class="item-text">跌倒评估</span>
        </div>
    </div>
    <div class="fall-content">
        <div class="row mb-2">
            <div class="col-sm-3">
                <span class="ml-10">跌倒评分(HDFS 6-20)</span>
            </div>
            <div class="col-sm-4">
                评估时间
            </div>
            <div class="col-sm-3">
                <span class="ml-10">备注</span>
            </div>
        </div>
        <?php foreach ($fallScore as $k => $v): ?>
            <?php
            $childModel->fallScore = $v['score'];
            $childModel->fallTime = $v['assesment_time'];
            $childModel->fallRemark = $v['remark'];
            ?>
            <div class="row assesment-config">
                <div class="col-sm-3">
                    <?= $form->field($childModel, 'fallScore', $fieldConfig)->textInput(['name' => 'ChildAssessment[fallScore][]', 'class' => 'child-fall-score form-control', 'placeholder' => '请输入跌倒评分'])->label(false) ?>
                </div>
                <div class="col-sm-4 bootstrap-timepicker">
                    <?php
                    echo DateTimePicker::widget([
                        'id' => 'child-fall-time' . $k,
                        'name' => 'ChildAssessment[fallTime][]', //当没有设置model时和attribute时必须设置name
                        'language' => 'zh-CN',
                        'template' => "{input}{reset}{button}",
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd hh:ii',
                            'size' => 'lg',
                            'minuteStep' => 10,
                            'modalBackdrop' => true,
                            'class' => "child-fall-time",
                            'endDate' => date('Y-m-d H:i'),
                        ],
                        'options' => [
                            'autocomplete' => 'off',
                            'class' => "child-fall-time",
                            'placeholder' => '评估时间',
                        ],
                        'value' => $childModel->fallTime == 0 ? '' : date("Y-m-d H:i", $childModel->fallTime)
                    ]);
                    ?>
                    <div class="help-block"></div>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($childModel, 'fallRemark', $fieldConfig)->textInput(['name' => 'ChildAssessment[fallRemark][]', 'class' => 'child-fall-remark form-control', 'maxlength' => 30, 'placeholder' => '请输入备注'])->label(false) ?>
                </div>
                <div class="col-sm-2 font-0px">
                    <?= Html::a('删除', 'javascript:void(0);', ['class' => 'btn-from-delete-add add-del-text btn clinic-delete-score btn-from-delete-add-top-0']) ?>
                    <?= Html::a('再评估', 'javascript:void(0);', ['class' => 'btn-from-delete-add add-del-text btn clinic-add-score btn-from-delete-add-top-0']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class = 'row'>
        <div class="col-sm-6 asq-item">
            <span class="item-num"></span><span class="item-text"> ASQ-3评估结果</span>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
            <?= $form->field($assessmentModel, 'communicate')->radioList($assessmentModel::$getCommunicate, ['class' => 'add-line-height'])->label($attribute['communicate']) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
            <?= $form->field($assessmentModel, 'coarse_action')->radioList($assessmentModel::$getCoarseAction, ['class' => 'add-line-height'])->label($attribute['coarse_action']) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
            <?= $form->field($assessmentModel, 'fine_action')->radioList($assessmentModel::$getFineAction, ['class' => 'add-line-height'])->label($attribute['fine_action']) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
            <?= $form->field($assessmentModel, 'solve_problem')->radioList($assessmentModel::$getFineAction, ['class' => 'add-line-height'])->label($attribute['solve_problem']) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
            <?= $form->field($assessmentModel, 'personal_society')->radioList($assessmentModel::$getFineAction, ['class' => 'add-line-height'])->label($attribute['personal_society']) ?>
        </div>
    </div>

    <!--    <div class = 'row'>-->
    <!--        <div class="col-sm-12 asq-item">-->
    <!--            <span class="item-num"></span><span class="item-text"> ASQ-SE评估结果</span>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class = 'row'>-->
    <!--        <div class = 'col-sm-12' >-->
    <!--            --><?php //echo $form->field($assessmentModel, 'score')->textInput(['maxlength' => 16, 'placeholder' => '请输入' . $attribute['score']])   ?>
    <!--        </div>-->
    <!--        <div class = 'col-sm-12'>-->
    <!--            --><?php //echo $form->field($assessmentModel, 'evaluation_result')->radioList($assessmentModel::$getEvaluationResult,['class'=>'add-line-height'])->label($attribute['evaluation_result'])   ?>
    <!--        </div>-->
    <!--    </div>-->

    <div class = 'row'>
        <div class="col-sm-6 asq-item">
            <span class="item-num"></span><span class="item-text"> 其他评估结果</span>
        </div>
    </div>

    <div class = 'row'>
        <div class = 'col-sm-12'>
            <?php
            if (!$assessmentModel->evaluation_type_result) {
                $assessmentModel->evaluation_type_result = $assessmentModel->other_evaluation_type ? $assessmentModel->other_evaluation_type . ';' . $assessmentModel->other_evaluation_result : $assessmentModel->other_evaluation_result;
            }
            ?>
            <?= $form->field($assessmentModel, 'evaluation_type_result')->textarea(['maxlength' => 500, 'rows' => 6, 'placeholder' => '请输入' . $attribute['evaluation_type_result']])->label(false, '其他评估结果') ?>
        </div>
    </div>

    <?php if (!isset($isFormSubmit) || $isFormSubmit): ?>
        <div class = 'row'>
            <div class = 'col-sm-12'>
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
