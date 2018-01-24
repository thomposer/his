<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use app\modules\triage\models\TriageInfo;
use app\modules\spot\models\CheckCode;
use app\modules\outpatient\models\FirstCheck;
if(2 == $type){
    $attribute = $triageInfoModel->attributeLabels();
}

$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
if(2 == $record_type){
    $data = CheckCode::getData();
    if($data){
        $childForm['count'] = FirstCheck::getCount($recordId);
        $childForm['check_code_id'] = $data['id'];
        $childForm['name'] = $data['name'];
        $childForm['content'] = $data['name'] . '(' . $data['help_code'] .')' . '(' . $data['major_code'] .')';
    }
}
?>
<div class="outpatient-first-check-weight-form col-sm-12 col-md-12 patient-basic">

    <?php $form = ActiveForm::begin([
        'id' => 'first-check-weight'
    ]); ?>
    <?php if(2 == $type): ?>
        <div class="row">
            <div class="basic-form-content">
                <?= $form->field($triageInfoModel, 'weightkg')->input('text', ['placeholder' => '体重的值精确到小数点后两位'])->label($attribute['weightkg'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
    <?php endif ?>
    <div class="row">
        <div class="basic-form-content">
            <!-- 初步诊断 -->
            <div class="form-group">
                <label class="control-label" for="checkrecord-check_id">初步诊断<span class = "label-required">*</span></label>
                <?= $this->render('_firstCheckForm', ['form' => $form,'firstCheckDataProvider'=>$firstCheckDataProvider,'modal'=>'-modal', 'childForm' => $childForm]) ?>
            </div>

        </div>
    </div>
    <div class = 'row'>
            <div class = 'col-sm-12'>
                <div class="button-center">
                    <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form ', 'type' => 'button', 'data-dismiss' => 'modal']) ?>
                    <?= Html::button('保存', ['class' => 'btn btn-default btn-form ', 'type' => 'submit']) ?>
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$js = <<<JS
   require(["$baseUrl/public/js/outpatient/firstCheckModal.js?v=$versionNumber"], function (main) {
        main.init();
        main.initFirstCheckBtn();
    });
JS;
$this->registerJs($js);
?>
<?php 
    $this->registerJs("
//            var \$select2 = $(\".outpatient-first-check-weight-form #popup-check-code-sel\").select2({
//                language: \"zh-CN\",
//                ajax: {
//                    url: getCheckCodeList,
//                    dataType: 'json',
//                    delay: 200,
//                    data: function (params) {
//                        return {
//                            search: params.term, // search term
//                        };
//                    },
//                    processResults: function (data) {
//                        return {
//                            results: data.list,
//                        };
//                    },
//                },
//                escapeMarkup: function (markup) {
//
//                    return markup;
//                }, // let our custom formatter work
//                minimumInputLength: 1, //至少输入多少个字符后才会去调用ajax
//                templateResult: function (repo) {
//                    return htmlEncodeByRegExp(repo.text);
//                },
//                templateSelection: function (repo) {
//                    return htmlEncodeByRegExp(repo.text);
//                },
//                width: \"100%\",
//            });
//
//            \$select2.data('select2').\$container.addClass(\"popup-check-code-sel2\");
    ");
?>
