<?php

use yii\helpers\Html;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\NursingRecordTemplate */

$this->title = '编辑护理记录模板';
$this->params['breadcrumbs'][] = ['label' => '护理记录模板', 'url' => ['index']];
/* $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]]; */
$this->params['breadcrumbs'][] = '编辑记录模板';
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="nursing-record-template-update col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['index'],['class' => 'right-cancel']) ?>
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

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>