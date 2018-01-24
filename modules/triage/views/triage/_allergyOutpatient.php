<?php
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use app\modules\outpatient\models\AllergyOutpatient;

$allergyModel=new AllergyOutpatient();
$attribute = $allergyModel->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
$length = count($allergyOutpatientModel);
$disabled = (boolean)(in_array($allergyOtherInfo['patientRecordStatus'],[4,5]));
?>


<?php
$css = <<<CSS

        .btn-allergy-submit[disabled],
        .btn-allergy-submit[disabled]:hover,
        .btn-allergy-submit[disabled]:focus{
            background-color: #76a6ef;
            border-color: #76a6ef;
        }
        #ptab4 #allergyoutpatient-allergy_degree > label {
            margin-left: 10px;
        }
CSS;
$this->registerCss($css);
?>

<div class="tab-pane" id="ptab4" data-type="4">
    <?php $form = ActiveForm::begin(['id' => 'j_tabForm_4', 'action' => Url::to(['@triageTriageInfo'])]); ?>
    <?php $model->modal_tab = 4; ?>
    <?= $form->field($model, 'modal_tab')->input('hidden')->label(false) ?>
    <?= $form->field($allergyOutpatientModel[0], 'record_id')->input('hidden',['name'=>'allergyOutpatient[record_id]'])->label(false) ?>
    <div class="allergy-form">
    <div class="row">
        <div class="col-sm-12">
            <label style="width: 40px;"><input type="radio" name="allergyOutpatient[haveAllergyOutpatient]" value="1" class="have-allergy" <?= $allergyOtherInfo['haveStatus'] == 0 ? ' checked' : '' ?>  <?= $disabled?'disabled':'' ?>> 无</label>
            <label style="width: 40px;"><input type="radio" name="allergyOutpatient[haveAllergyOutpatient]" value="2" class="have-allergy" <?= $allergyOtherInfo['haveStatus']? ' checked' : '' ?>  <?= $disabled?'disabled':'' ?>> 有</label>
        </div>
            
    </div>
    <div class="allergy-content" style="<?= $allergyOtherInfo['haveStatus'] == 0?'display:none':''?>">
        <?php foreach ($allergyOutpatientModel as $key => $allergyOutpatient): ?>
                <div class = 'row allergy-line'>
                    <div class = 'col-sm-3'>
                        <?= $form->field($allergyOutpatient, 'type')->dropDownList(AllergyOutpatient::$getAllergyType, ['prompt' => '请选择过敏类型','name'=>'allergyOutpatient[type][]','disabled' => $disabled])->label(false) ?>
                    </div>
                    <div class = 'col-sm-4' style="padding-left:0;">
                        <?= $form->field($allergyOutpatient, 'allergy_content')->textInput(['placeholder' => '请填写引起过敏的食物或者物品的名称','maxlength' => 255, 'name'=>'allergyOutpatient[allergy_content][]' , 'disabled' => $disabled])->label(false) ?>
                    </div>
                    <div class = 'col-sm-3' style="width:22%;padding-left:0px;padding-right:0;margin-top: 5px;">
                        <?= $form->field($allergyOutpatient, 'allergy_degree')->radioList(AllergyOutpatient::$getAllergyDegreeItems,['name'=>'allergyOutpatient[allergy_degree]['.$key.']', 'itemOptions' => ['disabled' => $disabled]])->label(false) ?>
                    </div>
                    <?php if(!$disabled): ?>
                    <div class = 'col-sm-2'>
                        <a href="javascript:void(0);" class="btn-from-delete-add btn allergy-delete" style="display: inline-block;">
                            <i class="fa fa-minus"></i>
                        </a>
                        <a href="javascript:void(0);" class="btn-from-delete-add btn allergy-add" style="display: <?= ($key == ($length - 1) ) ? 'inline-block' : 'none' ?>;"  data-key="<?= $key ?>">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
        <?php endforeach; ?>
    </div>
    </div>


    <div class = 'row'>
        <div class="button-center">
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form ', 'type' => 'button', 'data-dismiss' => 'modal']) ?>
            <?= Html::button('保存', ['class' => 'btn btn-default btn-form btn-allergy-submit', 'type' => 'submit','disabled' => $disabled]) ?>
        </div>
    </div>

    <!--</form>-->
    <?php ActiveForm::end(); ?>
</div>
<?php
$js = <<<JS
   var baseUrl = '$baseUrl';
   require(["$baseUrl/public/js/triage/allergy.js?v=$versionNumber"], function (main) {
        main.init();
        main.initAllergyBtn("#j_tabForm_4");
    });
JS;
$this->registerJs($js);
?>