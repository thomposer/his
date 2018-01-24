<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\outpatient\models\DentalHistoryRelation;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\modules\outpatient\models\DentalFirstTemplate;
use app\modules\outpatient\models\DentalReturnvisitTemplate;

$dentalHistoryAttribute = $dentalHistory->attributeLabels();
$typeList = DentalHistoryRelation::$getType;
$recordType = $dentalHistory->type;
$recordStatus = Yii::$app->request->get('recordType');
$has_save = $recordStatus ? 0 : ($dentalHistory->id ? 1 : 0);
$dental_case_id = Yii::$app->request->get('dental_case_id');
$has_save = ($dental_case_id !== null) ? 0 : $has_save;
$hasTeethMark = 2;
?>
<div class="outpatient-dentalRecordForm col-sm-12 col-md-12 patient-basic">

    <?php $form = ActiveForm::begin([
        'id' => 'dental-history'
    ]); ?>
    <div class="row basic-form">
        <div class=" basic-header">
            <span class='basic-left-info'>
                病历信息
            </span>
            <span class='basic-right-up basic-right-up-case'>
                <div class='col-md-6 case-template-select'>
                    <?php if ($has_save == 1): ?>
                        <span class="edit_button"><?php echo Html::button('修改', ['class' => 'btn btn-default btn-form']) ?></span>
                    <?php endif; ?>
                        <span class="<?= $has_save == 1 ? 'case_template_none' : 'case_template' ?>">
                            <?php if(1 == $recordType): ?>
                                <?php echo Html::dropDownList('',$dental_case_id, ArrayHelper::map(DentalFirstTemplate::templateList(), 'id', 'name', 'group'), ['id' => 'dental-template', 'class' => 'form-control', 'style' => 'width:300px;margin-right:15px', 'prompt' => '- 选择病历模板 -']) ?>
                            <?php else: ?>
                                <?php echo Html::dropDownList('',$dental_case_id, ArrayHelper::map(DentalReturnvisitTemplate::templateList(), 'id', 'name', 'group'), ['id' => 'dental-template', 'class' => 'form-control', 'style' => 'width:300px;margin-right:15px', 'prompt' => '- 选择病历模板 -']) ?>
                            <?php endif; ?>
                        </span>
                </div>
            </span>
        </div>
        <div class="basic-form-content">
