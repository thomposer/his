<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Json;
use yii\widgets\Pjax;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;

$this->params['breadcrumbs'][]=['label' => '治疗', 'url' => ['index']];
$this->title = '已完成治疗项';
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$versionNumber = Yii::getAlias("@versionNumber");
CrudAsset::register($this);
?>

<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this,'@web/public/plugins/select2/select2.min.css') ?>
<?php AppAsset::addCss($this,'@web/public/css/lib/patient_info.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/cure/cure.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/check/print.css') ?>
<?php $this->endBlock() ?>


<?php $this->beginBlock('content') ?>
<div class = 'col-xs-2 col-sm-2 col-md-2' id = 'outpatient-patient-info'>

</div>

<div class="col-xs-10 col-sm-10 col-md-10" >
    <div class="box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Yii::$app->request->referrer, ['class' => 'right-cancel']) ?>
        </div>
        <div class="box-body box-show">
            <?=
            $this->render('_form', [
                'dataProvider' => $recipeRecordDataProvider,
                'status'=>$status,
            ])
            ?>
        </div>

        <div class = "box-body hide">
            <?=
            $this->render('_printCureForm',[
                'dataProvider' => $recipeRecordDataProvider,
                'status'=>$status,
                'triageInfo' =>$triageInfo,
                'repiceInfo' => $repiceInfo,
                'soptInfo'=>$soptInfo,
                'baseUrl' => $baseUrl,
                'spotConfig' => $spotConfig
                
            ])

            ?>
        </div>
    </div>
</div>

<?php $this->endBlock() ?>


<?php $this->beginBlock('renderJs'); ?>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var status = '<?= $status ?>';
    var triageInfo = <?= json_encode($triageInfo, true); ?>;
    var dispensingUrl = '<?= Yii::$app->getRequest()->absoluteUrl ?>';
    var indexUrl = '<?= Url::to(['@cureIndexIndex' ]); ?>';
    var allergy = <?= json_encode($allergy, true); ?>;
    require(["<?= $baseUrl ?>"+"/public/js/lib/jquery-migrate-1.1.0.js"],function(){
    });

    require(["<?= $baseUrl ?>"+"/public/js/lib/jquery.jqprint-0.3.js"],function(){
    });

    require(["<?= $baseUrl ?>" + "/public/js/cure/cure.js?v="+ '<?= $versionNumber ?>'], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock(); ?>
<?php AutoLayout::end()?>
