<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CaseTemplate */

$this->title = '新增医嘱模板分类';
$this->params['breadcrumbs'][] = ['label' => '医生门诊', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '医嘱模板分类', 'url' => ['recipe-type-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/recipeTemplate.css')?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'), ['type' => 2]) ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div class="case-template-create col-xs-10">
        <div class = "box">
            <div class="box-header with-border">
                <span class = 'left-title'><?= Html::encode($this->title) ?></span>
                <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['recipe-type-index']), ['class' => 'right-cancel','data-pjax' => 0]) ?>
            </div>
            <div class = "box-body">

                <?=
                $this->render('_recipeTypeForm', [
                    'model' => $model,
                    'hidden' => true
                ])
                ?>
            </div>
        </div>
    </div>
<?php Pjax::end() ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>