<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\Schedule */

$this->title = '修改班次 ';
$this->params['breadcrumbs'][] = ['label' => '班次配置', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' =>  $model->shift_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改班次';
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/spot/schedule.css')?>
<?php AppAsset::addCss($this, '@web/public/plugins/timepicker/bootstrap-timepicker.min.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="schedule-update col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Url::to(['index']),['class' => 'right-cancel']) ?>      
    </div>
        <div class = "box-body">
        
            <?= $this->render('_form', [
                'model' => $model,

            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script type = "text/javascript">
        var baseUrl = '<?= $baseUrl ?>';
        //        var uploadUrl = '<?//= Url::to(['@manageSitesUpload']); ?>//'
        require(['<?= $baseUrl?>'+'/public/js/spot/schedule.js?v='+ '<?= $versionNumber ?>'],function(main){
            main.init();
        })
    </script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>