<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Json;
use app\modules\outpatient\models\CureRecord;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
use yii\helpers\Url;

$this->title = '处方医嘱（退药）';
$this->params['breadcrumbs'][] = ['label' => '药房管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/patient_info.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/highRisk.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/pharmacy/pharmacy.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<div class = 'col-xs-2 col-sm-2 col-md-2' id = 'outpatient-patient-info'>

</div>
<div class="col-xs-10 col-sm-10 col-md-10">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['index']), ['class' => 'right-cancel']) ?>   
        </div>
        <div class = "box-body">    
            <?=
            $this->render('_backForm', [
                'model' => $model,
                'dataProvider' => $recipeRecordDataProvider,
                'status' => $status,
            ])
            ?>
        </div>

    </div>
</div>

<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var status = '<?= $status ?>';
    var triageInfo = <?= json_encode($triageInfo, true); ?>;
    var preUrl = '<?= Url::to(['@pharmacyIndexPrebatch']) ?>';
    var completeUrl = '<?= Url::to(['@pharmacyIndexEndbatch']); ?>';
    var recordId = '<?= $model->record_id ?>';
    var recipeData = <?= json_encode($skinTestData,true) ?>;
    var allergy = <?= json_encode($allergy, true); ?>;
    var getCureResult = <?= json_encode(CureRecord::$getCureResult)?>;
    require([baseUrl + "/public/js/pharmacy/back.js"], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>