<?php
use app\assets\AppAsset;
use yii\widgets\ActiveForm;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
//$attributeLabels = $model->attributeLabels();
?>

<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/patient/childBirth.css') ?>
<?php $this->endBlock() ?>

<?php $form = ActiveForm::begin(['id' => 'bornInfoForm', 'enableClientValidation'=>true]);
?>
<?php
echo $this->render('@bornInfoModalContentView', ['model' => $model,'form' => $form,'showBtn'=>1]);
?>
<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
   var baseUrl = '$baseUrl';
   require(["$baseUrl/public/js/outpatient/birthInfo.js?v=$versionNumber"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>

