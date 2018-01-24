<?php

use yii\helpers\Html;
use app\common\AutoLayout;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\NursingRecordTemplate */

$this->title = '新增护理模板';
$this->params['breadcrumbs'][] = ['label' => '护理模板', 'url' => ['nursing-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>1]) ?>
<div class="nursing-record-template-create col-xs-10">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['nursing-index'],['class' => 'right-cancel','data-pjax' => 0]) ?>
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