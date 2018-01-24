<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\patient\models\Patient;
use app\common\Common;
use \app\modules\outpatient\models\CureRecord;
$status=1;
Yii::$app->params['totalPrice'] = 0;
/* @var $this yii\web\View */
/* @var $model app\modules\cure\models\Cure */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="my-show cure-print" id="<?= Yii::$app->request->get('id')?>cure-myshow">
    <div class="rebate-foot-bottom">
        <?php
        if ($spotConfig['logo_img']) {
            $logoImg = $spotConfig['logo_shape'] == 1 ?"clinic-img":"clinic-img-long";
            echo Html::img(Yii::$app->params['cdnHost'] . $spotConfig['logo_img'], ['class' => $logoImg, 'onerror' => 'javascript:this.src=\'' . $baseUrl . '/public/img/charge/img_click_moren.png\'']);
        }
        ?>
        <p class="rebate-date fr"><?= $spotConfig['pub_tel']?'Tel:'.Html::encode($spotConfig['pub_tel']):'' ?></p>
        <div class="children-sign">儿科</div>
    </div>
    <span class="clearfix"></span>
    <p class = 'title rebate-title add-margin-bottom-20' style = "font-size:16px; margin-top:-50px"><?= Html::encode($spotConfig['spot_name']) ?></p>
    <p class = 'title rebate-title add-margin-bottom-20'>治疗单</p>
    <div style="min-height: 750px;" class="print-main-contnet">
        <div class="fill-info">
            <p class = 'title small-title'>病历号：<?= $triageInfo['patient_number'] ?></p>
            <div class = 'patient-user'>
                <div>
                    <div class="total-column-three-part"><span class="column-name">姓名</span><span class="column-value" ><?= Html::encode($triageInfo['username']) ?></span></div>

                    <div class="tc total-column-three-part"><span class="column-name">性别</span><span class="column-value"><?= Patient::$getSex[$triageInfo['sex']]?></span></div>

                    <div class="tr total-column-three-part"><span class="column-name">年龄</span><span class="column-value"><?=  $triageInfo['birthday'] ?></span></div>
                </div>

                <div class="line-margin-top">
                    <div class="total-column-three-part"><span class="column-name">出生日期</span><span style="width: 58%" class="column-value"><?=  $triageInfo['birth'] ?></span></div>

                    <div class="tc total-column-three-part"><span class="column-name" >TEL</span><span class="column-value"><?= Html::encode($triageInfo['iphone']) ?></span></div>

                    <div class="tr total-column-three-part"><span class="column-name">就诊科室</span><span class="column-value" style="width:58%;"><?= Html::encode($repiceInfo['second_department']) ?></span></div>
                </div>

                <div class="line-margin-top">
                    <div  class="total-column-three-part"><span class="column-name">开单时间</span><span  style="width:58%;" class="column-value"><?= $repiceInfo['time'] ?></span></div>
                </div>

            </div>
        </div>

        <?php echo  $this->render(Yii::getAlias('@orderFillerInfoPrint')) ?>

        <div class=" fill-info col-xs-12">
            <div class = 'title small-title-third'>治疗项</div>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view charge-table-first', 'id' => 'grid'],
                'tableOptions' => ['class' => 'font-3rem table table-hover charge-table inspect-table '],
                'headerRowOptions' => ['class' => 'header'],
                'layout' => '{items}',
                'columns' => [
                    [
                        'attribute' => 'name',
                        'headerOptions' => ['class' => 'col-sm-5 width-20'],
                    ],
                    [
                        'attribute' => 'unit',
                        'headerOptions' => ['class' => 'col-sm-1'],
                    ],
                    [
                        'attribute' => 'time',
                        'headerOptions' => ['class' => 'col-sm-1'],

                    ],
                    [
                        'attribute' => 'price',
                        'headerOptions' => ['class' => 'col-sm-1']
                    ],
                    [
                        'attribute' => 'totalPrice',
                        'headerOptions' => ['class' => 'col-sm-1'],
                        'value' => function($model){
                            Yii::$app->params['totalPrice'] += Common::num($model->time * $model->price);
                            return Common::num($model->time * $model->price);
                        }
                    ],
                    [
                        'attribute' => 'description',
                        'headerOptions' => ['class' => 'col-sm-3'],
                    ],
                    [
                        'attribute' => 'cure_result',
                        'format' => 'raw',
                        'value' =>function($model){
                            if($model->type == 1){
                                return CureRecord::$getCureResult[$model->cure_result];

                            }else{
                                return Html::encode($model->cure_result);
                            }

                        }
                    ],
                    [
                        'attribute' => 'remark',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'format' => 'raw',
                        'value' => function ($model)use($status) {
                            $html = Html::encode($model->remark);
                            $hiddenHtml="<input type='hidden' name='id[]' class='checkitemid' value='$model->id'>";
                            if ($status == 2) {
                                $text = Html::input('text', 'CureRecord[remark]', $html, ['class' => 'form-control']);
                                $text.=$hiddenHtml;
                            } else {
                                $text = '<span>' . $html . '</span>';
                                $text .= Html::input('text', 'CureRecord[remark]', $html, ['class' => 'form-control hid L-remark']);
                                $text .=$hiddenHtml;
                            }
                            return $text;
                        }
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
    <div class="fill-info-buttom">
        <p  class="width-30 fr">接诊医生签名：</p>
        <div class="fl line"></div>
        <p  class="fl add-margin-top-10">合计金额： <?= Common::num(Yii::$app->params['totalPrice']) . '元'; ?></p>
        <p  class="width-30 add-margin-top-10 fr">执行人：</p>
    </div>
    <span class="clearfix"></span>
</div>



