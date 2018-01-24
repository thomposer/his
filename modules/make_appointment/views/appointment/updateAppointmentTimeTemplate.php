<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use yii\helpers\Url;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\AppointmentTimeTemplate */

$this->title = '编辑模板';
$this->params['breadcrumbs'][] = ['label' => '预约时间模板', 'url' => URL::to(['@appointmentTimeTemplate'])];
/* $this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]]; */
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/appointmentTimeTemplate.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
    <div class="appointment-time-template-update col-xs-12">
        <div class = "box">
            <div class="box-header with-border">
                <span class = 'left-title'><?= Html::encode($this->title) ?></span>
                <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['@appointmentTimeTemplate'],['class' => 'right-cancel second-cancel']) ?>
            </div>
            <div class = "box-body">
                <?= $this->render('_createAppointmentTimeTemplateForm', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script type="text/javascript">
        var baseUrl = '<?= $baseUrl ?>';
        var appointmentTimeTemplateUrl= '<?= URL::to(['@appointmentTimeTemplate']) ?>';
        var timeConfig = <?= json_encode($timeConfig,true)?>;
        require(["<?= $baseUrl ?>"+"/public/js/make_appointment/timeTemplate.js"],function(main){
            main.init();
        });
    </script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>