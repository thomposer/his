<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\patient\models\Patient;
use yii\helpers\Url;
use app\modules\follow\models\Follow;
use app\assets\AppAsset;
use app\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\follow\models\Follow */
/* @var $form yii\widgets\ActiveForm */

$baseUrl = Yii::$app->request->baseUrl;
$attribute = $patientModel->attributeLabels();
$modelAttribute = $model->attributeLabels();
if (isset($view) && $view == 1) {
    $readonly = 'readonly';
} else {
    $readonly = false;
}
if (isset($update) && $update == 1) {
    $update = 1;
} else {
    $update = 2;
}
//获取诊所所有状态正常的员工
$userInfo = User::getSpotUser();
$baseUrl = Yii::$app->request->baseUrl;
?>

<?php AppAsset::addCss($this, '@web/public/css/follow/form.css')?>
<?php $form = ActiveForm::begin(['id' => 'follow']); ?>
<div class = 'col-sm-2 col-md-2 col-custom upload-head'>
    <div id="crop-avatar">
        <!-- Current avatar -->
        <div class="avatar-view">
            <?php if ($patientModel->head_img): ?>
                <?= Html::img(Yii::$app->params['cdnHost'] . $patientModel->head_img, ['alt' => '头像', 'onerror' => 'this.src=\'' . $baseUrl . '/public/img/default.png\'']) ?>
            <?php else: ?>
                <?= Html::img(Yii::$app->request->baseUrl . '/public/img/user/img_user_big.png', ['alt' => '头像']) ?>
            <?php endif; ?>
        </div>

    </div>
    <?php if(!$model->isNewRecord): ?>
    <div class="message-info"><?= Html::a(Html::tag('i', '', ['class' => 'fa fa-commenting transform-x']) . '对话消息', ['@followIndexDialogMessage','id'=>$model->id], ['target' => '_blank']) ?></div>
    <?php endif;?>
</div>
<div class = 'col-sm-10 col-md-10'>
    <div class = 'row title_patient_div'>
        <div class = 'col-sm-12'>
            <p class="title_p">
                <span class="circle_span"></span>
                <span class="title_span">患者信息</span>
            </p>
        </div>
    </div>
    <div class = 'row'>
        <div class="row">
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'username')->textInput(['maxlength' => true, 'readonly' => true, 'autocomplete' => 'off'])->label($attribute['username'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'sex')->radioList(Patient::$getSex, ['class' => 'sex', 'itemOptions' => ['disabled' => 'disabled']])->label($attribute['sex'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'iphone')->textInput(['maxlength' => 11, 'readonly' => true])->label($attribute['iphone'] . '<span class = "label-required">*</span>') ?>            
            </div>
        </div>
        <div class="row">
            <div class = 'col-sm-4'>
                <?=
                $form->field($patientModel, 'birthTime')->textInput(['maxlength' => 11, 'readonly' => true])->label($attribute['birthday'] . '<span class = "label-required">*</span>')
                ?>
            </div>
            <div class="col-sm-4  bootstrap-timepicker">
                <?php echo $form->field($patientModel, 'hourMin')->textInput(['class' => 'form-control timepicker', 'readonly' => true]) ?>
            </div>
            <div class = 'col-sm-4'>
                <div class="form-group field-patient-patient_source">
                    <label class="control-label" for="patient-patient_source">患者来源<span class="label-required">*</span></label>
                    <input type="text" id="patient-patient_source" class="form-control" value="<?= Patient::$getPatientSource[$patientModel->patient_source] ?>" name="Patient[patient_source]" value="妈咪知道微信/微博" readonly="" prompt="请选择">
                </div>
            </div>
        </div>



        <div class = 'row title_child_div'>
            <div class = 'col-sm-12'>
                <p class="title_p">
                    <span class="circle_span"></span>
                    <span class="title_span">就诊信息</span>
                    <?php if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@patientIndexView'), $this->params['permList'])): ?>
                    <?= Html::a('查看详情 '.Html::img($baseUrl.'/public/img/user/icon_view.png'), Url::to(['@patientIndexView', 'id' => $triageInfo['patient_id'], 'recordId' => $triageInfo['record_id'], '#' => 'treatment']), ['class' => 'follow-view-more']) ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <div class="form-group field-patient-username required">
                    <label class="control-label" for="patient-username">接诊时间</label>
                    <input type="text" id="patient-username" readonly  class="form-control" name="Patient[username]" value="<?= $triageInfo['diagnosis_time'] ? date('Y-m-d H:i', $triageInfo['diagnosis_time']) : '' ?>" maxlength="64" autocomplete="off">
                </div>         
            </div>
            <div class = 'col-sm-4'>
                <div class="form-group field-patient-username required">
                    <label class="control-label" for="patient-username">接诊诊所</label>
                    <input type="text" id="patient-username" readonly  class="form-control" name="Patient[username]" value="<?= Html::encode($triageInfo['spotName']) ?>" maxlength="64" autocomplete="off">
                </div>         
            </div>
            <div class="col-sm-4">
                <div class="form-group field-patient-username required">
                    <label class="control-label" for="patient-username">接诊科室</label>
                    <input type="text" id="patient-username" readonly  class="form-control" name="Patient[username]" value="<?= Html::encode($triageInfo['departmentName']) ?>" maxlength="64" autocomplete="off">
                </div>         
            </div>
        </div>

        <div class = 'row'>
            <div class = 'col-sm-4'>
                <div class="form-group field-patient-username required">
                    <label class="control-label" for="patient-username">接诊医生</label>
                    <input type="text" id="patient-username" readonly  class="form-control" name="Patient[username]" value="<?= Html::encode($triageInfo['doctorName']) ?>" maxlength="64" autocomplete="off">
                </div>         
            </div>
            <div class = 'col-sm-4'>
                <div class="form-group field-patient-username required">
                    <label class="control-label" for="patient-username">服务类型</label>
                    <input type="text" id="patient-username" readonly  class="form-control" name="Patient[username]" value="<?= $triageInfo['type_description'] ?>" maxlength="64" autocomplete="off">
                </div>         
            </div>
        </div>

        <!--随访信息-->
        <?php // var_dump(isset($execute) || isset($view));exit;?>
        <?php echo $this->render('_followInfo', ['model' => $model, 'form' => $form, 'userInfo' => $userInfo, 'modelAttribute' => $modelAttribute, 'readonly' => isset($execute) || isset($view) == 1 ? 1 : 2, 'update' => $update]) ?>

        <!--随访执行信息-->
        <?php if ((isset($execute) && $execute == 1) || ($model->follow_state > 1 && $update != 1)): ?>
            <?php echo $this->render('_executeInfo', ['model' => $model, 'form' => $form, 'userInfo' => $userInfo, 'modelAttribute' => $modelAttribute, 'followFile' => $followFile, 'readonly' => isset($readonly) ? 1 : 2, 'view' => isset($view) ? 1 : 2]) ?>
        <?php endif; ?>

    </div>
    <?php if (!isset($view)): ?>
        <div class="row">
            <div class="form-group">
                <?= Html::a('取消', Url::to(['index']), ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
                <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form', 'id' => 'reportForm', 'data-url' => Url::to(['confirm-report']), 'contentType' => 'application/x-www-form-urlencoded', 'data-request-method' => 'post', 'processData' => 1, 'actionUrl' => $actionUrl]) ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="form-group">
                <?= Html::a('返回', Url::to(['index']), ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php ActiveForm::end(); ?>


<?php AppAsset::addScript($this, '@web/public/js/lib/common.js') ?>