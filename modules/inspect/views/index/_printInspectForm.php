<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\patient\models\Patient;
use app\modules\pharmacy\models\PharmacyRecord;
use app\modules\inspect\models\InspectRecordUnion;
if(isset($val['report_time'])){
    $report_time = $val['report_time'];
}
$status = 1;
$repiceInfo = PharmacyRecord::getRepiceInfo(Yii::$app->request->get('id'),1,$val['id']);
$record_id = Yii::$app->request->get('id');
$allergy = isset($allergy) ? $allergy[$record_id] : [];
//var_dump($allergy);exit();
/* @var $this yii\web\View */
/* @var $model app\modules\cure\models\Cure */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="my-show inspect-print">
    <div class="rebate-foot-bottom">
        <?php
        if ($spotConfig['logo_img']) {
            $logoImg = $spotConfig['logo_shape'] == 1 ?"clinic-img":"clinic-img-long";
            echo Html::img(Yii::$app->params['cdnHost'] . $spotConfig['logo_img'], ['class' => $logoImg, 'onerror' => 'javascript:this.src=\'' . $baseUrl . '/public/img/charge/img_click_moren.png\'']);
        }
        ?>
        <p class="rebate-date"><?= $spotConfig['pub_tel'] ? 'Tel:' . Html::encode($spotConfig['pub_tel']) : ''; ?></p>
        <div class="children-sign">儿科</div>
    </div>
    <span class="clearfix"></span>
    <p class = 'title rebate-title' style = "font-size:16px; margin-top:-50px"><?= Html::encode($spotConfig['spot_name']) ?></p>

    <p class = 'title rebate-title'>检验报告</p>
    <p class = 'tc title font-3rem'>（<?= Html::encode($val['name']) ?>）</p>
    <div style="min-height: 750px;" class="print-main-contnet font-3rem">
        <div class="fill-info">
            <p class = 'title small-title'>病历号：<?= $triageInfo['patient_number'] ?></p>
            <div class = 'patient-user'>
                <div>
                    <div class="total-column-three-part"><span class="column-name">姓名</span><span class="column-value" ><?= Html::encode( $triageInfo['username']) ?></span></div>

                    <div class="tc total-column-three-part"><span class="column-name">性别</span><span class="column-value" ><?= Patient::$getSex[$triageInfo['sex']]?></span></div>

                    <div class="tr total-column-three-part"><span class="column-name">年龄</span><span style="width:70%" class="column-value"><?=  Patient::dateDiffage($triageInfo['birthtime'])?></span></div>
                </div>

                <div class="line-margin-top">
                    <div class="total-column-three-part"><span class="column-name">出生日期</span><span style="width:58%;" class="column-value"><?= Html::encode($triageInfo['birth']) ?></span></div>

                    <div class="tc total-column-three-part"><span class="column-name">TEL</span><span class="column-value"><?= Html::encode($triageInfo['iphone']) ?></span></div>

                    <div class="tr total-column-three-part"><span class="column-name">就诊科室</span><span class="column-value" style="width:58%;"><?= Html::encode($repiceInfo['second_department']) ?></span></div>

                </div>

                <div class="line-margin-top">
                    <div class="total-column-three-part"><span class="column-name">接诊医生</span><span style="width:60%;" class="column-value"><?= Html::encode($repiceInfo['doctor']) ?></span></div>

                    <div class="tc total-column-three-part"><span class="column-name">开单时间</span><span style="width: 58%;" class="column-value"><?= $repiceInfo['time'] ?></span></div>

                    <div class="tr total-column-three-part"><span class="column-name">报告时间</span><span style="width: 58%;" class="column-value"><?= $report_time?date('Y-m-d H:i:s',$report_time):'' ?></span></div>

                </div>

            </div>

        </div>

        <?php echo  $this->render(Yii::getAlias('@orderFillerInfoPrint')) ?>

        <div class = 'fill-info'>
            <div class = 'title small-title-third'>检验项目</div>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive charge-table-first'],
                'tableOptions' => ['class' => 'font-3rem table table-hover charge-table inspect-table'],
                'headerRowOptions' => ['class' => 'header'],
                'layout' => '{items}',
                'columns' => [
                    [
                        'attribute' => 'name',
                        'headerOptions' => ['class' => 'width-50 col-sm-2'],
                        'format'=>'raw'
                    ],
                    [
                        'attribute' => 'result',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'format' => 'raw',
                        'value' => function ($model)use($status) {
                            $html = Html::encode($model->result);
                            $options = [];
                            if(in_array(strtoupper($model->result_identification), ['H','HH','P','Q','E'])){
                                $options['class'] = 'red';
                            }else if(in_array(strtoupper($model->result_identification), ['L','LL'])){
                                $options['class'] = 'blue';
                            }
                            $text = Html::tag('span',$html,$options);
                            return $text;
                        }
                    ],
                    [
                        'attribute' => 'result_identification',
                        'format' => 'raw',
                        'value' => function ($model){
                            return InspectRecordUnion::getResultIdentification($model->result_identification);
                        }
                    ],
                    [
                        'attribute' => 'unit',
                        'headerOptions' => ['class' => 'col-sm-2'],
                    ],

                    [
                        'attribute' => 'reference',
                        'headerOptions' => ['class' => 'col-sm-2'],
                    ],
                ],
            ]);
            ?>
        </div>


    </div>

    <div class="font-0px fill-info-buttom">
        <p class="font-3rem">注：此检验报告仅对本次标本负责，如有疑问，请立即与 <b>化验科</b> 联系,谢谢合作</p>
        <div class="double-underline"></div>
        <p class="font-3rem rebate-write">检验员： </p>
        <p class="font-3rem rebate-write">审核员： </p>
    </div>
    <span class="clearfix"></span>

</div>



