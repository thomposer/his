<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use johnitvn\ajaxcrud\CrudAsset;
use rkit\yii2\plugins\ajaxform\Asset;
CrudAsset::register($this);
Asset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\UserAppointmentConfig */

$this->title = '编辑';
$this->params['breadcrumbs'][] = ['label' => '医生-服务-诊金关联配置', 'url' => ['user-appointment-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
	<?php AppAsset::addCss($this, '@web/public/css/spot_set/userAppointmentConfig.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="user-appointment-config-update col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',  yii\helpers\Url::to(['@spot_setUserAppointmentConfigIndex']),['class' => 'right-cancel']) ?>      
    </div>
        <div class = "box-body">
        
            <?= $this->render('_form', [
                'model' => $model,
                'spotTypeList' => $spotTypeList,
                'userTypeList' => $userTypeList,
                'userInfo' => $userInfo
                
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script>

    require(["<?= $baseUrl ?>"+"/public/js/spot_set/userAppointmentConfig.js"],function(main){
    	main.init();
	});
    </script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>