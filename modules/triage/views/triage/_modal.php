<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Url;
use rkit\yii2\plugins\ajaxform\Asset;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
/* @var $form yii\widgets\ActiveForm */
Asset::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\triage\models\Triage */
/* @var $form yii\widgets\ActiveForm */
$selectTab = Yii::$app->request->get('selectTab');
$attribute = $model->attributeLabels();

$css = <<<CSS
    #ajaxCrudModal .modal-body {
         border-top:1px solid #ddd;
         padding: 0;
    }
    .modal-lg .modal-header, .modal-dialog .modal-header {
        border-bottom: none;
    }
   #progressWizard .tab-content {
        padding: 3px 25px 0px 25px !important;
    }
CSS;
$this->registerCss($css);
$versionNumber = Yii::getAlias("@versionNumber");
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AppAsset::addCss($this, '@web/public/css/triage/triageModal.css') ?>
<div id="progressWizard" class="basic-wizard">
    <ul class="nav nav-tabs">
        <li class="border-none active"><a href="#ptab1" data-toggle="tab" aria-expanded="true">体征测量</a></li>
        <li class="border-none"><a href="#ptab5" data-toggle="tab" aria-expanded="false">健康教育</a></li>
        <li class="border-none"><a href="#ptab4" data-toggle="tab" aria-expanded="false">过敏史</a></li>
        <li class="border-none"><a href="#ptab2" data-toggle="tab" aria-expanded="false">患者评估</a></li>
        <!--        <li class="border-none"><a href="#ptab3" data-toggle="tab" aria-expanded="false">病历报到</a></li>-->
        <li class="border-none"><a href="#ptab3" data-toggle="tab" aria-expanded="false">护理记录</a></li>


    </ul>
    <div class="tab-content step tab-content-modal" id="j_supplyInfo">
        <?php echo $this->render('_signMeasurement', ['model' => $model, 'action' => Url::to(['@triageTriageInfo'])]) ?>
        <?php echo $this->render('_healthEducation', ['healthEduModel' => $healthEduModel, 'model' => $model]) ?>
        <?php echo $this->render('_allergyOutpatient', ['allergyOutpatientModel' => $allergyOutpatientModel, 'model' => $model, 'allergyOtherInfo' => $allergyOtherInfo]) ?>
        <?php echo $this->render('_examinationAssessment', ['assessmentModel' => $assessmentModel, 'model' => $model,'childModel'=>$childModel,'painScore'=>$painScore,'fallScore'=>$fallScore]) ?>
        <?php echo $this->render('_nursingRecord', ['dataProvider' => $nursingDataProvider, 'recordId' => $model->record_id]) ?>
        <?php //echo $this->render('_caseRegistration', ['model' => $model]) ?>
        <?php // echo $this->render('_medicalHistory', ['model' => $model]) ?>


    </div>
</div>

<script type="text/template" id="triage_info_template">

    <!--<div class="row" id="allergy-list">-->
    <div class="allergy-list-modal">
    <div class="col-sm-4">
    <div class="form-group field-triageinfo-allergy1">

    <select id="triageinfo-allergy1" class="form-control select2" name="TriageInfo[allergy1][]" style="width:100%">
    <option value="">请选择过敏源</option>
    <option value="1">花生过敏</option>
    <option value="2">阿司匹林</option>
    </select>

    <div class="help-block"></div>
    </div>                        </div>
    <div class="col-sm-3">
    <div class="form-group field-triageinfo-allergy2">

    <select id="triageinfo-allergy2" class="form-control select3" name="TriageInfo[allergy2][]" style="width:100%">
    <option value="">请选择过敏反应</option>
    <option value="1">皮肤红疹</option>
    <option value="2">局部皮肤红肿、痒感</option>
    </select>

    <div class="help-block"></div>
    </div>                        </div>
    <div class="col-sm-3">
    <div class="form-group field-triageinfo-allergy3">

    <select id="triageinfo-allergy3" class="form-control select4" name="TriageInfo[allergy3][]" style="width:100%">
    <option value="">请选择过敏程度</option>
    <option value="1">轻度</option>
    <option value="2">中度</option>
    <option value="3">重度</option>
    <option value="4">非常严重</option>
    </select>

    <div class="help-block"></div>
    </div>                        </div>
    <div class="col-sm-2">
    <div class="form-group">
    <a href="javascript:void(0);" class="btn-from-delete-add-modal btn clinic-add">
    <i class="fa fa-plus"></i>
    </a>
    <a href="javascript:void(0);" class="btn-from-delete-add-modal btn clinic-delete" style="display: none;">
    <i class="fa fa-minus"></i>
    </a>
    </div>
    </div>
    </div>