<!--            <div class="row">-->
<!--                <div class='col-md-12'>-->
<!--                    <div class="form-group field-recordType">-->
<!--                        <div class="recordType-labelWidth text-right"><label class="control-label"-->
<!--                                                                             style="line-height: 36px;">类型：</label>-->
<!--                        </div>-->
<!--                        <div class="col-xs-9 col-sm-9 dental-record-type">-->
<!--                            --><?php //if (2 == $recordType): ?>
<!--                                <label class="record-type-add-padding" style="line-height: 36px;">-->
<!--                                    <input type="radio" name="DentalHistory[type]" value="1">初诊-->
<!--                                </label>-->
<!--                                <label class="record-type-add-padding" style="line-height: 36px;">-->
<!--                                    <input type="radio" name="DentalHistory[type]" value="2" checked>复诊-->
<!--                                </label>-->
<!--                            --><?php //else: ?>
<!--                                <label class="record-type-add-padding" style="line-height: 36px;">-->
<!--                                    <input type="radio" name="DentalHistory[type]" value="1" checked>初诊-->
<!--                                </label>-->
<!--                                <label class="record-type-add-padding" style="line-height: 36px;">-->
<!--                                    <input type="radio" name="DentalHistory[type]" value="2">复诊-->
<!--                                </label>-->
<!--                            --><?php //endif; ?>
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0">-->
<!--                        <div class="col-xs-2 widthLeft"></div>-->
<!--                        <div class="col-xs-2 widthRightError">-->
<!--                            <div class="help-block">-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
            <?php if ($recordType == 2): ?>
                <?= $form->field($dentalHistory, 'returnvisit')->textarea(['rows' => 4, 'id' => 'returnvisit', 'maxlength' => true])->label($dentalHistoryAttribute['returnvisit']) ?>
            
                <?= $this->render('_allergyForm', ['model' => $dentalHistory, 'form' => $form]) ?>
            <?php else: ?>
                <?= $form->field($dentalHistory, 'chiefcomplaint')->textarea(['rows' => 4, 'id' => 'chiefcomplaint', 'maxlength' => true])->label($dentalHistoryAttribute['chiefcomplaint'] . '<span class = "label-required">*</span>') ?>

                <?= $form->field($dentalHistory, 'historypresent')->textarea(['rows' => 4, 'id' => 'historypresent', 'maxlength' => true])->label($dentalHistoryAttribute['historypresent'] . '<span class = "label-required">*</span>') ?>

                <?= $form->field($dentalHistory, 'pasthistory')->textarea(['rows' => 4, 'id' => 'pasthistory', 'maxlength' => true])->label($dentalHistoryAttribute['pasthistory'] . '<span class = "label-required">*</span>') ?>

                <?= $this->render('_allergyForm', ['model' => $dentalHistory, 'form' => $form]) ?>
            <?php endif;?>

            <!-- 初步诊断 -->
            <div class="form-group">
                <label class="control-label" for="checkrecord-check_id">初步诊断<span style="color:#FF5000;">（若需要给患者开检验，检查，治疗，处方医嘱，请务必填写初步诊断）</span></label>
                <?= $this->render('_firstCheckForm', ['model' => $getOutpatientRelationModel, 'form' => $form,'firstCheckDataProvider'=>$firstCheckDataProvider]) ?>
                <!-- 下拉选择 -->
            </div>

            <div class="dental-line"></div>
            <div class="tooth-sysc-top"><input type="checkbox" name="tooth-sysc" class="tooth-sysc">牙位病症同步<span class="dental-error-info">（手动输入牙位数填写规则：数字填写范围1～8，字母填写范围A～E，不可重复，最多不超过13个字）</span></div>
            <?php foreach ($typeList as $key => $value): ?>
                <div class="dental-check">
                    <label class="control-label dental-check-title" for="dental-check" data-value="2"><?= $value ?> <i class="fa fa-pencil fa-fw dentail-content-edit"></i></label>
                    <div class="fa fa-question-circle blue dental-check-tips" data-toggle="tooltip" data-html="true" data-placement="top" data-original-title="牙位图说明：<br/>1.点击铅笔图标，可直接在十字图上直接输入填写<br/>2.点击十字图，可在弹窗中选择牙位进行标记"></div>
                    <div class="dental-check-content-list">
                    <?php if (isset($dentalHistoryRelation[$key])): ?>
                        <?php foreach ($dentalHistoryRelation[$key] as $k => $data): ?>
                            <?php 
                              //是否对口腔检查、辅助检查、诊断、治疗方案、治疗牙位图进行过标记
                              //1-有标记 2-无标记
                              if(!empty($data['position'])){
                                  $hasTeethMark = 1;
                              }
                              //得到标记了的类型(口腔检查、辅助检查、诊断、治疗方案、治疗)
                              $teethMarkType[] = $value;
                            ?>
                            <?php $positionArr = $data['position'] ? explode(',', $data['position']) : ['', '', '', '']; ?>
                            <div class="dental-check-content">
                                <div class="tooth-position">
                                    <div class="left-top"><span class="left-top-text"><?= $positionArr[0] ?></span><?= Html::input('text','', $positionArr[0], ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
                                    <div class="right-top"><span class="right-top-text"><?= $positionArr[1] ?></span><?= Html::input('text','', $positionArr[1], ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
                                    <div class="left-bottom"><span class="left-bottom-text"><?= $positionArr[3] ?></span><?= Html::input('text','', $positionArr[3], ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
                                    <div class="right-bottom"><span class="right-bottom-text"><?= $positionArr[2] ?></span><?= Html::input('text','', $positionArr[2], ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
                                    <div style="clear:both;"></div>
                                </div>

                                <textarea class="dentail-content form-control" class="form-control" name="DentalHistoryRelation[content][]" rows="5" maxlength="1000"><?= $data['content'] ?></textarea>
                                <input type="hidden" name="DentalHistoryRelation[type][]" value="<?= $key ?>">
                                <input type="hidden" name="DentalHistoryRelation[position][]" class="dental-history-relation-position"
                                       value="<?= $data['position'] ?>" >
                                <?php if (0 == $k): ?>
                                    <div class="add-booth">
                                        <botton type="button" class="add-booth-button" data-type="<?= $key ?>">添加牙位
                                        </botton>
                                    </div>
                                <?php else: ?>
                                    <div class="add-booth">
                                        <a href="javascript:void(0);"
                                           class="btn-from-delete-add btn dental-check-delete btn-from-delete-add-margin-top-0"
                                           style="display: inline-block;">
                                            <i class="fa fa-minus"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if(($positionArr[0] || $positionArr[1] || $positionArr[2] || $positionArr[3]) && $data['dental_disease'] !=0){
                                        $textDental = 'display: block';
                                    }else{;
                                        $textDental = 'display: none';
                                    }
                                ?>
                                    <div style="display: none" class="has-dental-disease" >

                                        <div  class="dental-disease-to-select">请选择病症</div>
                                        <div class="dental-disease-select-content">
                                            <?= Html::dropDownList('DentalHistoryRelation[dental_disease][]',$data['dental_disease'],DentalHistoryRelation::$dentalDisease,['prompt' => ['text' => '请选择','options' => ['value' => 0,]],'class' => 'dental-disease-select form-control','style' => 'width:170px;margin-left:11px']);?>
                                        </div>
                                    </div>
                                        <div style="<?= $textDental ?>" class="dental-disease-show">
                                        
                                        病症：<?= DentalHistoryRelation::$dentalDisease[$data['dental_disease']] ?>
                                    </div>

                                <div style="clear:both;"></div>

                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="dental-check-content">
                            <div class="tooth-position">
                                <div class="left-top"><span class="left-top-text"></span><?= Html::input('text','', '', ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
                                <div class="right-top"><span class="right-top-text"></span><?= Html::input('text','', '', ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
                                <div class="left-bottom"><span class="left-bottom-text"></span><?= Html::input('text','', '', ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
                                <div class="right-bottom"><span class="right-bottom-text"></span><?= Html::input('text','', '', ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
                                <div style="clear:both;"></div>
                            </div>
                            <textarea class="dentail-content form-control" class="form-control" name="DentalHistoryRelation[content][]" rows="5" maxlength="1000"></textarea>
                            <input type="hidden" name="DentalHistoryRelation[type][]" value="<?= $key ?>">
                            <input type="hidden" name="DentalHistoryRelation[position][]" value=""
                                   class="dental-history-relation-position">
                            <div class="add-booth">
                                <botton type="button" class="add-booth-button" data-type="<?= $key ?>">添加牙位</botton>
                            </div>
                            <div style="clear:both;"></div>
                            <div style="display: none" class="has-dental-disease" >
                                <div  class="dental-disease-to-select">请选择病症</div>
                                <div class="dental-disease-select-content">
                                    <?= Html::dropDownList('DentalHistoryRelation[dental_disease][]','',DentalHistoryRelation::$dentalDisease,['prompt' => ['text' => '请选择', 'options' => ['value' => 0]],'class' => 'dental-disease-select form-control']);?>
                                </div>
                            </div>
                            <div style="display: none" class="dental-disease-show">
                            </div>
                        </div>
                    <?php endif; ?>
                    </div>
                    <div class="help-block" style="color: #FF5000;display: none;">填写有误。（填写规则：数字填写范围1～8，字母填写范围A～E，不可重复，最多不超过13个字）</div>
                    <div class="help-block-dental-show" style="display: none;">病症选择不能为空</div>
                    <div class="form-group dental-check-btn" style="display: none;margin-bottom:20px;">
                        <div class="btn btn-cancel btn-form dental-check-btn-cancel">取消</div>
                        <div class="btn btn-default btn-form dental-check-btn-commit">保存</div>
                    </div>
                </div>
<!--                <div class="dental-line"></div>-->
            <?php endforeach; ?>
            <div class="dental-line"></div>
            <?= $form->field($dentalHistory, 'advice')->textarea(['rows' => 4, 'id' => 'advice', 'maxlength' => true])->label($dentalHistoryAttribute['advice']) ?>

            <?= $form->field($dentalHistory, 'remarks')->textarea(['rows' => 4, 'id' => 'remarks', 'maxlength' => true])->label($dentalHistoryAttribute['remarks']) ?>

            <div class="form-group clearfix">
                <?php if ($has_save == 1): ?>
                    <?= Html::button('修改', ['class' => 'btn btn-default btn-form reocrd-btn-custom']).Html::button('修改', ['class' => 'btn btn-default btn-form reocrd-btn-custom btn-fixed']) ?>
                  
                    <?= Html::button('打印病历', ['class' => 'btn btn-default btn-form pull-right print-teeth-record','style'=>'margin-right:0 !important;', 'name' => 'teethPrint' .$_GET['id']]); ?>

                    <?php
                      if($hasTeethMark == 1){//如果选择了牙位图,显示"打印牙位图"按钮

                        $options = [
                            'data-pjax' => '0',
                            'role' => 'modal-remote',
                            'data-modal-size' => 'normal',
                            'data-delete' => true,
                            'data-modal-size' => 'normal',
                            'class' => 'btn btn-default btn-form fr print-teeth-img',
                        ];
                        echo Html::a('打印牙位图', Url::to(['@apiOutpatientMarkType','recordId'=>$_GET['id']]), $options);
                      }
                    ?>
                <?php else: ?>
                    <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form reocrd-btn-custom']).Html::submitButton('保存', ['class' => 'btn btn-default btn-form reocrd-btn-custom btn-fixed']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<!--此处有作用，不能删-->
<div class="hide">
    <label class="control-label" for="upload-mediaFile">上传附件</label>
    <?= $this->render('_fileUpload', ['model' => $getTriageInfoModel, 'medicalFile' => $medicalFile]) ?>
</div>
<div id="teeth-print" class="hide"></div>
<!-- outpatient-_recordForm -->

<!--class="hide"-->
