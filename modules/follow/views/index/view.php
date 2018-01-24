<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\follow\models\Follow */

$this->title = '编辑随访';
$this->params['breadcrumbs'][] = ['label' => '随访管理', 'url' => ['index']];
/* $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]]; */
$this->params['breadcrumbs'][] = '编辑随访';
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/dist/css/cropper.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/upload.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/user/manage.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/follow/selectFollow.css')?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<div class="follow-update col-xs-12">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
<?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', ['index'], ['class' => 'right-cancel second-cancel', 'data-pjax' => 0]) ?>      
        </div>
        <div class = "box-body">

            <?=
            $this->render('_form', [
                'model' => $model,
                'patientModel' => $patientModel,
                'triageInfo' => $triageInfo,
                'view' => 1,
                'followFile' =>$followFile,
            ])
            ?>
        </div>
    </div>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>