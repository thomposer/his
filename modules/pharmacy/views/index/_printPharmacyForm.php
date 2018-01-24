<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\outpatient\models\CureRecord;
use app\modules\pharmacy\models\PharmacyRecord;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\spot\models\RecipeList;
use app\common\Common;
use app\modules\outpatient\models\RecipeRecord;
$attribute = $model->attributeLabels();

/* @var $this yii\web\View */
/* @var $model app\modules\cure\models\Cure */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="my-show recipe-print" id="<?= Yii::$app->request->get('id')?>recipe-myshow">
    <div class="rebate-foot-bottom">
        <?php
            if($spotConfig['logo_img']){
                $logoImg = $spotConfig['logo_shape'] == 1 ?"clinic-img":"clinic-img-long";
                echo Html::img(Yii::$app->params['cdnHost'] . $spotConfig['logo_img'],["class" => $logoImg,'onerror' => 'javascript:this.src=\'/public/img/charge/img_click_moren.png\'']);
            }
        ?>
        <p class="rebate-date fr"><?= $spotConfig['pub_tel']?'Tel:'.Html::encode($spotConfig['pub_tel']):''; ?></p>
        <span class="clearfix"></span>
        <div class="children-sign">儿科</div>
    </div>
    <span class="clearfix"></span>
    <p class = 'title rebate-title add-margin-bottom-20' style = "font-size:16px; margin-top:-50px"><?= Html::encode($spotConfig['spot_name']) ?></p>
    <p class = 'title rebate-title add-margin-bottom-20'>门诊处方</p>
    <div style="min-height: 700px;" class="print-main-contnet">
        <p class="title small-title-third">
            病历号：<?= $triageInfo['patient_number']?>
        </p>
        <div class="fill-info font-0px">
            <div>
                <div class="total-column-three-part">
                    <span class="column-name">姓名</span>
                    <div class="column-value"><?= Html::encode($triageInfo['username']) ?></div>
                </div>
                <div class="tc total-column-three-part">
                    <span class="column-name">性别</span>
                    <div class="column-value"><?= Patient::$getSex[$triageInfo['sex']]?></div>
                </div>
                <div class="tr total-column-three-part">
                    <span class="column-name">年龄</span>
                    <div class="column-value"><?=  $triageInfo['birthday'] ?></div>
                </div>

            </div>
            <div style="margin-top: 15px;">
                <div class="total-column-three-part">
                    <span class="column-name">出生日期</span>
                    <div style="width:58%;" class="column-value"><?=  $triageInfo['birth'] ?></div>
                </div>
                <div class="tc total-column-three-part">
                    <span class="column-name">体重</span>
                    <div  class="column-value"><?=  !empty($triageInfo['weightkg'])?$triageInfo['weightkg'].'KG':'' ?> </div>
                </div>
                <div class="tr total-column-three-part">
                    <span class="column-name">就诊科室</span>
                    <div style="width: 58%" class="column-value"><?= Html::encode($repiceInfo['second_department']) ?></div>
                </div>

            </div>
            <div style="margin-top: 15px;">
                <div class="total-column-three-part">
                    <span class="column-name">费别</span>
                    <div class="column-value"></div>
                </div>

                <div class="tc total-column-three-part">
                    <span class="column-name">日期</span>
                    <div  class="column-value"><?= date('Y-m-d H:i:s',strtotime($repiceInfo['time'])) ?></div>
                </div>
                <div class="tr total-column-three-part">
                    <span class="column-name">门诊号</span>
                    <div style="width:64%;" class="column-value"><?= Html::encode($triageInfo['case_id']) ?></div>
                </div>
            </div>
        </div>

        <?php echo  $this->render(Yii::getAlias('@orderFillerInfoPrint')) ?>

        <div  class="fill-info">
            <p class = 'font-3rem title small-title-third'>处方RP</p>

            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view charge-table-first'],
                'tableOptions' => ['class' => 'table  charge-table inspect-table font-5rem'],
                'headerRowOptions' => ['class' => 'header'],
                'rowOptions' => function ($model){
                    return ['class'=>'recipe-top skin_test_print_'.$model->id];
                },
                'layout' => '{items}',
                'columns' => [
                    [
                        'attribute' => 'name',
                        'headerOptions' => ['class' => 'col-sm-3','width-20'],
                        'value'=>function($dataProvider){
                            if($dataProvider->product_name !=''){
                                return $dataProvider->name."（".$dataProvider->product_name."）";
                            }else{
                                return $dataProvider->name;
                            }
                        }
                    ],
                    [
                        'attribute' => 'specification',
                        'headerOptions' => ['class' => 'col-sm-1','width-15'],
                    ],
                    [
                        'attribute' => 'dosage_form',
                        'headerOptions' => ['class' => 'col-sm-1','width-10'],
                        'value' => function ($model) {
                            return RecipeList::$getType[$model->dosage_form];
                        }
                    ],
                    [
                        'attribute' => 'num',
                        'headerOptions' => ['class' => 'col-sm-1'],
                        'value' => function ($model) {
                        return $model->num.RecipeList::$getUnit[$model->unit];
                        }
                    ],

                    [
                        'attribute' => 'price',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'value' => function($model){
                            return Common::num($model->price * $model->num);
                        }
                    ],
                    [
                        'attribute' => '备注',
                        'format'=>'raw',
                        'headerOptions' => ['class' => 'col-sm-5'],
                        'value' => function ($model) {
                           $html = '';
                            if($model->description !=''){
                                $html .= '<div class="pharmacy-description">医嘱：'.Html::encode($model->description).'</div>';
                            }
                            if($model->remark !=''){
                                $html .= '<div class="pharmacy-remark">药师：'.Html::encode($model->remark).'</div>';
                            }
                            return $html;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'headerOptions' => ['class' => 'col-sm-1'],
                        'value' => function($model){
                            return RecipeRecord::$getStatusOtherDesc[$model->status];
                        }
                    ],

                ],

            ]);
            ?>

        </div>
        <div class="line-img">
            <?= Html::img($baseUrl . "/public/img/common/line.png",["class" => "img-line-img"]);?>
        </div>


    </div>
    <div class="font-3rem doc-sign">
        接诊医生签名：
    </div>
    <span class="clearfix"></span>
    <div class="foot-bottom">
        <div class="font-3rem tow-line-buttom">
            <div class="rebate-foot-second">
                <div  class="rebate-bottom">审核药师：</div>
                <div  class="rebate-bottom">配药药师： </div>
                <div  class="rebate-bottom">发药药师：</div>
            </div>
        </div>
        <div class="total-price">
            <div class="font-3rem total-price-content">
                <?php
                $dataProviderArray = $dataProvider->query->asArray()->all();
                $totalPrice = '';
                foreach($dataProviderArray as $value){
                    $totalPrice += $value['price'] * $value['num'];
                }
                echo "药品总价：" . Common::num($totalPrice) . "元";
                ?>
            </div>
        </div>
    </div>





</div>






