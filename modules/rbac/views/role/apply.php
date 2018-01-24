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
                
                    <?php // $form->field($model, 'child')->checkboxList(ArrayHelper::map($permissions, 'name', 'description'))->label(false) ?>
                    <?php if($permissions):?>
                    <?php foreach ($permissions as $key => $value):?>
                        
                    <ul class="parent-node">
                        <li>
                            <label class='checkbox inline parent-node-title'>
                                <?= Html::input('checkbox','ItemForm[child][]',$permissions[$key]->name,['checked' => in_array($value->name,$model->child)?true:false]) ?>
                                <?= $permissions[$key]->description;?>
                            </label>
                            <?php if(isset($child[$key])):?>
                            <?php foreach ($child[$key] as $v):?>
                            <ul style="padding-left: 25px;" class="child-node">
                                <li>
                                    <label class='checkbox child-node-title'>
                                        <?= Html::input('checkbox','ItemForm[child][]',$v['name'],['checked' => in_array($v['name'],$model->child)?true:false]) ?>
                                        <?= $v['description'];?>
                                    </label>
                                    <?php foreach ($v['childData'] as $childValue):?>
                                        <ul style="padding-left: 25px;" class="grandson-node">
                                                <li>
                                                    <label class='checkbox'>
                                                        <?= Html::input('checkbox','ItemForm[child][]',$childValue['name'],['checked' => in_array($childValue['name'],$model->child)?true:false]) ?>
                                                        <?= $childValue['description'];?>
                                                    </label>
                                                </li>
                                        </ul>
                                    <?php endforeach;?>
                                </li>
                            </ul>
                            <?php endforeach;?>
                            <?php endif;?>
                        </li>
                    </ul>
                    <?php endforeach;?> 
                    <?php endif;?>
                    <div class="form-group">
                        <?= Html::a('取消',['index'],['class' => 'btn btn-cancel btn-form']) ?>
                        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
                    </div>
            
                <?php ActiveForm::end(); ?>
            
            </div>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script type="text/javascript">

    require([baseUrl+"/public/js/rbac/role.js"],function(main){
    	main.init();
	});
    </script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>