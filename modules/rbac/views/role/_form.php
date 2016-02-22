<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Role */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = \Yii::$app->request->baseUrl;
$session = Yii::$app->session;
$spot = $session->get('currentSpot')?$session->get('currentSpot'):$session->get('spot');

?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <style type="text/css" media="screen">
        .form-control{
	       width:600px;
          padding-left:none;
        };
        
    </style>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class = "col-xs-12">
    <div class = "box">
        <div class = "box-body">
<?php $form = ActiveForm::begin([
    'options' => ['class' => 'roleform'],
    'fieldConfig' => [
        'template' => "<div class=' text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0 errors'>{error}</div>",
    ]]);?> 
  
   <table class="table table-bordered table-hover definewidth m10" style="text-align:left">
        <tr>
            <td class="tableleft"><span class="need">*</span>角色名称</td>
            <td>
              <?= $form->field($model, 'description')->textInput(['placeholder'=>'中文描述'])->label(false) ?>
            </td>
        </tr>
         <tr>
            <td width="10%" class="tableleft"><span class="need">*</span>角色简称</td>
            <td>
              <?= $form->field($model, 'name')->textInput(['placeholder'=>'命名规范：必须英文'])->label(false) ?>
            </td>
        </tr>       
        <tr>
            <td class="tableleft">权限列表</td>
            <td>
                    
                <?php if($permission_child):?>
                <?php foreach ($permission_child as $key => $value):?>
                    
                <ul style="padding-left:42px;">
                    <li>
                        <label class='checkbox inline'>
                            <input type='checkbox' name='RoleForm[parentName][]' value='<?php echo $permission_parent[$key]->name;?>' /><?php echo $permission_parent[$key]->description.'('. $permission_parent[$key]->name.')';?>
                        </label>
                       
                        <ul style="padding-left: 25px;">
                         <?php foreach ($value as $v):?>
                            <li>
                                <label class='checkbox inline'>
                                    <input type='checkbox' name='RoleForm[child][]' <?php if($model->child){if(in_array($v->name,$model->child)){echo "checked";}} ?> value='<?php echo $v->name;?>' /><?php echo $v->description.'('.trim(str_replace($spot.'/', '', $v->name)).')';?>
                                </label>
                            </li>
                           
                        <?php endforeach;?>
                        </ul>
                        
                    </li>
                </ul>
                <?php endforeach;?> 
                <?php endif;?>
            </td>
        </tr>
        
        <tr>
            <td class="tableleft"></td>
            <td>
			    <div class="form-group">
			        <?= Html::submitButton($model->isNewRecord?'添加':'修改', ['class' => 'btn btn-success']) ?>
			        <?= Html::a('返回列表', ['@rbacRole'], ['class' => 'btn btn-primary'])?>
			    </div>
            </td>
        </tr>
    </table>

    <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>

    <script type="text/javascript">
    require(["<?php echo $baseUrl ?>"+"/public/js/rbac/role.js"],function(main){
        	main.init();
    	});
	</script>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>