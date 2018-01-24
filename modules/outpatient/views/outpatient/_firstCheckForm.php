<?php

use app\modules\outpatient\models\FirstCheck;

$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
$length = count($firstCheckDataProvider);
if (isset($modal)) {
    $content = $modal;
} else {
    $content = '';
}
?>


<div class="first-check-content<?= $content ?>">
    <?php foreach ($firstCheckDataProvider as $key => $firstCheck): ?>
        <?php
        if ($firstCheck['check_code_id'] || $firstCheck->check_code_type == 1) {
            $selectHide = '';
            $hide = 'hide';
            $firstCheck->check_code_type = 1;
        } else {
            $hide = '';
            $selectHide = 'hide';
            $firstCheck->check_code_type = 2;
        }
        ?>
        <div class = 'row first-check-line'>
            <div class = 'col-sm-2 first-check-type'>
                <?= $form->field($firstCheck, 'check_code_type')->dropDownList([1 => 'ICD-10', 2 => '自定义'], ['name' => 'FirstCheck[check_code_type][]', 'class' => 'first-check-left select-first-check form-control'])->label(false) ?>
            </div>
            <div class = 'col-sm-6 first-check-text'>
                <div class="first-check-right form-group">
                    <select class="CheckCodeSel form-control <?= $selectHide ?>" name="FirstCheck[check_code_id][]">
                        <?php if(isset($childForm) && isset($childForm['check_code_id']) && $childForm['count'] == 0): ?>
                            <option value="<?php echo $childForm['check_code_id'] ?>" selected><?php echo $childForm['content'] ?></option>
                        <?php elseif ($firstCheck->check_code_id): ?>
                            <option value="<?php echo $firstCheck->check_code_id ?>" selected><?php echo $firstCheck->content ?></option>
                        <?php else: ?>
                            <option value="0">请输入名称、拼音码或ICD编码进行搜索</option>
                        <?php endif; ?>
                    </select>
                    <?php if(isset($childForm) && isset($childForm['check_code_id']) && $childForm['count'] == 0): ?>
                        <?= $form->field($firstCheck, 'content')->textInput(['placeholder' => '请填输入', 'maxlength' => 30, 'name' => 'FirstCheck[content][]', 'class' => 'first-check-custom form-control ' . $hide, 'value' => $childForm['name']])->label(false) ?>
                    <?php else: ?>
                        <?= $form->field($firstCheck, 'content')->textInput(['placeholder' => '请填输入', 'maxlength' => 30, 'name' => 'FirstCheck[content][]', 'class' => 'first-check-custom form-control ' . $hide])->label(false) ?>
                    <?php endif; ?>
                    <div class="help-block"></div>
                </div>
            </div>
            <div class = 'col-sm-2' style = "width: 140px;margin-top: 5px;">
                <?= $form->field($firstCheck, 'check_degree')->radioList(FirstCheck::$getCheckDegreeItems, ['name' => 'FirstCheck[check_degree][' . $key . ']'])->label(false) ?>
            </div>
            <div class = 'col-sm-2 first-check-line-button'>
                <a href="javascript:void(0);" class="btn-from-delete-add btn first-check-delete margin-top-0" style="display:inline-block">
                    <i class="fa fa-minus"></i>
                </a>
                <a href="javascript:void(0);" class="btn-from-delete-add btn first-check-add margin-top-0" style="display: <?= ($key == ($length - 1) ) ? 'inline-block' : 'none' ?>;"  data-key="<?= $key ?>">
                    <i class="fa fa-plus"></i>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>