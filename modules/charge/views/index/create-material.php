<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\Charge */
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;
use app\modules\charge\models\ChargeInfo;
use app\common\Common;
use rkit\yii2\plugins\ajaxform\Asset;

CrudAsset::register($this);

//CrudAsset::register($this);
$this->title = '新增收费';
$this->params['breadcrumbs'][] = ['label' => '收费', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/dist/css/cropper.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/upload.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/timepicker/bootstrap-timepicker.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/create.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<div class="charge-create col-xs-12">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
<?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', ['index'], ['class' => 'right-cancel']) ?>
        </div>
        <div class = "box-body charge-body">    
            <?=
            $this->render('_patientForm', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'list' => $list,
                'materialTotal' => $materialTotal
            ])
            ?>

        </div>  
    </div>

</div>

<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<script type="text/javascript">
    var baseUrl = "<?= $baseUrl ?>";
    var materialList = <?= json_encode($list, true) ?>;
    var uploadUrl = '<?= Url::to(['@manageSitesUpload']); ?>';
    var getPatients = '<?= Url::to(['@patientPatientGetPatients']); ?>';
    var getIphone = '<?= Url::to(['@apiPatientGetIphone']); ?>';
    var createUrl = '<?= Url::to(['@chargeIndexCreateMaterial']) ?>';
    var indexUrl = '<?= Url::to(['@chargeIndexIndex']) ?>';
    var materialTotal = <?= json_encode($materialTotal,true) ?>;
    require(["<?= $baseUrl ?>" + "/public/js/charge/createMaterial.js"], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>