<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Item */
/* @var $form yii\widgets\ActiveForm */
$this->title = '权限管理';
$this->params['breadcrumbs'][] = ['label' => '角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/rbac/rbac.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="item-create col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['index'],['class' => 'right-cancel']) ?>      
    </div>
        <div class = "box-body">    
            <div class="item-form col-md-8">

                <?php $form = ActiveForm::begin(); ?>
                
                    <?= $form->field($model, 'child')->checkboxList(ArrayHelper::map($permissions, 'name', 'description'))->label(false) ?>
            
                    <div class="form-group">
                        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
                        <?= Html::a('取消',['index'],['class' => 'btn btn-cancel btn-form']) ?>
                    </div>
            
                <?php ActiveForm::end(); ?>
            
            </div>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>