<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\CaseTemplate;
use yii\helpers\Url;
use app\modules\outpatient\models\OrthodonticsFirstRecord;
use app\modules\outpatient\models\OrthodonticsFirstRecordFeatures;
use app\modules\outpatient\models\OrthodonticsFirstRecordExamination;
use app\modules\outpatient\models\OrthodonticsFirstRecordModelCheck;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CaseTemplate */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
$firstRecordModel = $model->getModel('firstRecord');
$firstRecordModelAttributes = $firstRecordModel->attributeLabels();

$firstRecordExaminationModel = $model->getModel('firstRecordExamination');
$firstRecordExaminationModelAttributes = $firstRecordExaminationModel->attributeLabels();

$firstRecordFeaturesModel = $model->getModel('firstRecordFeatures');
$firstRecordFeaturesModelAttributes = $firstRecordFeaturesModel->attributeLabels();

$firstRecordModelCheckModel = $model->getModel('firstRecordModelCheck');
$firstRecordModelCheckModelAttributes = $firstRecordModelCheckModel->attributeLabels();

$firstRecordTeethCheckModel = $model->getModel('firstRecordTeethCheck');
$firstRecordTeethCheckModelAttributes = $firstRecordTeethCheckModel->attributeLabels();
$retention = explode(',',$firstRecordModel->retention);
$earlyLoss = explode(',',$firstRecordModel->early_loss);

$dentalCaries = explode(',', $firstRecordTeethCheckModel->dental_caries);
$reverse = explode(',', $firstRecordTeethCheckModel->reverse);
$impacted = explode(',', $firstRecordTeethCheckModel->impacted);
$ectopic = explode(',', $firstRecordTeethCheckModel->ectopic);
$defect = explode(',', $firstRecordTeethCheckModel->defect);
$teethCheckRetention = explode(',', $firstRecordTeethCheckModel->retention);
$repairBody = explode(',', $firstRecordTeethCheckModel->repair_body);
$teethCheckOther = explode(',', $firstRecordTeethCheckModel->other);

$triageInfo = $model->getModel('triageInfo');
$triageInfoAttributes = $triageInfo->attributeLabels();
?>

