<?php
use app\modules\charge\models\ChargeInfo;
use yii\grid\GridView;
use app\common\Common;
use yii\helpers\Html;
use app\modules\spot\models\RecipeList;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\charge\models\ChargeRecord;
use app\modules\patient\models\PatientRecord;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use app\modules\triage\models\TriageInfo;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use app\modules\outpatient\models\Outpatient;
use yii\helpers\Url;
use yii\widgets\DetailView;

$attribute = $model->attributeLabels();

$baseUrl = Yii::$app->request->baseUrl;
$action=Yii::$app->controller->action->id;

$allergyStr = '';
if ($triageInfo['allergy']) {
    $allergyArr = [];
    $allergy = json_decode($triageInfo['allergy'], true);
    $allergySource = ArrayHelper::map(Patient::$allergy1, 'id', 'name');
    $allergyReaction = ArrayHelper::map(Patient::$allergy2, 'id', 'name');
    $allergyDegree = ArrayHelper::map(Patient::$allergy3, 'id', 'name');
    foreach ($allergy as $v) {
        $str = '';
        $str.=$v['source'] ? $allergySource[$v['source']] : '';
        $str.=$v['reaction'] ? ',表现为:' . $allergyReaction[$v['reaction']] : '';
        $str.=$v['degree'] ? ',' . $allergyDegree[$v['degree']] : '';
        $allergyArr[] = $str;
    }
    $allergyArr = array_filter($allergyArr);
    $allergyStr = implode(' / ', $allergyArr);
}

?>

<div class="my-show" id="record<?=Yii::$app->request->get('id')?>myshow">
    <div class="rebate-foot-bottom">
        <div class="tow-line">
            <p class="rebate-pay spot_name"><?= Html::encode($soptInfo['spot_name']); ?></p>
            <p class="rebate-date"><?= $soptInfo['telephone']?'Tel:'.Html::encode($soptInfo['telephone']):''; ?></p>
        </div>
    </div>
    <div class="patientid">
        <p class = 'title small-title'>患者ID：<?= $triageInfo['patient_id'] ?></p>
    </div>
    <p class = 'title rebate-title'>门诊病历</p>
    <div class="print-main-contnet">
        <div class="fill-info">
                <div>
                    <table class="rebate-table table-responsive">
                        <tr>
                            <td>
                                <p>姓名<span class="td-field"><?= Html::encode($triageInfo['username']) ?></span></p>
                            </td>
                            <td>
                                <p>性别<span class="td-field"><?= Patient::$getSex[$triageInfo['sex']] ?></span></p>
                            </td>
                            <td>
                                <p>出生时间<span class="td-field" style="width:56%"><?=  $triageInfo['birthtime']!=0?date('Y-m-d',$triageInfo['birthtime']):'' ?></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>手机号码<span class="td-field" style="width:64%"><?= Html::encode($triageInfo['iphone']) ?></span></p>
                            </td>
                            <td>
                                <p>接诊类型<span class="td-field" style="width:64%"><?= PatientRecord::$getType[$triageInfo['type']]?></span></p>
                            </td>
                            <td>
                                <p>科室<span class="td-field" style="width:70%"><?=  Html::encode($repiceInfo['second_department']) ?></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>就诊日期<span class="td-field" style="width:64%"><?= date('Y.m.d',$triageInfo['create_time']) ?></span></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
    <div class="fill-info">
        <p class = 'title small-title-second'>病历信息</p>
        <div>
        <?= DetailView::widget([
            'options' => ['class' => 'table charge-table print-table'],
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'incidence_date',
                    'format' => 'raw',
                    'value' => date('Y-m-d',$model->incidence_date)
                ],
                [
                    'attribute' =>  'chiefcomplaint',
                    'format' => 'raw',
                    'value' => Html::encode($model->chiefcomplaint)
                ],
                [
                    'attribute' =>  'historypresent',
                    'format' => 'raw',
                    'value' => Html::encode($model->historypresent)
                ],
                [
                    'attribute' =>  'pasthistory',
                    'format' => 'raw',
                    'value' => Html::encode($model->pasthistory)
                ],
                [
                    'attribute' => 'allergy',
                    'format' => 'raw',
                'value' =>$allergyStr?$allergyStr:'无'
                ],
                [
                    'attribute' =>  'personalhistory',
                    'format' => 'raw',
                    'value' => Html::encode($model->personalhistory)
                ],
                [
                    'attribute' =>  'genetichistory',
                    'format' => 'raw',
                    'value' => Html::encode($model->genetichistory)
                ],
                [
                    'attribute' =>  'examination_check',
                    'format' => 'raw',
                    'value' => Html::encode($model->examination_check)
                ],
                [
                    'attribute' =>  'first_check',
                    'format' => 'raw',
                    'value' => Html::encode($model->first_check)
                ],
                [
                    'attribute' =>  'physical_examination',
                    'format' => 'raw',
                    'value' => Html::encode($model->physical_examination)
                ],
                [
                    'attribute' =>  'cure_idea',
                    'format' => 'raw',
                    'value' => Html::encode($model->cure_idea)
                ],
                [
                    'attribute' =>  'remark',
                    'format' => 'raw',
                    'value' => Html::encode($model->remark)
                ],
            ],
        ]) ?>
            </div>
    </div>

    <?php if($recipeRecordDataProvider):?>
        <div class="fill-info">
            <p class = 'title small-title-second'>处方</p>
                <div>
                <table class="table charge-table">
                    <?php foreach ($recipeRecordDataProvider as $key => $v): ?>
                        <tr>
                            <th style="width:10%">
                                <?= $key+1 ?>
                            </th>
                            <td style="width:60%">
                                <div>
                                    <div><?=Html::encode($v['name'])?>:<?= Html::encode($v['specification']) ?></div>
                                    <div><b>用法：</b><?= RecipeList::$getDefaultUsed[$v['used']]?>;<?= $v['dose'] ?><?= RecipeList::$getUnit[$v['unit']] ?>;<?= RecipeList::$getDefaultConsumption[$v['frequency']] ?>;<?= $v['day'] ?>天</div>
                                </div>
                            </td>
                            <td style="width:30%">
                                <div>
                                   共<?= $v['num'] ?><?= RecipeList::$getUnit[$v['unit']] ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
         </div>
    <?php endif;?>
</div>

    <div>
        <div class="fill-info-buttom">
            <div class="rebate-foot-bottom-second">
                <div class="tow-line-buttom">
                    <div class="rebate-foot-second">
                        <p class="rebate-write">接诊医生：  </p>
                        <p class="rebate-write">日期：</p>
                    </div>
                </div>
        </div>
        </div>
    </div>

</div>