</script>


<?php
$isNewRecord = $assessmentModel->isNewRecord;
$js = <<<JS
   var isNewRecord = '$isNewRecord';
   var recordId = '$recordId';
   var selectTab = '$selectTab';
JS;
$this->registerJs($js);
$this->registerJs("
        if(isNewRecord == 1){
            $('#j_tabForm_2 input[value=0]').attr({'checked' : true});
        }
        
        if(selectTab != ''){
            $('.modal-dialog').find('.nav-tabs').find('li').removeClass('active');
            $('.modal-dialog').find('a[href=\"#" . $selectTab . "\"]').parent('li').addClass('active');
            $('.modal-dialog').find(\"#" . $selectTab . "\").siblings('.tab-pane') . removeClass('active');
            $('.modal-dialog').find(\"#" . $selectTab . "\").addClass('active');
        }

        if(recordId != ''){
            $('.modal-dialog').find('.nav-tabs').find('li').removeClass('active');
            $('.modal-dialog').find('a[href=\"#ptab3\"]').parent('li').addClass('active');
            $('.modal-dialog').find(\"#ptab3\").siblings('.tab-pane') . removeClass('active');
            $('.modal-dialog').find(\"#ptab3\").addClass('active');
        }

        $('#j_tabForm_1').yiiAjaxForm({
              beforeSend: function() {
              },
              complete: function() {
              },
              success: function(data) {
              if(data.errorCode == 0){
              //$('#progressWizard').find('li.border-none').eq(1).find('a').click();
              showInfo('保存成功', '180px');
              }
           },
        }); 
        $('#j_tabForm_3').yiiAjaxForm({
	   beforeSend: function() {
	   },
	   complete: function() {
	   },
	   success: function(data) {
            if(data.errorCode == 0){
               // $('#progressWizard').find('li.border-none').eq(3).find('a').click();
               showInfo('保存成功', '180px');
            }else{
              showInfo(data.msg, '180px', 2);
             }
	   },
	});
        $('#j_tabForm_5').yiiAjaxForm({
                  beforeSend: function() {
                  },
                  complete: function() {
                  },
                  success: function(data) {
                   if(data.errorCode == 0){
                   showInfo('保存成功', '180px');

                  }
               },
       });
            $('body').on('click', \"input[name='TriageInfo[has_allergy_type]'][value=1]\", function () {
                $('#allergy-list-modal').show();
                $('.clinic-delete').hide();
            })
            $('body').on('click', \"input[name='TriageInfo[has_allergy_type]'][value=2]\", function () {
                $('#allergy-list-modal').hide();
            })
            
            if($('#triageinfo-bloodtype').val() == '' || $('#triageinfo-bloodtype').val() == '0'){
                $(\"input[type=checkbox][name='TriageInfo[blood_type_supplement][]']\").attr(\"disabled\",true);
                $(\"input[type=checkbox][name='TriageInfo[blood_type_supplement][]']\").removeAttr(\"checked\");
            }
            $('#triageinfo-bloodtype').change(function () {
                var bloodtype = '';
                bloodtype = $('#triageinfo-bloodtype').val();
                if(bloodtype == ''|| bloodtype=='0'){
                    $(\"input[type=checkbox][name='TriageInfo[blood_type_supplement][]']\").attr(\"disabled\",true);
                    $(\"input[type=checkbox][name='TriageInfo[blood_type_supplement][]']\").removeAttr(\"checked\");
                }else{
                    $(\"input[type=checkbox][name='TriageInfo[blood_type_supplement][]']\").attr(\"disabled\",false);
                }
            });
    
")?>

<?php
$js = <<<JS
   var baseUrl = '$baseUrl';
   require(["$baseUrl/public/js/triage/triageModal.js?v=$versionNumber"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>