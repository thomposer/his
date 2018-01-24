<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\SpotConfig;
use yii\helpers\Url;
$attributeLabels = $model->attributeLabels();
?>

<div class="spot-config-form col-md-8">
    <?php $form = ActiveForm::begin(
        [
            'method'=>'post',
            'options'=>['enctype'=>'multipart/form-data'],
        ]
    ); ?>
    <div class = 'row row-title'>
        打印logo
    </div>
    <div class = 'row logo-config'>
        <div class = 'col-sm-6 col-md-6'>
            <?= $form->field($model,'logo_shape')->radioList(SpotConfig::$logoShape) ?>
            <?= $form->field($model, 'logo_img')->hiddenInput(['id' =>'avatar_url'])->label(false); ?>
            <div id="crop-avatar">
                <!-- Current avatar -->
                <div class="avatar-view" myRa='NaN'>
                    <div style="width: 100%">
                        <?php if($model->logo_img):?>
                            <?= Html::img(Yii::$app->params['cdnHost'].$model->logo_img,['alt' => '诊所图标','onerror'=>"this.src='{$model->logo_img}'",'class'=>'conf-img-show']) ?>
                        <?php else:?>
                            <?php
                                if($model->logo_shape == 2){
                                    echo Html::img(Yii::$app->request->baseUrl.'/public/img/common/img_logo_chang.png',['alt' => '诊所图标','class'=>'conf-img-show']);
                                }else{
                                    echo Html::img(Yii::$app->request->baseUrl.'/public/img/common/img_logo.png',['alt' => '诊所图标','class'=>'conf-img-show']);
                                }
                            ?>
                        <?php endif;?>
                    </div>
                    <div class = 'spot-config-UpImg btn btn-default  header_img ' >上传图标</div>
                </div>


                <div class="<?php echo $model->logo_img?'':'hide';?> spot-config-ImgDel">
                    <?=Html::button('删除',[
                        'class'=>' btn  font-body2  conf-img-delImg ',
                    ])?>

                </div>

            </div>
        </div>
    </div>
    <div class = 'row row-title row-title-buttom'>
        打印诊所名称
    </div>
    <div class = 'row'>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'spot_name')->textInput(['maxlength' => true]) ?>
        </div>

    </div>
    <div class = 'row row-title row-title-buttom'>
        打印电话
    </div>
    <div class = 'row'>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'pub_tel')->textInput(['maxlength' => true]) ?>
        </div>
        <div class = 'col-md-8  '>
            <span class="spot-config-tel">设置此电话会在系统打印的病历、检验检查申请单及报告、处方单、治疗单、收费退费清单上显示</span>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'label_tel')->textInput(['maxlength' => true]) ?>
        </div>
        <div class = 'col-md-8  '>
            <span class="spot-config-tel">设置此电话会在系统打印的药品标签上显示</span>
        </div>
    </div>
    <div class = 'row row-title row-title-buttom'>
        打印模板
    </div>

    <div class = 'row'>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'appointment_rebate')->dropDownList(SpotConfig::$getrebatetype) ?>
        </div>
        <div class = 'col-md-2 spot-config-underline'>

            <?= Html::a('预览'.Html::img($baseUrl.'/public/img/user/icon_view.png',['class'=>'spot-config-viewIcon']), Url::to(['rebate-img','id'=>1]),['class' => 'spot-config-viewImg', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
        </div>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'inspect_rebate')->dropDownList(SpotConfig::$getrebatetype) ?>
        </div>
        <div class = 'col-md-2 spot-config-underline'>
            <?= Html::a('预览'.Html::img($baseUrl.'/public/img/user/icon_view.png',['class'=>'spot-config-viewIcon']), Url::to(['rebate-img','id'=>2]),['class' => 'spot-config-viewImg', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'check_rebate')->dropDownList(SpotConfig::$getrebatetype) ?>
        </div>
        <div class = 'col-md-2 spot-config-underline'>
            <?= Html::a('预览'.Html::img($baseUrl.'/public/img/user/icon_view.png',['class'=>'spot-config-viewIcon']), Url::to(['rebate-img','id'=>3]),['class' => 'spot-config-viewImg', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
        </div>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'cure_rebate')->dropDownList(SpotConfig::$getrebatetype) ?>
        </div>
        <div class = 'col-md-2 spot-config-underline'>
            <?= Html::a('预览'.Html::img($baseUrl.'/public/img/user/icon_view.png',['class'=>'spot-config-viewIcon']), Url::to(['rebate-img','id'=>4]),['class' => 'spot-config-viewImg', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'recipe_rebate')->dropDownList(SpotConfig::$getRecipeRebateType) ?>
        </div>
        <div class = 'col-md-2 spot-config-underline'>
            <?= Html::a('预览'.Html::img($baseUrl.'/public/img/user/icon_view.png',['class'=>'spot-config-viewIcon']), Url::to(['rebate-img','id'=>5]),['class' => 'spot-config-viewImg recipe-rebate-url', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
        </div>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'charge_rebate')->dropDownList(SpotConfig::$getrebatetype) ?>
        </div>
        <div class = 'col-md-2 spot-config-underline'>
            <?= Html::a('预览'.Html::img($baseUrl.'/public/img/user/icon_view.png',['class'=>'spot-config-viewIcon']), Url::to(['rebate-img','id'=>6]),['class' => 'spot-config-viewImg', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::a('取消',[''],['class' => 'btn btn-cancel btn-form spot-config-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
