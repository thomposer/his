<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Json;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
use app\modules\spot\models\RecipeList;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\charge\models\ChargeInfo;
use yii\helpers\Url;

$this->title = '影像学检查';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
CrudAsset::register($this);

?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/patient_info.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/tab.css')?>
<?php AppAsset::addCss($this, '@web/public/css/check/check.css')?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/preview.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
    <div class = 'col-xs-2 col-sm-2 col-md-2' id = 'outpatient-patient-info'>

    </div>
    <div class="outpatient-update col-xs-10 col-sm-10 col-md-10">
        <div class = "box">
            <div class="box-header with-border">
                <span class = 'left-title'><?= Html::encode($this->title) ?></span>
                <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
            </div>
            <div class = "box-body">

                <?= $this->render('_form', [
                    'model' => $model,
                    'status'=>$status,
                    'checkList' => $checkList,
                ]) ?>
            </div>
        </div>
    </div>


<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script type="text/javascript">
        var baseUrl = '<?= $baseUrl ?>';
        var triageInfo = <?= json_encode($triageInfo, true); ?>;
        var indexUrl = '<?= Url::to(['@checkIndexIndex' ]); ?>';
        var referrerUrl = '<?= Yii::$app->request->referrer ?>';
        var status = '<?= $status ?>';
        var allergy = <?= json_encode($allergy, true); ?>;
        require(["<?= $baseUrl ?>" + "/public/js/check/check.js?v=" + '<?= $versionNumber ?>'], function (main) {
            main.init();
        });
    </script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>