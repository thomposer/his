<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\assets\AppAsset;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\CureList;
use app\modules\outpatient\models\RecipeRecord;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CaseTemplate */

$this->title = '新增影像学检查模板';
$this->params['breadcrumbs'][] = ['label' => '医生门诊', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '影像学检查模板', 'url' => ['check-template-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/form.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'), ['type' => 2]) ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div class="case-template-create col-xs-10">
        <div class = "box">
            <div class="box-header with-border">
                <span class = 'left-title'><?= Html::encode($this->title) ?></span>
                <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['check-template-index']), ['class' => 'right-cancel', 'data-pjax' => 0]) ?>
            </div>
            <div class = "box-body">

                <?=
                $this->render('_checkTemplateForm', [
                    'model' => $model,
                    'type' => $type,
                ])
                ?>
            </div>
        </div>
    </div>
<?php Pjax::end() ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<?php AppAsset::addScript($this, '@web/public/plugins/select2/select2.full.min.js') ?>
<?php AppAsset::addScript($this, '@web/public/plugins/select2/i18n/zh-CN.js') ?>
    <script type="text/javascript">
        var baseUrl = "<?= $baseUrl ?>";
        var indexUrl = "<?= Url::to(['@outpatientOutpatientCheckTemplateIndex']) ?>";
        var checkSearchUrl = '<?= Url::to(['@apiSearchPackageTemplateCheck']) ?>';
        require([baseUrl + "/public/js/outpatient/checkTemplate.js"], function (main) {
            main.init();
        });
    </script>
<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>