<div class="case-template-form col-md-12">

    <?php $form = ActiveForm::begin([
        'id' => 'orthodonticsFirstRecord',
        'options' => ['class' => 'basic-form-content']
    ]); ?>

    <?= $form->field($firstRecordModel, 'chiefcomplaint')->textarea(['rows' => 5])->label($firstRecordModelAttributes['chiefcomplaint'].'<span class = "label-required">*</span>') ?>

    <?= $form->field($firstRecordModel, 'motivation')->textarea(['rows' => 5])->label($firstRecordModelAttributes['motivation'].'<span class = "label-required">*</span>') ?>

    <?= $form->field($firstRecordModel, 'historypresent')->textarea(['rows' => 5 ])->label($firstRecordModelAttributes['historypresent'].'<span class = "label-required">*</span>') ?>

    <?= $form->field($firstRecordModel, 'all_past_history')->textarea(['rows' => 5 ]) ?>

    <?= $form->field($firstRecordModel, 'pastdraghistory')->textarea(['rows' => 5]) ?>
	
	<?= $this->render('_allergyForm', ['model' => $firstRecordModel, 'form' => $form]) ?>

	<div class='row title-patient-div'>
		<div class='col-sm-12'>
			<p class="titleP">
				<span class="circleSpan"></span> <span class="titleSpan">口腔病史<span class = "orthodontics-title-help-block">  （手动输入牙位数填写规则：数字填写范围为1~8，字母填写范围为A~E，不可重复，最多不超过13个字）</span></span>
			</p>
		</div>
	</div>
	<div class = "row">
		
		<div class="dental-check col-sm-4">
    	    <label class="control-label dental-check-title" for="dental-check" data-value="2">滞留<i class="fa fa-pencil fa-fw dentail-content-edit"></i></label>
        	<div class="dental-check-content">
        		<div class="tooth-position">
        			<div class="left-top">
        				<span class="left-top-text"><?= $retention[0] ?></span><?= Html::input('text','', $retention[0], ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-top">
        				<span class="right-top-text"><?= $retention[1] ?></span><?= Html::input('text','', $retention[1], ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="left-bottom">
        				<span class="left-bottom-text"><?= $retention[3] ?></span><?= Html::input('text','', $retention[3], ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-bottom">
        				<span class="right-bottom-text"><?= $retention[2] ?></span><?= Html::input('text','', $retention[2], ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div style="clear: both;"></div>
        		</div>
        		<input type="hidden" id="orthodonticsfirstrecord-retention" class="dental-history-relation-position" name="OrthodonticsFirstRecord[retention]" value="<?= $firstRecordModel->retention ?>">
        		<div style="clear: both;"></div>
        	</div>
                 <div class="form-group dental-check-btn" style="display: none;margin-bottom:20px;">
                   	<div class="btn btn-cancel btn-form dental-check-btn-cancel">取消</div>
                    <div class="btn btn-default btn-form dental-check-btn-commit">保存</div>
            </div>
    	</div>
    	
    	<div class="dental-check col-sm-8">
    	    <label class="control-label dental-check-title" for="dental-check" data-value="2">早失<i class="fa fa-pencil fa-fw dentail-content-edit"></i></label>
        	<div class="dental-check-content">
        		<div class="tooth-position">
        			<div class="left-top">
        				<span class="left-top-text"><?= $earlyLoss[0] ?></span><?= Html::input('text','', $earlyLoss[0], ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-top">
        				<span class="right-top-text"><?= $earlyLoss[1] ?></span><?= Html::input('text','', $earlyLoss[1], ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="left-bottom">
        				<span class="left-bottom-text"><?= $earlyLoss[3] ?></span><?= Html::input('text','', $earlyLoss[3], ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-bottom">
        				<span class="right-bottom-text"><?= $earlyLoss[2] ?></span><?= Html::input('text','', $earlyLoss[2], ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div style="clear: both;"></div>
        		</div>
        		<input type="hidden" id="orthodonticsfirstrecord-retention" class="dental-history-relation-position" name="OrthodonticsFirstRecord[early_loss]" value="<?= $firstRecordModel->early_loss ?>">
        		<div style="clear: both;"></div>
        	</div>
                 <div class="form-group dental-check-btn" style="display: none;margin-bottom:20px;">
                   	<div class="btn btn-cancel btn-form dental-check-btn-cancel">取消</div>
                    <div class="btn btn-default btn-form dental-check-btn-commit">保存</div>
            </div>
    	</div>
		
	</div>
	
	<div class = 'row'>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordModel, 'bad_habits')->radioList(OrthodonticsFirstRecord::$getBadHabits,['class' => 'inline-label-input'])->label($firstRecordModelAttributes['bad_habits'].'<span class = "label-required">*</span>',['class' => 'inline-label-input']) ?>
		</div>
	</div>
	<div class = 'row bad-habits-abnormal-div'>
		<div class = 'col-sm-12 row'>
    		<?= $form->field($firstRecordModel, 'bad_habits_abnormal')->checkboxList(OrthodonticsFirstRecord::$getBadHabitsAbnormal)->label(false) ?>
    		<?= $form->field($firstRecordModel, 'bad_habits_abnormal_other')->textInput(['placeholder' => '请输入其他不良习惯，不超过10个字'])->label(false) ?>
		</div>
	</div>
	<div class = 'row'>
		<div class = 'col-sm-12'>
    		<?= $form->field($firstRecordModel, 'traumahistory')->textarea(['rows' => 5])->label($firstRecordModelAttributes['traumahistory'].'<span class = "label-required">*</span>') ?>
		</div>
	</div>
	
	<div class='row title-patient-div'>
		<div class='col-sm-12'>
			<p class="titleP">
				<span class="circleSpan"></span> <span class="titleSpan">先天及遗传史</span>
			</p>
		</div>
	</div>
	<div class = 'row'>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordModel, 'feed')->checkboxList(OrthodonticsFirstRecord::$getFeed,['class' => 'inline-label-input'])->label($firstRecordModelAttributes['feed'].'<span class = "label-required">*</span>',['class' => 'inline-label-input']) ?>
		</div>
		<div class = 'col-sm-12'>
    		<?= $form->field($firstRecordModel, 'immediate')->textarea(['rows' => 5])->label($firstRecordModelAttributes['immediate'].'<span class = "label-required">*</span>') ?>
		</div>
	</div>
	
	<div class='row title-patient-div'>
		<div class='col-sm-12'>
			<p class="titleP">
				<span class="circleSpan"></span> <span class="titleSpan">全身状态</span>
			</p>
		</div>
	</div>
	<div class = 'row'>
		<div class = 'col-sm-6'>
				<?= $form->field($triageInfo, 'heightcm')->textInput(['id' => 'orthodonticeHeightcm','class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin','placeholder' => '身高的值精确到小数点后一位'])->error(['class' => 'help-block radio-checkbox-margin inline-label-input'])->label($triageInfoAttributes['heightcm'],['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
		<div class = 'col-sm-6'>
				<?= $form->field($triageInfo, 'weightkg')->textInput(['id' => 'orthodonticeWeightcm','class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin','placeholder' => '体重的值精确到小数点后两位'])->error(['class' => 'help-block radio-checkbox-margin inline-label-input'])->label($triageInfoAttributes['weightkg'],['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
	</div>
			
	<div class = 'row'>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordFeaturesModel, 'dental_age')->textInput(['class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin'])->error(['class' => 'help-block radio-checkbox-margin inline-label-input'])->label($firstRecordFeaturesModelAttributes['dental_age'],['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordFeaturesModel, 'bone_age')->textInput(['class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin'])->error(['class' => 'help-block radio-checkbox-margin inline-label-input'])->label($firstRecordFeaturesModelAttributes['bone_age'],['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
	</div>
	<div class = 'row'>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordFeaturesModel, 'second_features')->radioList(OrthodonticsFirstRecordFeatures::$getSecondFeatures,['class' => 'inline-label-input radio-checkbox-margin'])->label($firstRecordFeaturesModelAttributes['second_features'],['class' => 'inline-label-input second-features-label text-right']) ?>
		</div>
	</div>
	<div class='row title-patient-div'>
		<div class='col-sm-12'>
			<p class="titleP">
				<span class="circleSpan"></span> <span class="titleSpan">颜貌</span>
			</p>
		</div>
	</div>
	
	<div class = 'row'>
		<span class="frontal-header-title">正面</span>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'frontal_type')->radioList(OrthodonticsFirstRecordFeatures::$getFrontalType,['class' => 'inline-label-input radio-checkbox-margin'])->label('<span class="frontal-title"></span>'.$firstRecordFeaturesModelAttributes['frontal_type'],['class' => 'inline-label-input']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'symmetry')->radioList(OrthodonticsFirstRecordFeatures::$getSymmetry,['class' => 'inline-label-input radio-checkbox-margin'])->label('<span class="frontal-title"></span>'.$firstRecordFeaturesModelAttributes['symmetry'],['class' => 'inline-label-input']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'abit')->radioList(OrthodonticsFirstRecordFeatures::$getAbit,['class' => 'inline-label-input radio-checkbox-margin'])->label('<span class="frontal-title"></span>'.$firstRecordFeaturesModelAttributes['abit'],['class' => 'inline-label-input']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'face')->radioList(OrthodonticsFirstRecordFeatures::$getFace,['class' => 'inline-label-input radio-checkbox-margin'])->label('<span class="frontal-title"></span><span class = "label-name-width">'.$firstRecordFeaturesModelAttributes['face'].'</span>',['class' => 'inline-label-input ']) ?>
		</div>
		<div class = 'other-need-remark col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'smile')->radioList(OrthodonticsFirstRecordFeatures::$getSmile,['class' => 'inline-label-input radio-checkbox-margin'])->label('<span class="frontal-title"></span><span class = "label-name-width">'.$firstRecordFeaturesModelAttributes['smile'].'</span>',['class' => 'inline-label-input']) ?>
		
				<?= $form->field($firstRecordFeaturesModel, 'smile_other')->textInput(['placeholder' => '请输入其他，不超过10个字','disabled' => true,'class'=>'form-control'])->label(false,['class' => 'inline-label-input']) ?>
			
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'upper_lip')->radioList(OrthodonticsFirstRecordFeatures::$getUpperLip,['class' => 'inline-label-input radio-checkbox-margin'])->label('<span class="frontal-title"></span><span class = "label-name-width">'.$firstRecordFeaturesModelAttributes['upper_lip'].'</span>',['class' => 'inline-label-input']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'lower_lip')->radioList(OrthodonticsFirstRecordFeatures::$getLowerLip,['class' => 'inline-label-input radio-checkbox-margin'])->label('<span class="frontal-title"></span><span class = "label-name-width">'.$firstRecordFeaturesModelAttributes['lower_lip'].'</span>',['class' => 'inline-label-input']) ?>
		</div>
	</div>
	
	<div class = 'row'>
		<span class="frontal-header-title">侧面</span>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'side')->radioList(OrthodonticsFirstRecordFeatures::$getSide,['class' => 'inline-label-input radio-checkbox-margin radio-checkbox-width'])->label('<span class="frontal-title"></span>'.$firstRecordFeaturesModelAttributes['side'],['class' => 'inline-label-input']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'nasolabial_angle')->radioList(OrthodonticsFirstRecordFeatures::$getNasolabialAngle,['class' => 'inline-label-input radio-checkbox-margin radio-checkbox-width'])->label('<span class="frontal-title"></span>'.$firstRecordFeaturesModelAttributes['nasolabial_angle'],['class' => 'inline-label-input']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'chin_lip')->radioList(OrthodonticsFirstRecordFeatures::$getChinLip,['class' => 'inline-label-input radio-checkbox-margin radio-checkbox-width'])->label('<span class="frontal-title"></span>'.$firstRecordFeaturesModelAttributes['chin_lip'],['class' => 'inline-label-input']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'mandibular_angle')->radioList(OrthodonticsFirstRecordFeatures::$getMandibularAngle,['class' => 'inline-label-input radio-checkbox-margin radio-checkbox-width'])->label('<span class="frontal-title"></span><span class = "label-name-width">'.$firstRecordFeaturesModelAttributes['mandibular_angle'].'</span>',['class' => 'inline-label-input']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'upper_lip_position')->radioList(OrthodonticsFirstRecordFeatures::$getUpperLipPosition,['class' => 'inline-label-input radio-checkbox-margin radio-checkbox-width'])->label('<span class="frontal-title"></span><span class = "label-name-width">'.$firstRecordFeaturesModelAttributes['upper_lip_position'].'</span>',['class' => 'inline-label-input']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'lower_lip_position')->radioList(OrthodonticsFirstRecordFeatures::$getLowerLipPosition,['class' => 'inline-label-input radio-checkbox-margin radio-checkbox-width'])->label('<span class="frontal-title"></span><span class = "label-name-width">'.$firstRecordFeaturesModelAttributes['lower_lip_position'].'</span>',['class' => 'inline-label-input']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordFeaturesModel, 'chin_position')->radioList(OrthodonticsFirstRecordFeatures::$getChinPosition,['class' => 'inline-label-input radio-checkbox-margin radio-checkbox-width'])->label('<span class="frontal-title"></span><span class = "label-name-width">'.$firstRecordFeaturesModelAttributes['chin_position'].'</span>',['class' => 'inline-label-input']) ?>
		</div>
	</div>
	
	<div class='row title-patient-div'>
		<div class='col-sm-12'>
			<p class="titleP">
				<span class="circleSpan"></span> <span class="titleSpan">功能性检查</span>
			</p>
		</div>
	</div>
	
	<div class = 'row'>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordModel, 'oral_function')->radioList(OrthodonticsFirstRecord::$getOralFunction,['class' => 'inline-label-input radio-checkbox-margin'])->label($firstRecordModelAttributes['oral_function'].'<span class = "label-required">*</span>',['class' => 'inline-label-input']) ?>
		</div>
	</div>
	<div class = 'row bad-habits-abnormal-div'>
		<div class = 'col-sm-10'>
    		<?= $form->field($firstRecordModel, 'oral_function_abnormal')->checkboxList(OrthodonticsFirstRecord::$getOralFunctionAbnormal,['class' => 'radio-checkbox-margin'])->label(false) ?>
		</div>
		
	</div>
	<div class = 'row'>
		<div class = 'mandibular-movement-div'>
			<?= $form->field($firstRecordModel, 'mandibular_movement')->radioList(OrthodonticsFirstRecord::$getMandibularMovement,['class' => 'inline-label-input radio-checkbox-margin'])->label($firstRecordModelAttributes['mandibular_movement'].'<span class = "label-required">*</span>',['class' => 'inline-label-input']) ?>
    	</div>
    	<div class = 'other-remark' style="margin-left:-60px;">
    			<?= $form->field($firstRecordModel, 'mandibular_movement_abnormal')->textInput(['class' => 'form-control','placeholder' => '请输入下颌运动异常内容，不超过30个字','disabled' => true])->label(false,['class' => 'inline-label-input']) ?>
    			
    	</div>
	</div>
	<div class = 'row'>
		<div class = 'mandibular-movement-div'>
			<?= $form->field($firstRecordModel, 'mouth_open')->radioList(OrthodonticsFirstRecord::$getMouthOpen,['class' => 'inline-label-input radio-checkbox-margin'])->label($firstRecordModelAttributes['mouth_open'].'<span class = "label-required">*</span>',['class' => 'inline-label-input  text-right']) ?>
    	</div>
    	<div class = 'other-remark' style="margin-left:-60px;">
    			<?= $form->field($firstRecordModel, 'mouth_open_abnormal')->textInput(['class' => 'form-control','placeholder' => '请输入张口度异常内容，不超过30个字','disabled' => true])->label(false,['class' => 'inline-label-input']) ?>
    			
    	</div>
	</div>
	<div class = 'row'>
		<div class = 'col-sm-12'>
			颞下颌关节 ：
		</div>
	</div>
	<div class = 'row'>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordModel, 'left_temporomandibular_joint')->radioList(OrthodonticsFirstRecord::$getLeftTemporomandibularJoint,['class' => 'inline-label-input radio-checkbox-margin'])->label('左<span class = "label-required">*</span>',['class' => 'inline-label-input text-right']) ?>
		</div>
	</div>
	<div class = 'row temporomandibular-joint-abnormal-div' id = 'left-temporomandibular-joint-abnormal'>
		<div class = 'col-sm-10'>
    		<?= $form->field($firstRecordModel, 'left_temporomandibular_joint_abnormal')->checkboxList(OrthodonticsFirstRecord::$getLeftTemporomandibularJointAbnormal,['class' => 'radio-checkbox-margin'])->label(false) ?>
    		<?= $form->field($firstRecordModel, 'left_temporomandibular_joint_abnormal_other')->textInput(['class' => 'form-control','placeholder' => '请输入其他异常内容，不超过30个字'])->label(false) ?>
		</div>
		
	</div>
	<div class = 'row'>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordModel, 'right_temporomandibular_joint')->radioList(OrthodonticsFirstRecord::$getRightTemporomandibularJoint,['class' => 'inline-label-input radio-checkbox-margin'])->label('右<span class = "label-required">*</span>',['class' => 'inline-label-input text-right']) ?>
		</div>
	</div>
	<div class = 'row temporomandibular-joint-abnormal-div' id = 'right-temporomandibular-joint-abnormal'>
		<div class = 'col-sm-10'>
    		<?= $form->field($firstRecordModel, 'right_temporomandibular_joint_abnormal')->checkboxList(OrthodonticsFirstRecord::$getRightTemporomandibularJointAbnormal,['class' => 'radio-checkbox-margin'])->label(false) ?>
    		<?= $form->field($firstRecordModel, 'right_temporomandibular_joint_abnormal_other')->textInput(['class' => 'form-control','placeholder' => '请输入其他异常内容，不超过30个字'])->label(false) ?>
		</div>
		
	</div>
	
	<div class='row title-patient-div'>
		<div class='col-sm-12'>
			<p class="titleP">
				<span class="circleSpan"></span> <span class="titleSpan">口腔组织检查</span>
			</p>
		</div>
	</div>
	<?= $form->field($firstRecordExaminationModel, 'hygiene')->textarea(['rows' => 5 ]) ?>
    <?= $form->field($firstRecordExaminationModel, 'periodontal')->textarea(['rows' => 5 ]) ?>
    <?= $form->field($firstRecordExaminationModel, 'ulcer')->textarea(['rows' => 5 ]) ?>
    <?= $form->field($firstRecordExaminationModel, 'gums')->textarea(['rows' => 5 ]) ?>
    <?= $form->field($firstRecordExaminationModel, 'tonsil')->textarea(['rows' => 5 ]) ?>
    <?= $form->field($firstRecordExaminationModel, 'frenum')->textarea(['rows' => 5 ]) ?>
    <?= $form->field($firstRecordExaminationModel, 'soft_palate')->textarea(['rows' => 5 ]) ?>
    <?= $form->field($firstRecordExaminationModel, 'lip')->textarea(['rows' => 5 ]) ?>
    <?= $form->field($firstRecordExaminationModel, 'tongue')->textarea(['rows' => 5 ]) ?>
	<div class = 'row'>
    	<div class = 'col-sm-12'>
    			<?= $form->field($firstRecordExaminationModel, 'dentition')->radioList(OrthodonticsFirstRecordExamination::$getDentition,['class' => 'inline-label-input radio-checkbox-margin radio-checkbox-width'])->label('<span class = "label-name-width-70 text-right">'.$firstRecordExaminationModelAttributes['dentition'].'</span>',['class' => 'inline-label-input']) ?>
    	</div>
    	<div class = 'col-sm-12'>
    			<?= $form->field($firstRecordExaminationModel, 'arch_form')->radioList(OrthodonticsFirstRecordExamination::$getArchForm,['class' => 'inline-label-input radio-checkbox-margin radio-checkbox-width'])->label('<span class = "label-name-width-70 text-right">'.$firstRecordExaminationModelAttributes['arch_form'].'</span>',['class' => 'inline-label-input']) ?>
    	</div>
    	<div class = 'col-sm-12'>
    			<?= $form->field($firstRecordExaminationModel, 'arch_coordination')->radioList(OrthodonticsFirstRecordExamination::$getArchCoordination,['class' => 'inline-label-input radio-checkbox-margin radio-checkbox-width'])->label('<span class = "label-name-width-70">'.$firstRecordExaminationModelAttributes['arch_coordination'].'</span>',['class' => 'inline-label-input']) ?>
    	</div>
	</div>
	<p class = 'title-patient-div'>覆合</p>
	<div class = 'row'>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordExaminationModel, 'overbite_anterior_teeth')->radioList(OrthodonticsFirstRecordExamination::$getOverbiteAnteriorTeeth,['class' => 'inline-label-input radio-checkbox-margin'])->label('前牙<span class = "label-required">*</span>',['class' => 'inline-label-input label-name-width-70 text-right']) ?>
		</div>
	</div>
	<div class = 'row temporomandibular-joint-abnormal-div ' id = 'overbite-anterior-teeth-abnormal'>
		<div class = 'col-sm-10'>
    		<?= $form->field($firstRecordExaminationModel, 'overbite_anterior_teeth_abnormal')->checkboxList(OrthodonticsFirstRecordExamination::$getOverbiteAnteriorTeethAbnormal,['class' => 'radio-checkbox-margin'])->label(false) ?>
    		<?= $form->field($firstRecordExaminationModel, 'overbite_anterior_teeth_other')->textInput(['class' => 'form-control','placeholder' => '请输入其他异常内容，不超过30个字'])->label(false) ?>
		</div>
		
	</div>
	<div class = 'row'>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordExaminationModel, 'overbite_posterior_teeth')->radioList(OrthodonticsFirstRecordExamination::$getOverbitePosteriorTeeth,['class' => 'inline-label-input radio-checkbox-margin'])->label('后牙<span class = "label-required">*</span>',['class' => 'inline-label-input label-name-width-70 text-right']) ?>
		</div>
	</div>
	<div class = 'row temporomandibular-joint-abnormal-div' id = 'overbite-posterior-teeth-abnormal'>
		<div class = 'col-sm-10'>
    		<?= $form->field($firstRecordExaminationModel, 'overbite_posterior_teeth_abnormal')->checkboxList(OrthodonticsFirstRecordExamination::$getOverbitePosteriorTeethAbnormal,['class' => 'radio-checkbox-margin'])->label(false) ?>
    		<?= $form->field($firstRecordExaminationModel, 'overbite_posterior_teeth_other')->textInput(['class' => 'form-control','placeholder' => '请输入其他异常内容，不超过30个字'])->label(false) ?>
		</div>
		
	</div>
	<div>覆盖</div>
	<div class = 'row'>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordExaminationModel, 'cover_anterior_teeth')->radioList(OrthodonticsFirstRecordExamination::$getCoverAnteriorTeeth,['class' => 'inline-label-input radio-checkbox-margin'])->label('前牙<span class = "label-required">*</span>',['class' => 'inline-label-input label-name-width-70 text-right']) ?>
		</div>
	</div>
	<div class = 'row temporomandibular-joint-abnormal-div' id = 'cover-anterior-teeth-abnormal'>
		<div class = 'col-sm-8'>
    		<?= $form->field($firstRecordExaminationModel, 'cover_anterior_teeth_abnormal')->checkboxList(OrthodonticsFirstRecordExamination::$getCoverAnteriorTeethAbnormal,['class' => 'radio-checkbox-margin'])->label(false) ?>
		</div>
		
	</div>
	<div class = 'row'>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordExaminationModel, 'cover_posterior_teeth')->radioList(OrthodonticsFirstRecordExamination::$getCoverPosteriorTeeth,['class' => 'inline-label-input radio-checkbox-margin'])->label('后牙<span class = "label-required">*</span>',['class' => 'inline-label-input label-name-width-70 text-right']) ?>
		</div>
	</div>
	<div class = 'row temporomandibular-joint-abnormal-div ' id = 'cover-posterior-teeth-abnormal'>
		<div class = 'col-sm-8'>
    		<?= $form->field($firstRecordExaminationModel, 'cover_posterior_teeth_abnormal')->checkboxList(OrthodonticsFirstRecordExamination::$getCoverPosteriorTeethAbnormal,['class' => 'radio-checkbox-margin'])->label(false) ?>
		</div>
		
	</div>
	<div class = 'title-patient-div'>咬合关系</div>
	<div class = 'row'>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordExaminationModel, 'left_canine')->radioList(OrthodonticsFirstRecordExamination::$getLeftCanine,['class' => 'inline-label-input radio-checkbox-margin'])->label($firstRecordExaminationModelAttributes['left_canine'].'<span class = "label-required">*</span>',['class' => 'inline-label-input label-name-width-70 text-right']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordExaminationModel, 'right_canine')->radioList(OrthodonticsFirstRecordExamination::$getRightCanine,['class' => 'inline-label-input radio-checkbox-margin'])->label($firstRecordExaminationModelAttributes['right_canine'].'<span class = "label-required">*</span>',['class' => 'inline-label-input  label-name-width-70 text-right']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordExaminationModel, 'left_molar')->radioList(OrthodonticsFirstRecordExamination::$getLeftMolar,['class' => 'inline-label-input radio-checkbox-margin'])->label($firstRecordExaminationModelAttributes['left_molar'].'<span class = "label-required">*</span>',['class' => 'inline-label-input label-name-width-70 text-right']) ?>
		</div>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordExaminationModel, 'right_molar')->radioList(OrthodonticsFirstRecordExamination::$getRightMolar,['class' => 'inline-label-input radio-checkbox-margin'])->label($firstRecordExaminationModelAttributes['right_molar'].'<span class = "label-required">*</span>',['class' => 'inline-label-input label-name-width-70 text-right']) ?>
		</div>
		
	</div>
	<div class = 'row'>
		<div class = 'mandibular-movement-div margin-midline-teeth'>
			<?= $form->field($firstRecordExaminationModel, 'midline_teeth')->radioList(OrthodonticsFirstRecordExamination::$getMidlineTeeth,['class' => 'inline-label-input radio-checkbox-margin'])->label($firstRecordExaminationModelAttributes['midline_teeth'].'<span class = "label-required">*</span>',['class' => 'inline-label-input label-name-width-70 text-right']) ?>
    	</div>
    	<div class = 'other-remark'>
    			<?= $form->field($firstRecordExaminationModel, 'midline_teeth_value')->textInput(['class'=>'form-control','placeholder' => '请输入左偏/右偏数值，单位为mm'])->label(false,['class' => 'inline-label-input']) ?>
    			
    	</div>
	</div>
	<div class = 'row'>
		<div class = 'mandibular-movement-div margin-midline-teeth'>
			<?= $form->field($firstRecordExaminationModel, 'midline')->radioList(OrthodonticsFirstRecordExamination::$getMidline,['class' => 'inline-label-input radio-checkbox-margin'])->label($firstRecordExaminationModelAttributes['midline'].'<span class = "label-required">*</span>',['class' => 'inline-label-input label-name-width-70 text-right']) ?>
    	</div>
    	<div class = 'other-remark'>
    			<?= $form->field($firstRecordExaminationModel, 'midline_value')->textInput(['class'=>'form-control','placeholder' => '请输入左偏/右偏数值，单位为mm'])->label(false,['class' => 'inline-label-input']) ?>
    			
    	</div>
	</div>
	<div class='row title-patient-div'>
		<div class='col-sm-12'>
			<p class="titleP">
				<span class="circleSpan"></span> <span class="titleSpan">模型检查</span>
			</p>
		</div>
	</div>
	<p class = 'title-patient-div'>拥挤度</p>
	<div class = 'row'>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordModelCheckModel, 'crowded_maxillary')->textInput(['placeholder' => '单位为mm','class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin'])->error(['class' => 'inline-label-input radio-checkbox-margin help-block'])->label($firstRecordModelCheckModelAttributes['crowded_maxillary'].'<span class ="label-required">*</span>',['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordModelCheckModel, 'crowded_mandible')->textInput(['placeholder' => '单位为mm','class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin'])->error(['class' => 'help-block inline-label-input radio-checkbox-margin'])->label($firstRecordModelCheckModelAttributes['crowded_mandible'].'<span class ="label-required">*</span>',['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
	</div>
	<p class = 'title-patient-div'>尖牙区宽度</p>
	<div class = 'row'>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordModelCheckModel, 'canine_maxillary')->textInput(['placeholder' => '单位为mm','class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin'])->error(['class' => 'help-block inline-label-input radio-checkbox-margin'])->label($firstRecordModelCheckModelAttributes['canine_maxillary'].'<span class ="label-required">*</span>',['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordModelCheckModel, 'canine_mandible')->textInput(['placeholder' => '单位为mm','class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin'])->error(['class' => 'help-block inline-label-input radio-checkbox-margin'])->label($firstRecordModelCheckModelAttributes['canine_mandible'].'<span class ="label-required">*</span>',['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
	</div>
	<p class = 'title-patient-div'>磨牙区宽度</p>
	<div class = 'row'>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordModelCheckModel, 'molar_maxillary')->textInput(['placeholder' => '单位为mm','class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin'])->error(['class' => 'help-block inline-label-input radio-checkbox-margin'])->label($firstRecordModelCheckModelAttributes['molar_maxillary'].'<span class ="label-required">*</span>',['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordModelCheckModel, 'molar_mandible')->textInput(['placeholder' => '单位为mm','class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin'])->error(['class' => 'help-block inline-label-input radio-checkbox-margin'])->label($firstRecordModelCheckModelAttributes['molar_mandible'].'<span class ="label-required">*</span>',['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
	</div>
	<div class = 'row'>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordModelCheckModel, 'spee_curve')->textInput(['placeholder' => '单位为mm','class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin'])->error(['class' => 'help-block inline-label-input radio-checkbox-margin'])->label($firstRecordModelCheckModelAttributes['spee_curve'].'<span class ="label-required">*</span>',['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
	</div>
	<div class = 'row'>
		<div class = 'col-sm-12'>
				<?= $form->field($firstRecordModelCheckModel, 'transversal_curve')->radioList(OrthodonticsFirstRecordModelCheck::$getTransversalCurve,['class' => 'inline-label-input radio-checkbox-margin'])->label($firstRecordModelCheckModelAttributes['transversal_curve'].'<span class = "label-required">*</span>',['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
	</div>
	<p class = 'title-patient-div'>bolton比值</p>
	<div class = 'row'>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordModelCheckModel, 'bolton_nterior_teeth')->textInput(['class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin'])->error(['class' => 'help-block inline-label-input radio-checkbox-margin'])->label($firstRecordModelCheckModelAttributes['bolton_nterior_teeth'].'<span class ="label-required">*</span>',['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
		<div class = 'col-sm-6'>
				<?= $form->field($firstRecordModelCheckModel, 'bolton_all_teeth')->textInput(['class' => 'form-control inline-label-input inline-text-input-width radio-checkbox-margin'])->error(['class' => 'help-block inline-label-input radio-checkbox-margin'])->label($firstRecordModelCheckModelAttributes['bolton_all_teeth'].'<span class ="label-required">*</span>',['class' => 'inline-label-input inline-text-input-width-label']) ?>
		</div>
	</div>
	<div class = 'row'>
		<div class = 'col-sm-12'>
			    <?= $form->field($firstRecordModelCheckModel, 'examination')->textarea(['rows' => 5])->label($firstRecordModelCheckModelAttributes['examination'].'<span class = "label-required">*</span>') ?>
		</div>
	</div>
	
	<div class='row title-patient-div'>
		<div class='col-sm-12'>
			<p class="titleP">
				<span class="circleSpan"></span> <span class="titleSpan">牙齿检查<span class = "orthodontics-title-help-block">  （手动输入牙位数填写规则：数字填写范围为1~8，字母填写范围为A~E，不可重复，最多不超过13个字）</span></span>
			</p>
		</div>
	</div>
	<div class = "row">
		
		<div class="dental-check col-sm-4">
    	    <label class="control-label dental-check-title" for="dental-check" data-value="2">龋齿<i class="fa fa-pencil fa-fw dentail-content-edit"></i></label>
        	<div class="dental-check-content">
        		<div class="tooth-position">
        			<div class="left-top">
        				<span class="left-top-text"><?= $dentalCaries[0] ?></span><?= Html::input('text','', $dentalCaries[0], ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-top">
        				<span class="right-top-text"><?= $dentalCaries[1] ?></span><?= Html::input('text','', $dentalCaries[1], ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="left-bottom">
        				<span class="left-bottom-text"><?= $dentalCaries[3] ?></span><?= Html::input('text','', $dentalCaries[3], ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-bottom">
        				<span class="right-bottom-text"><?= $dentalCaries[2] ?></span><?= Html::input('text','', $dentalCaries[2], ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div style="clear: both;"></div>
        		</div>
        		<input type="hidden" id="orthodonticsfirstrecord-retention" class="dental-history-relation-position" name="OrthodonticsFirstRecordTeethCheck[dental_caries]" value="<?= $firstRecordTeethCheckModel->dental_caries ?>">
        		<div style="clear: both;"></div>
        	</div>
                 <div class="form-group dental-check-btn" style="display: none;margin-bottom:20px;">
                   	<div class="btn btn-cancel btn-form dental-check-btn-cancel">取消</div>
                    <div class="btn btn-default btn-form dental-check-btn-commit">保存</div>
            </div>
    	</div>
    	<div class="dental-check col-sm-4">
    	    <label class="control-label dental-check-title" for="dental-check" data-value="2">扭转<i class="fa fa-pencil fa-fw dentail-content-edit"></i></label>
        	<div class="dental-check-content">
        		<div class="tooth-position">
        			<div class="left-top">
        				<span class="left-top-text"><?= $reverse[0] ?></span><?= Html::input('text','', $reverse[0], ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-top">
        				<span class="right-top-text"><?= $reverse[1] ?></span><?= Html::input('text','', $reverse[1], ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="left-bottom">
        				<span class="left-bottom-text"><?= $reverse[3] ?></span><?= Html::input('text','', $reverse[3], ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-bottom">
        				<span class="right-bottom-text"><?= $reverse[2] ?></span><?= Html::input('text','', $reverse[2], ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div style="clear: both;"></div>
        		</div>
        		<input type="hidden" id="orthodonticsfirstrecord-retention" class="dental-history-relation-position" name="OrthodonticsFirstRecordTeethCheck[reverse]" value="<?= $firstRecordTeethCheckModel->reverse ?>">
        		<div style="clear: both;"></div>
        	</div>
                 <div class="form-group dental-check-btn" style="display: none;margin-bottom:20px;">
                   	<div class="btn btn-cancel btn-form dental-check-btn-cancel">取消</div>
                    <div class="btn btn-default btn-form dental-check-btn-commit">保存</div>
            </div>
    	</div>
    	<div class="dental-check col-sm-4">
    	    <label class="control-label dental-check-title" for="dental-check" data-value="2">阻生<i class="fa fa-pencil fa-fw dentail-content-edit"></i></label>
        	<div class="dental-check-content">
        		<div class="tooth-position">
        			<div class="left-top">
        				<span class="left-top-text"><?= $impacted[0] ?></span><?= Html::input('text','', $impacted[0], ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-top">
        				<span class="right-top-text"><?= $impacted[1] ?></span><?= Html::input('text','', $impacted[1], ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="left-bottom">
        				<span class="left-bottom-text"><?= $impacted[3] ?></span><?= Html::input('text','', $impacted[3], ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-bottom">
        				<span class="right-bottom-text"><?= $impacted[2] ?></span><?= Html::input('text','', $impacted[2], ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div style="clear: both;"></div>
        		</div>
        		<input type="hidden" id="orthodonticsfirstrecord-retention" class="dental-history-relation-position" name="OrthodonticsFirstRecordTeethCheck[impacted]" value="<?= $firstRecordTeethCheckModel->impacted ?>">
        		<div style="clear: both;"></div>
        	</div>
                 <div class="form-group dental-check-btn" style="display: none;margin-bottom:20px;">
                   	<div class="btn btn-cancel btn-form dental-check-btn-cancel">取消</div>
                    <div class="btn btn-default btn-form dental-check-btn-commit">保存</div>
            </div>
    	</div>
		
	</div>
	
	<div class = "row">
		
		<div class="dental-check col-sm-4">
    	    <label class="control-label dental-check-title" for="dental-check" data-value="2">异位<i class="fa fa-pencil fa-fw dentail-content-edit"></i></label>
        	<div class="dental-check-content">
        		<div class="tooth-position">
        			<div class="left-top">
        				<span class="left-top-text"><?= $ectopic[0] ?></span><?= Html::input('text','', $ectopic[0], ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-top">
        				<span class="right-top-text"><?= $ectopic[1] ?></span><?= Html::input('text','', $ectopic[1], ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="left-bottom">
        				<span class="left-bottom-text"><?= $ectopic[3] ?></span><?= Html::input('text','', $ectopic[3], ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-bottom">
        				<span class="right-bottom-text"><?= $ectopic[2] ?></span><?= Html::input('text','', $ectopic[2], ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div style="clear: both;"></div>
        		</div>
        		<input type="hidden" id="orthodonticsfirstrecord-retention" class="dental-history-relation-position" name="OrthodonticsFirstRecordTeethCheck[ectopic]" value="<?= $firstRecordTeethCheckModel->ectopic ?>">
        		<div style="clear: both;"></div>
        	</div>
                 <div class="form-group dental-check-btn" style="display: none;margin-bottom:20px;">
                   	<div class="btn btn-cancel btn-form dental-check-btn-cancel">取消</div>
                    <div class="btn btn-default btn-form dental-check-btn-commit">保存</div>
            </div>
    	</div>

    	<div class="dental-check col-sm-4">
    	    <label class="control-label dental-check-title" for="dental-check" data-value="2">缺失<i class="fa fa-pencil fa-fw dentail-content-edit"></i></label>
        	<div class="dental-check-content">
        		<div class="tooth-position">
        			<div class="left-top">
        				<span class="left-top-text"><?= $defect[0] ?></span><?= Html::input('text','', $defect[0], ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-top">
        				<span class="right-top-text"><?= $defect[1] ?></span><?= Html::input('text','', $defect[1], ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="left-bottom">
        				<span class="left-bottom-text"><?= $defect[3] ?></span><?= Html::input('text','', $defect[3], ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-bottom">
        				<span class="right-bottom-text"><?= $defect[2] ?></span><?= Html::input('text','', $defect[2], ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div style="clear: both;"></div>
        		</div>
        		<input type="hidden" id="orthodonticsfirstrecord-retention" class="dental-history-relation-position" name="OrthodonticsFirstRecordTeethCheck[defect]" value="<?= $firstRecordTeethCheckModel->defect ?>">
        		<div style="clear: both;"></div>
        	</div>
                 <div class="form-group dental-check-btn" style="display: none;margin-bottom:20px;">
                   	<div class="btn btn-cancel btn-form dental-check-btn-cancel">取消</div>
                    <div class="btn btn-default btn-form dental-check-btn-commit">保存</div>
            </div>
    	</div>
    	<div class="dental-check col-sm-4">
    	    <label class="control-label dental-check-title" for="dental-check" data-value="2">滞留<i class="fa fa-pencil fa-fw dentail-content-edit"></i></label>
        	<div class="dental-check-content">
        		<div class="tooth-position">
        			<div class="left-top">
        				<span class="left-top-text"><?= $teethCheckRetention[0] ?></span><?= Html::input('text','', $teethCheckRetention[0], ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-top">
        				<span class="right-top-text"><?= $teethCheckRetention[1] ?></span><?= Html::input('text','', $teethCheckRetention[1], ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="left-bottom">
        				<span class="left-bottom-text"><?= $teethCheckRetention[3] ?></span><?= Html::input('text','', $teethCheckRetention[3], ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-bottom">
        				<span class="right-bottom-text"><?= $teethCheckRetention[2] ?></span><?= Html::input('text','', $teethCheckRetention[2], ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div style="clear: both;"></div>
        		</div>
        		<input type="hidden" id="orthodonticsfirstrecord-retention" class="dental-history-relation-position" name="OrthodonticsFirstRecordTeethCheck[retention]" value="<?=  $firstRecordTeethCheckModel->retention ?>">
        		<div style="clear: both;"></div>
        	</div>
                 <div class="form-group dental-check-btn" style="display: none;margin-bottom:20px;">
                   	<div class="btn btn-cancel btn-form dental-check-btn-cancel">取消</div>
                    <div class="btn btn-default btn-form dental-check-btn-commit">保存</div>
            </div>
    	</div>
		
	</div>
	<div class = "row">
		
		<div class="dental-check col-sm-4">
    	    <label class="control-label dental-check-title" for="dental-check" data-value="2">修复体<i class="fa fa-pencil fa-fw dentail-content-edit"></i></label>
        	<div class="dental-check-content">
        		<div class="tooth-position">
        			<div class="left-top">
        				<span class="left-top-text"><?= $repairBody[0] ?></span><?= Html::input('text','', $repairBody[0], ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-top">
        				<span class="right-top-text"><?= $repairBody[1] ?></span><?= Html::input('text','', $repairBody[1], ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="left-bottom">
        				<span class="left-bottom-text"><?= $repairBody[3] ?></span><?= Html::input('text','', $repairBody[3], ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-bottom">
        				<span class="right-bottom-text"><?= $repairBody[2] ?></span><?= Html::input('text','', $repairBody[2], ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div style="clear: both;"></div>
        		</div>
        		<input type="hidden" id="orthodonticsfirstrecord-retention" class="dental-history-relation-position" name="OrthodonticsFirstRecordTeethCheck[repair_body]" value="<?= $firstRecordTeethCheckModel->repair_body ?>">
        		<div style="clear: both;"></div>
        	</div>
                 <div class="form-group dental-check-btn" style="display: none;margin-bottom:20px;">
                   	<div class="btn btn-cancel btn-form dental-check-btn-cancel">取消</div>
                    <div class="btn btn-default btn-form dental-check-btn-commit">保存</div>
            </div>
    	</div>
    	<div class="dental-check col-sm-3">
    	    <label class="control-label dental-check-title" for="dental-check" data-value="2">其他<i class="fa fa-pencil fa-fw dentail-content-edit"></i></label>
        	<div class="dental-check-content">
        		<div class="tooth-position">
        			<div class="left-top">
        				<span class="left-top-text"><?= $teethCheckOther[0] ?></span><?= Html::input('text','', $teethCheckOther[0], ['class' => 'left-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-top">
        				<span class="right-top-text"><?= $teethCheckOther[1] ?></span><?= Html::input('text','', $teethCheckOther[1], ['class' => 'right-top-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="left-bottom">
        				<span class="left-bottom-text"><?= $teethCheckOther[3] ?></span><?= Html::input('text','', $teethCheckOther[3], ['class' => 'left-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div class="right-bottom">
        				<span class="right-bottom-text"><?= $teethCheckOther[2] ?></span><?= Html::input('text','', $teethCheckOther[2], ['class' => 'right-bottom-input dental-check-input', 'style' => 'display: none;', 'data-value' => '1']) ?></div>
        			<div style="clear: both;"></div>
        		</div>
<!--         		<textarea class="other-remark-teeth-check form-control" name="OrthodonticsFirstRecordTeethCheck[other_remark]" rows="5" maxlength="100" placeholder = "请输入其他异常情况，不超过100字"></textarea> -->
<!--         		<div class = 'help-block red'></div> -->
        		<input type="hidden" id="orthodonticsfirstrecord-retention" class="dental-history-relation-position" name="OrthodonticsFirstRecordTeethCheck[other]" value="<?= $firstRecordTeethCheckModel->other ?>">
        		<div style="clear: both;"></div>
        	</div>
                 <div class="form-group dental-check-btn" style="display: none;margin-bottom:20px;">
                   	<div class="btn btn-cancel btn-form dental-check-btn-cancel">取消</div>
                    <div class="btn btn-default btn-form dental-check-btn-commit">保存</div>
            </div>
    	</div>
		<div class = 'col-sm-4'>
			<?= $form->field($firstRecordTeethCheckModel, 'other_remark')->textarea(['rows' => 5,'placeholder' => '请输入其他异常情况，不超过100字'])->label('') ?>
		</div>
	</div>
	<p class = 'title-patient-div'></p>
    <!-- 初步诊断 -->
     <div class="form-group">
         <label class="control-label" for="checkrecord-check_id">初步诊断<span style="color:#FF5000;">（若需要给患者开检验，检查，治疗，处方医嘱，请务必填写初步诊断）</span></label>
         <?= $this->render('_firstCheckForm', ['form' => $form,'firstCheckDataProvider'=>$firstCheckDataProvider]) ?>
                <!-- 下拉选择 -->
     </div>	
	<?= $form->field($firstRecordTeethCheckModel, 'orthodontic_target')->textarea(['rows' => 5])->label($firstRecordTeethCheckModelAttributes['orthodontic_target'].'<span class = "label-required">*</span>') ?>
	<?= $form->field($firstRecordTeethCheckModel, 'cure')->textarea(['rows' => 5])->label($firstRecordTeethCheckModelAttributes['cure'].'<span class = "label-required">*</span>') ?>
	<?= $form->field($firstRecordTeethCheckModel, 'special_risk')->textarea(['rows' => 5])->label($firstRecordTeethCheckModelAttributes['special_risk'].'<span class = "label-required">*</span>') ?>

	<div>
        <label class="control-label" for="upload-mediaFile">上传附件</label>
        <?= $this->render('_fileUpload', ['model' => $firstRecordTeethCheckModel, 'medicalFile' => $medicalFile]) ?>
   </div>
	
	
	<div class="form-group">
		<?php if($firstRecordModel->isNewRecord):?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']).Html::submitButton('保存', ['class' => 'btn btn-default btn-form reocrd-btn-custom btn-fixed']) ?>
        <?php else: ?>
        <?= Html::button('修改', ['class' => 'btn btn-default btn-form reocrd-btn-custom']).Html::button('修改', ['class' => 'btn btn-default btn-form reocrd-btn-custom btn-fixed']) ?>
        <?= Html::button('打印病历', ['class' => 'btn btn-default btn-form pull-right print-orthodontics-record','style'=>'margin-right:0 !important;', 'name' => 'teethPrint' .$_GET['id']]); ?>
        <?php endif;?>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div id="teeth-print" class="hide"></div>

<?php
$js = <<<JS
    require(["$baseUrl/public/js/outpatient/orthodonticsFirstRecord.js"], function (main) {
        main.init();
        window.orthodonticsJs = main;
    });
JS;
$this->registerJs($js);
?>
