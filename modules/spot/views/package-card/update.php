<?php

use yii\helpers\Html;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CardManage */

$this->title = '修改套餐卡';
$this->params['breadcrumbs'][] = ['label' => '卡中心', 'url' => ['index']];
$this->params['breadcrumbs'][] = '套餐卡配置';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="package-card-update col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>      
    </div>
        <div class = "box-body">
        
            <?= $this->render('_form', [
                'model' => $model,
                'packageCardServiceList' => $packageCardServiceList,
                'packageServiceUnionList' => $packageServiceUnionList,
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>

<?php  AutoLayout::end()?>