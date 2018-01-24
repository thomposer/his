<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use yii\helpers\Url;
use app\assets\AppAsset;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = '编辑员工';
$this->params['breadcrumbs'][] = ['label' => '人员管理', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/user/manage.css') ?>
<?php AppAsset::addCss($this, '@web/public/dist/css/cropper.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/upload.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/user/update.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<div class="user-create col-xs-12">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
<?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回',Yii::$app->request->referrer, ['class' => 'right-cancel right-cancel-confirmed']) ?>
        </div>
        <div class = "box-body">    

            <?=
            $this->render('_form', [
                'model' => $model,
                'spotInfo' => $spotInfo,
                'roleInfo' => $roleInfo,
                'clinicInfo' => $clinicInfo,
                'department' => $department
            ])
            ?>
        </div>
    </div>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<script type = "text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var uploadUrl = '<?= Url::to(['@manageSitesUpload']); ?>'
    var apiSecondDepartmentSelect = '<?= Url::to(['@apiSecondDepartmentSelect']) ?>';
    var onceDepartmentSelect = <?= Json::encode($spotInfo,JSON_ERROR_NONE) ?>;
    require(['<?= $baseUrl ?>' + '/public/js/user/form.js?v='+ '<?= $versionNumber ?>'], function (main) {
        main.init();
    })
</script>
<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>